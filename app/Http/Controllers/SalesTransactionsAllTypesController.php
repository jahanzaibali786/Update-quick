<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\InvoicePayment;
use App\Models\SalesReceipt;
use App\Models\Proposal;
use App\Models\CreditNote;
use App\Models\Customer;
use App\Models\DelayedCharges;
use App\Models\DelayedCredits;
use App\Models\RefundReceipt;
use App\Models\TimeActivity;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Carbon\Carbon;

class SalesTransactionsAllTypesController extends Controller
{
    /**
     * Display the sales transactions page with all transaction types.
     */
    public function index(Request $request)
    {
        if (!Auth::user()->can('manage transaction')) {
            return redirect()->back()->with('error', __('Permission denied.'));
        }

        $user = Auth::user();
        $companyId = $user->creatorId();
        
        // Get filter values
        $type = $request->get('type', 'all');
        $dateRange = $request->get('date_range', 'last_12_months');
        $customerId = $request->get('customer', '');
        $status = $request->get('status', 'all');

        // Calculate date range
        $dateFilters = $this->getDateRange($dateRange);
        $startDate = $dateFilters['start'];
        $endDate = $dateFilters['end'];

        // Get customers for dropdown
        $customers = Customer::where('created_by', $companyId)
            ->pluck('name', 'id')
            ->prepend(__('Search'), '');

        // Calculate summary metrics
        $salesData = $this->calculateSalesMetrics($companyId, $startDate, $endDate);

        // Get transactions
        $transactions = $this->getTransactions($companyId, $type, $startDate, $endDate, $customerId, $status);

        // Type options for filter
        $typeOptions = [
            'all' => __('All transactions'),
            'invoice' => __('Invoice'),
            'payment' => __('Payment'),
            'estimate' => __('Estimate'),
            'sales_receipt' => __('Sales Receipt'),
            'credit_memo' => __('Credit Memo'),
        ];

        // Date range options (matches QBO exactly)
        $dateRangeOptions = [
            'all' => __('All'),
            'custom' => __('Custom dates'),
            'today' => __('Today'),
            'yesterday' => __('Yesterday'),
            'this_week' => __('This week'),
            'last_week' => __('Last week'),
            'this_month' => __('This month'),
            'last_month' => __('Last month'),
            'last_30_days' => __('Last 30 days'),
            'this_quarter' => __('This quarter'),
            'last_quarter' => __('Last quarter'),
            'last_3_months' => __('Last 3 months'),
            'last_6_months' => __('Last 6 months'),
            'last_12_months' => __('Last 12 months'),
            'year_to_date' => __('Year to date'),
            'this_year' => __('This year'),
            'last_year' => (string)Carbon::now()->subYear()->year,
        ];

        // Status options
        $statusOptions = [
            'all' => __('All statuses'),
            'open' => __('Open'),
            'overdue' => __('Overdue'),
            'paid' => __('Paid'),
        ];

        return view('SalesTransactionsAllTypes.SalesTransactionsAllTypes', compact(
            'salesData',
            'transactions',
            'customers',
            'typeOptions',
            'dateRangeOptions',
            'statusOptions',
            'type',
            'dateRange',
            'customerId',
            'status'
        ));
    }

    /**
     * Get all transactions based on filters.
     */
    private function getTransactions($companyId, $type, $startDate, $endDate, $customerId, $status)
    {
        $transactions = collect();

        // Invoices
        if ($type === 'all' || $type === 'invoice') {
            // Debug: Log all invoices before filtering
            $allInvoices = Invoice::where('created_by', $companyId)->get();
            \Log::info('All Invoices (before date filter)', [
                'total' => $allInvoices->count(),
                'ids' => $allInvoices->pluck('id')->toArray(),
                'dates' => $allInvoices->pluck('issue_date', 'id')->toArray(),
                'date_range' => ['start' => $startDate, 'end' => $endDate],
            ]);

            $invoices = Invoice::where('created_by', $companyId)
                ->whereBetween('issue_date', [$startDate, $endDate])
                ->when($customerId, fn($q) => $q->where('customer_id', $customerId))
                ->when($status !== 'all', fn($q) => $this->applyInvoiceStatusFilter($q, $status))
                ->with('customer')
                ->get();

            \Log::info('Invoices after filters', [
                'count' => $invoices->count(),
                'ids' => $invoices->pluck('id')->toArray(),
            ]);

            foreach ($invoices as $inv) {
                $statusText = $this->getInvoiceStatusText($inv);
                $transactions->push([
                    'id' => $inv->id,
                    'date' => $inv->issue_date,
                    'type' => __('Invoice'),
                    'no' => Auth::user()->invoiceNumberFormat($inv->invoice_id),
                    'customer' => optional($inv->customer)->name ?? '-',
                    'memo' => $inv->memo ?? $inv->ref_number ?? '',
                    'amount' => $inv->total_amount ?? (method_exists($inv, 'getTotal') ? $inv->getTotal() : 0),
                    'status' => $statusText,
                    'view_url' => route('invoice.edit', Crypt::encrypt($inv->id)),
                    'edit_url' => route('invoice.edit', Crypt::encrypt($inv->id)),
                    'edit_payment_url' => route(
                        'receive-payment.payment',
                        ['invoice_id' => Crypt::encrypt($inv->id)]
                    ),
                    'delete_url' => route('invoice.destroy', Crypt::encrypt($inv->id)),
                    'activity_url' => route('sales.transaction.activity', ['type' => 'invoice', 'id' => $inv->id]),
                ]);
            }
        }

        // Payments (Invoice Payments)
        if ($type === 'all' || $type === 'payment') {
            $payments = InvoicePayment::whereHas('invoice', function($q) use ($companyId) {
                    $q->where('created_by', $companyId);
                })
                ->whereBetween('date', [$startDate, $endDate])
                ->when($customerId, function($q) use ($customerId) {
                    $q->whereHas('invoice', function($iq) use ($customerId) {
                        $iq->where('customer_id', $customerId);
                    });
                })
                ->with(['invoice.customer'])
                ->get();

            foreach ($payments as $pay) {
                $transactions->push([
                    'id' => $pay->id,
                    'date' => $pay->date,
                    'type' => __('Payment'),
                    'no' => '#' . $pay->id,
                    'customer' => optional(optional($pay->invoice)->customer)->name ?? '-',
                    'memo' => $pay->description ?? '',
                    'amount' => $pay->amount,
                    'status' => __('Closed'),
                    'view_url' => route('receive-payment.show', $pay->id),
                    'delete_url' => route('receive-payment.destroy', $pay->id),
                    'activity_url' => route('sales.transaction.activity', ['type' => 'payment', 'id' => $pay->id]),
                ]);
            }
        }

        // Estimates (Proposals)
        if ($type === 'all' || $type === 'estimate') {
            $proposals = Proposal::where('created_by', $companyId)
                ->whereBetween('issue_date', [$startDate, $endDate])
                ->when($customerId, fn($q) => $q->where('customer_id', $customerId))
                ->with('customer')
                ->get();

            foreach ($proposals as $prop) {
                $statusText = Proposal::$statues[$prop->status] ?? '-';
                $transactions->push([
                    'id' => $prop->id,
                    'date' => $prop->issue_date,
                    'type' => __('Estimate'),
                    'no' => '#' . $prop->proposal_id,
                    'customer' => optional($prop->customer)->name ?? '-',
                    'memo' => '',
                    'amount' => $prop->total_amount ?? (method_exists($prop, 'getTotal') ? $prop->getTotal() : 0),
                    'status' => __($statusText),
                    'view_url' => route('proposal.edit', Crypt::encrypt($prop->id)),
                    'delete_url' => route('proposal.destroy', Crypt::encrypt($prop->id)),
                    'activity_url' => route('sales.transaction.activity', ['type' => 'estimate', 'id' => $prop->id]),
                ]);
            }
        }

        // Sales Receipts
        if ($type === 'all' || $type === 'sales_receipt') {
            $salesReceipts = SalesReceipt::where('created_by', $companyId)
                ->whereBetween('issue_date', [$startDate, $endDate])
                ->when($customerId, fn($q) => $q->where('customer_id', $customerId))
                ->with('customer')
                ->get();

            foreach ($salesReceipts as $sr) {
                $statusText = SalesReceipt::$statues[$sr->status] ?? '-';
                $transactions->push([
                    'id' => $sr->id,
                    'date' => $sr->issue_date,
                    'type' => __('Sales Receipt'),
                    'no' => '#' . $sr->ref_number,
                    'customer' => optional($sr->customer)->name ?? '-',
                    'memo' => $sr->memo ?? '',
                    'amount' => $sr->total_amount ?? 0,
                    'status' => __($statusText),
                    'view_url' => route('sales-receipt.edit', $sr->id),
                    'delete_url' => route('sales-receipt.destroy', $sr->id),
                    'activity_url' => route('sales.transaction.activity', ['type' => 'sales_receipt', 'id' => $sr->id]),
                ]);
            }
        }

        // Refund Receipts (Refund Receipts)
        if ($type === 'all' || $type === 'refund') {
            $creditNotes = RefundReceipt::where('created_by', $companyId)
                ->whereBetween('issue_date', [$startDate, $endDate])
                ->when($customerId, fn($q) => $q->where('customer_id', $customerId))
                ->get();

            foreach ($creditNotes as $cn) {
                $transactions->push([
                    'id' => $cn->id,
                    'date' => $cn->issue_date,
                    'type' => __('Refund'),
                    'no' => '#' . ($cn->ref_number ?? $cn->refund_receipt_id),
                    'customer' => optional($cn->customer)->name ?? '-',
                    'memo' => $cn->memo ?? '',
                    'amount' => -$cn->total_amount,
                    'status' => __('Paid'),
                    'view_url' => route('refund-receipt.edit', $cn->id),
                    'delete_url' => route('refund-receipt.destroy', $cn->id),
                    'activity_url' => route('sales.transaction.activity', ['type' => 'refund', 'id' => $cn->id]),
                ]);
            }
        }
        // Credit Memos (Credit Notes)
        if ($type === 'all' || $type === 'credit_memo') {
            $creditNotes = CreditNote::where('created_by', $companyId)
                ->whereBetween('date', [$startDate, $endDate])
                ->when($customerId, fn($q) => $q->where('customer', $customerId))
                ->get();

            foreach ($creditNotes as $cn) {
                $transactions->push([
                    'id' => $cn->id,
                    'date' => $cn->date,
                    'type' => __('Credit Memo'),
                    'no' => '#' . ($cn->credit_note_id ?? $cn->id),
                    'customer' => optional($cn->customer_detail)->name ?? '-',
                    'memo' => $cn->description ?? '',
                    'amount' => -$cn->amount,
                    'status' => __('Unapplied'),
                    'view_url' => route('creditmemo.edit', $cn->id),
                    'delete_url' => route('creditmemo.destroy', $cn->id),
                    'activity_url' => route('sales.transaction.activity', ['type' => 'credit_memo', 'id' => $cn->id]),
                ]);
            }
        }
        if ($type === 'all' || $type === 'delayed_credits') {
            $creditNotes = DelayedCredits::where('created_by', $companyId)
                ->whereBetween('date', [$startDate, $endDate])
                ->when($customerId, fn($q) => $q->where('customer_id', $customerId))
                ->get();

            foreach ($creditNotes as $cn) {
                $transactions->push([
                    'id' => $cn->id,
                    'date' => $cn->date,
                    'type' => __('Delayed Credit'),
                    'no' => '#' . ($cn->credit_id ?? $cn->id),
                    'customer' => optional($cn->customer_detail)->name ?? '-',
                    'memo' => $cn->description ?? '',
                    'amount' => -$cn->total_amount,
                    'status' => __('Open'),
                    'view_url' => route('delayed-credit.edit', $cn->id),
                    'delete_url' => route('delayed-credit.destroy', $cn->id),
                    'activity_url' => route('sales.transaction.activity', ['type' => 'delayed_credit', 'id' => $cn->id]),
                ]);
            }
        }
        if ($type === 'all' || $type === 'delayed_charges') {
            $creditNotes = DelayedCharges::where('created_by', $companyId)
                ->whereBetween('date', [$startDate, $endDate])
                ->when($customerId, fn($q) => $q->where('customer_id', $customerId))
                ->get();

            foreach ($creditNotes as $cn) {
                $transactions->push([
                    'id' => $cn->id,
                    'date' => $cn->date,
                    'type' => __('Delayed Charge'),
                    'no' => '#' . ($cn->credit_id ?? $cn->id),
                    'customer' => optional($cn->customer_detail)->name ?? '-',
                    'memo' => $cn->description ?? '',
                    'amount' => -$cn->total_amount,
                    'status' => __('Open'),
                   'view_url' => route('delayed-charge.edit', $cn->id),
                    'delete_url' => route('delayed-charge.destroy', $cn->id),
                    'activity_url' => route('sales.transaction.activity', ['type' => 'delayed_charge', 'id' => $cn->id]),
                ]);
            }
        }
        if ($type === 'all' || $type === 'time_charges') {
            $creditNotes = TimeActivity::where('created_by', $companyId)
                ->whereBetween('date', [$startDate, $endDate])
                ->when($customerId, fn($q) => $q->where('customer_id', $customerId))
                ->get();

            foreach ($creditNotes as $cn) {
                $transactions->push([
                    'id' => $cn->id,
                    'date' => $cn->date,
                    'type' => __('Time Charge'),
                    'no' => '#' . ($cn->id ?? $cn->id),
                    'customer' => optional($cn->customer)->name ?? '-',
                    'memo' => $cn->notes ?? '',
                    'amount' => $cn->total_amount,
                    'status' => __('Open'),
                    'view_url' => route('timeActivity.edit', $cn->id),
                    'delete_url' => '',
                    'activity_url' => route('sales.transaction.activity', ['type' => 'time_activity', 'id' => $cn->id]),
                ]);
            }
        }

        // Debug logging to track transaction counts
        \Log::info('Sales Transactions Debug', [
            'type_filter' => $type,
            'total_count' => $transactions->count(),
            'by_type' => $transactions->groupBy('type')->map->count()->toArray(),
        ]);

        // Sort by date descending
        return $transactions->sortByDesc('date')->values()->toArray();
    }

    /**
     * Calculate date range based on the selected filter.
     */
    private function getDateRange($dateRange)
    {
        $now = Carbon::now();
        
        switch ($dateRange) {
            case 'all':
                return [
                    'start' => '1970-01-01',
                    'end' => $now->copy()->addYear()->toDateString(),
                ];
            case 'custom':
                // For custom dates, we'll use a wide range (can be enhanced with date pickers)
                return [
                    'start' => $now->copy()->subYear()->toDateString(),
                    'end' => $now->copy()->toDateString(),
                ];
            case 'today':
                return [
                    'start' => $now->copy()->toDateString(),
                    'end' => $now->copy()->toDateString(),
                ];
            case 'yesterday':
                return [
                    'start' => $now->copy()->subDay()->toDateString(),
                    'end' => $now->copy()->subDay()->toDateString(),
                ];
            case 'this_week':
                return [
                    'start' => $now->copy()->startOfWeek()->toDateString(),
                    'end' => $now->copy()->endOfWeek()->toDateString(),
                ];
            case 'last_week':
                return [
                    'start' => $now->copy()->subWeek()->startOfWeek()->toDateString(),
                    'end' => $now->copy()->subWeek()->endOfWeek()->toDateString(),
                ];
            case 'this_month':
                return [
                    'start' => $now->copy()->startOfMonth()->toDateString(),
                    'end' => $now->copy()->endOfMonth()->toDateString(),
                ];
            case 'last_month':
                return [
                    'start' => $now->copy()->subMonth()->startOfMonth()->toDateString(),
                    'end' => $now->copy()->subMonth()->endOfMonth()->toDateString(),
                ];
            case 'last_30_days':
                return [
                    'start' => $now->copy()->subDays(30)->toDateString(),
                    'end' => $now->copy()->toDateString(),
                ];
            case 'this_quarter':
                return [
                    'start' => $now->copy()->firstOfQuarter()->toDateString(),
                    'end' => $now->copy()->lastOfQuarter()->toDateString(),
                ];
            case 'last_quarter':
                return [
                    'start' => $now->copy()->subQuarter()->firstOfQuarter()->toDateString(),
                    'end' => $now->copy()->subQuarter()->lastOfQuarter()->toDateString(),
                ];
            case 'last_3_months':
                return [
                    'start' => $now->copy()->subMonths(3)->toDateString(),
                    'end' => $now->copy()->toDateString(),
                ];
            case 'last_6_months':
                return [
                    'start' => $now->copy()->subMonths(6)->toDateString(),
                    'end' => $now->copy()->toDateString(),
                ];
            case 'year_to_date':
                return [
                    'start' => $now->copy()->startOfYear()->toDateString(),
                    'end' => $now->copy()->toDateString(),
                ];
            case 'this_year':
                return [
                    'start' => $now->copy()->startOfYear()->toDateString(),
                    'end' => $now->copy()->endOfYear()->toDateString(),
                ];
            case 'last_year':
                return [
                    'start' => $now->copy()->subYear()->startOfYear()->toDateString(),
                    'end' => $now->copy()->subYear()->endOfYear()->toDateString(),
                ];
            case 'last_12_months':
            default:
                return [
                    'start' => $now->copy()->subMonths(12)->toDateString(),
                    'end' => $now->copy()->toDateString(),
                ];
        }
    }

    /**
     * Calculate sales metrics for the summary bar.
     */
    private function calculateSalesMetrics($companyId, $startDate, $endDate)
    {
        // Estimates (Proposals)
        $estimatesQuery = Proposal::where('created_by', $companyId)
            ->whereBetween('issue_date', [$startDate, $endDate]);
        $estimatesCount = $estimatesQuery->count();
        $estimatesAmount = $estimatesQuery->sum('total_amount') ?? 0;

        // Unbilled income - Invoices in draft status
        $unbilledAmount = Invoice::where('created_by', $companyId)
            ->where('status', 0) // Draft
            ->whereBetween('issue_date', [$startDate, $endDate])
            ->sum('total_amount') ?? 0;

        // Overdue invoices
        $overdueQuery = Invoice::where('created_by', $companyId)
            ->whereIn('status', [1, 2, 3]) // Sent, Unpaid, Partially Paid
            ->where('due_date', '<', Carbon::now()->toDateString())
            ->whereBetween('issue_date', [$startDate, $endDate]);
        $overdueCount = $overdueQuery->count();
        $overdueAmount = $overdueQuery->sum('total_amount') ?? 0;

        // Open invoices and credits
        $openQuery = Invoice::where('created_by', $companyId)
            ->whereIn('status', [1, 2, 3]) // Sent, Unpaid, Partially Paid
            ->whereBetween('issue_date', [$startDate, $endDate]);
        $openCount = $openQuery->count();
        $openAmount = $openQuery->sum('total_amount') ?? 0;

        // Recently paid (last 30 days)
        $paidQuery = Invoice::where('created_by', $companyId)
            ->where('status', 4) // Paid
            ->whereBetween('issue_date', [Carbon::now()->subDays(30)->toDateString(), Carbon::now()->toDateString()]);
        $paidCount = $paidQuery->count();
        $paidAmount = $paidQuery->sum('total_amount') ?? 0;

        return [
            'estimates' => [
                'count' => $estimatesCount,
                'amount' => $estimatesAmount,
            ],
            'unbilled' => [
                'amount' => $unbilledAmount,
            ],
            'overdue' => [
                'count' => $overdueCount,
                'amount' => $overdueAmount,
            ],
            'open' => [
                'count' => $openCount,
                'amount' => $openAmount,
            ],
            'paid' => [
                'count' => $paidCount,
                'amount' => $paidAmount,
            ],
        ];
    }

    /**
     * Get invoice status text based on due date and payment status.
     */
    private function getInvoiceStatusText($invoice)
    {
        if ($invoice->status == 4) {
            return __('Paid');
        }

        if ($invoice->status == 0) {
            return __('Draft');
        }

        $dueDate = Carbon::parse($invoice->due_date);
        $today = Carbon::now()->startOfDay();
        $diff = $today->diffInDays($dueDate, false);

        if ($diff < 0) {
            return __('Overdue :d days', ['d' => abs($diff)]);
        } elseif ($diff == 0) {
            return __('Due today');
        } else {
            return __('Due in :d days', ['d' => $diff]);
        }
    }

    /**
     * Apply status filter to invoice query.
     */
    private function applyInvoiceStatusFilter($query, $status)
    {
        switch ($status) {
            case 'open':
                return $query->whereIn('status', [1, 2, 3]);
            case 'overdue':
                return $query->whereIn('status', [1, 2, 3])
                    ->where('due_date', '<', Carbon::now()->toDateString());
            case 'paid':
                return $query->where('status', 4);
            default:
                return $query;
        }
    }

public function viewActivity(Request $request, $type, $id)
    {
        if (!Auth::check()) {
            abort(403);
        }

        try {

        $user = Auth::user();
        $companyId = $user->creatorId();

        $title = $subtitle = $edit_url = '';
        $total = 0;
        $issue_date = $due_date = null;
        $customer = null;
        $products = collect();
        $activities = [];

        switch ($type) {
            case 'invoice':
                $invoice = Invoice::where('created_by', $companyId)->where('id', $id)->first();
                if (!$invoice) abort(404);
                $customer = Customer::find($invoice->customer_id);
                $latestPayment = \DB::table('invoice_payments')->where('invoice_id', $id)->orderByDesc('date')->first();
                $products = \DB::table('invoice_products')
                    ->leftJoin('product_services', 'invoice_products.product_id', '=', 'product_services.id')
                    ->where('invoice_products.invoice_id', $id)
                    ->select('product_services.name as product_name', 'invoice_products.quantity', 'invoice_products.price', 'invoice_products.amount', 'invoice_products.description')
                    ->get();
                $title    = __('Invoice') . ' ' . $user->invoiceNumberFormat($invoice->invoice_id);
                $subtitle = $this->getInvoiceStatusText($invoice) . (!$invoice->send_date ? ' (' . __('Not sent') . ')' : '');
                $total      = $invoice->total_amount;
                $issue_date = $invoice->issue_date;
                $due_date   = $invoice->due_date;
                $edit_url   = route('invoice.edit', Crypt::encrypt($invoice->id));
                $activities = [
                    ['label' => __('Opened'), 'date' => $invoice->created_at->format('n/j/Y'), 'done' => true],
                    ['label' => __('Sent'),   'date' => $invoice->send_date ? \Carbon\Carbon::parse($invoice->send_date)->format('n/j/Y') : null, 'done' => !empty($invoice->send_date)],
                    ['label' => __('Viewed'), 'date' => null, 'done' => false],
                    ['label' => __('Paid'),   'date' => ($latestPayment && $invoice->status == 4) ? \Carbon\Carbon::parse($latestPayment->date)->format('n/j/Y') : null, 'done' => $invoice->status == 4],
                    ['label' => __('Deposited'), 'date' => null, 'done' => false],
                ];
                break;

            case 'estimate':
                $proposal = Proposal::where('created_by', $companyId)->where('id', $id)->first();
                if (!$proposal) abort(404);
                $customer   = Customer::find($proposal->customer_id);
                $statusMap  = Proposal::$statues ?? [];
                $title      = __('Estimate') . ' #' . $proposal->proposal_id;
                $subtitle   = $statusMap[$proposal->status] ?? '';
                $total      = $proposal->total_amount;
                $issue_date = $proposal->issue_date;
                $edit_url   = route('proposal.edit', Crypt::encrypt($proposal->id));
                $activities = [
                    ['label' => __('Created'),   'date' => $proposal->created_at->format('n/j/Y'), 'done' => true],
                    ['label' => __('Sent'),      'date' => $proposal->send_date ? \Carbon\Carbon::parse($proposal->send_date)->format('n/j/Y') : null, 'done' => !empty($proposal->send_date)],
                    ['label' => __('Accepted'),  'date' => $proposal->accepted_date ? \Carbon\Carbon::parse($proposal->accepted_date)->format('n/j/Y') : null, 'done' => $proposal->status == 1],
                    ['label' => __('Converted'), 'date' => null, 'done' => $proposal->is_convert == 1],
                ];
                break;

            case 'payment':
                $payment = \App\Models\InvoicePayment::find($id);
                if (!$payment) abort(404);
                $parentInvoice = Invoice::where('created_by', $companyId)->where('id', $payment->invoice_id)->first();
                if (!$parentInvoice) abort(404);
                $customer   = Customer::find($parentInvoice->customer_id);
                $title      = __('Payment') . ' #' . $payment->id;
                $subtitle   = __('Closed');
                $total      = $payment->amount;
                $issue_date = $payment->date;
                $edit_url   = route('receive-payment.show', $payment->id);
                $activities = [
                    ['label' => __('Created'), 'date' => $payment->created_at->format('n/j/Y'), 'done' => true],
                    ['label' => __('Applied'), 'date' => $payment->created_at->format('n/j/Y'), 'done' => true],
                ];
                break;

            case 'sales_receipt':
                $sr = SalesReceipt::where('created_by', $companyId)->where('id', $id)->first();
                if (!$sr) abort(404);
                $statusMap  = SalesReceipt::$statues ?? [];
                $customer   = Customer::find($sr->customer_id);
                $title      = __('Sales Receipt') . ' #' . $sr->ref_number;
                $subtitle   = $statusMap[$sr->status] ?? '';
                $total      = $sr->total_amount;
                $issue_date = $sr->issue_date;
                $edit_url   = route('sales-receipt.edit', $sr->id);
                $activities = [
                    ['label' => __('Created'), 'date' => $sr->created_at->format('n/j/Y'), 'done' => true],
                    ['label' => __('Sent'),    'date' => !empty($sr->send_date) ? \Carbon\Carbon::parse($sr->send_date)->format('n/j/Y') : null, 'done' => !empty($sr->send_date)],
                ];
                break;

            case 'credit_memo':
                $cm = CreditNote::where('created_by', $companyId)->where('id', $id)->first();
                if (!$cm) abort(404);
                $customer   = Customer::find($cm->customer);
                $title      = __('Credit Memo') . ' #' . ($cm->credit_note_id ?? $cm->id);
                $subtitle   = __('Unapplied');
                $total      = $cm->total_amount;
                $issue_date = $cm->date;
                $edit_url   = route('creditmemo.edit', $cm->id);
                $activities = [
                    ['label' => __('Created'), 'date' => $cm->created_at->format('n/j/Y'), 'done' => true],
                ];
                break;

            case 'refund':
                $rr = RefundReceipt::where('created_by', $companyId)->where('id', $id)->first();
                if (!$rr) abort(404);
                $customer   = Customer::find($rr->customer_id);
                $title      = __('Refund Receipt') . ' #' . ($rr->ref_number ?? $rr->refund_receipt_id);
                $subtitle   = __('Paid');
                $total      = $rr->total_amount;
                $issue_date = $rr->issue_date;
                $edit_url   = route('refund-receipt.edit', $rr->id);
                $activities = [
                    ['label' => __('Created'), 'date' => $rr->created_at->format('n/j/Y'), 'done' => true],
                ];
                break;

            case 'delayed_credit':
                $dc = DelayedCredits::where('created_by', $companyId)->where('id', $id)->first();
                if (!$dc) abort(404);
                $customer   = Customer::find($dc->customer_id);
                $title      = __('Delayed Credit') . ' #' . ($dc->credit_id ?? $dc->id);
                $subtitle   = __('Open');
                $total      = $dc->total_amount;
                $issue_date = $dc->date;
                $edit_url   = route('delayed-credit.edit', $dc->id);
                $activities = [
                    ['label' => __('Created'), 'date' => $dc->created_at->format('n/j/Y'), 'done' => true],
                ];
                break;

            case 'delayed_charge':
                $dch = DelayedCharges::where('created_by', $companyId)->where('id', $id)->first();
                if (!$dch) abort(404);
                $customer   = Customer::find($dch->customer_id);
                $title      = __('Delayed Charge') . ' #' . ($dch->charge_id ?? $dch->id);
                $subtitle   = __('Open');
                $total      = $dch->total_amount;
                $issue_date = $dch->date;
                $edit_url   = route('delayed-charge.edit', $dch->id);
                $activities = [
                    ['label' => __('Created'), 'date' => $dch->created_at->format('n/j/Y'), 'done' => true],
                ];
                break;

            case 'time_activity':
                $ta = TimeActivity::where('created_by', $companyId)->where('id', $id)->first();
                if (!$ta) abort(404);
                $customer   = Customer::find($ta->customer_id);
                $title      = __('Time Activity') . ' #' . $ta->id;
                $subtitle   = __('Open');
                $total      = $ta->total_amount;
                $issue_date = $ta->date;
                $edit_url   = route('timeActivity.edit', $ta->id);
                $activities = [
                    ['label' => __('Created'), 'date' => $ta->created_at->format('n/j/Y'), 'done' => true],
                ];
                break;

            default:
                abort(404);
        }

       return view('SalesTransactionsAllTypes.partials.activity-panel', compact(
            'title', 'subtitle', 'total', 'issue_date', 'due_date',
            'customer', 'edit_url', 'products', 'activities'
        ));

        } catch (\Exception $e) {
            \Log::error('viewActivity error: ' . $e->getMessage());
            return response('<div style="padding:20px;color:red;">Error: ' . e($e->getMessage()) . '</div>', 500);
        }
    }

}
