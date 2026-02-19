@extends('layouts.admin')
@section('page-title')
    {{ __('Credit Memo') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item"><a href="{{ route('sales.transactions.index') }}">{{ __('Credit Memo') }}</a></li>
    <li class="breadcrumb-item">{{ __('Edit Credit Memo') }}</li>
@endsection

@push('css-page')
    <style>
        :root {
            --qbo-green: #2ca01c;
            --qbo-green-hover: #108000;
            --qbo-gray-text: #6b6c72;
            --qbo-border-color: #dcdcdc;
            --qbo-bg-color: #f4f5f8;
        }

        body {
            background-color: var(--qbo-bg-color);
            color: #393a3d;
            font-family: 'Avenir Next forINTUIT', 'Avenir Next', Futura, sans-serif;
        }

        .invoice-container {
            background: var(--qbo-bg-color);
            max-width: 100%;
            margin: 0;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Fixed Top Header */
        .fixed-top-header {
            position: sticky;
            top: 0;
            background: #fff;
            border-bottom: 1px solid #f4f5f8;
            z-index: 1000;
            padding: 0;
        }

        .header-top-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: #f4f5f8 !important;
            padding: 15px 30px;
        }

        .invoice-label {
            font-size: 24px;
            font-weight: 600;
            color: #393a3d;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .invoice-label svg {
            color: #393a3d;
        }

        .close-button {
            background: none;
            border: none;
            font-size: 24px;
            color: var(--qbo-gray-text);
            cursor: pointer;
            padding: 4px;
            line-height: 1;
        }

        /* Main Content Area */
        .main-content {
            padding: 20px 30px;
            flex: 1;
            background-color: #f5f5f5;
        }

        /* Top Customer Bar */
        .top-customer-bar {
            margin-bottom: 20px;
        }

        .amount-display {
            text-align: right;
        }

        .amount-label {
            font-size: 12px;
            color: var(--qbo-gray-text);
            text-transform: uppercase;
            font-weight: 600;
        }

        .amount-value {
            font-size: 36px;
            font-weight: 700;
            color: #393a3d;
        }

        /* Addresses Section */
        .form-grid-4 {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 15px;
        }

        /* Form Controls */
        .form-label {
            font-size: 13px;
            color: var(--qbo-gray-text);
            margin-bottom: 4px;
            font-weight: 400;
        }

        .form-control,
        .form-select {
            border: 1px solid #8d9096;
            border-radius: 2px;
            padding: 8px 10px;
            font-size: 14px;
            color: #393a3d;
            height: 36px;
        }

        /* Product Table */
        .product-section {
            padding: 24px 32px;
            background: #fff;
        }

        .invoice-card .product-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            border-top: 1px solid #e4e4e7;
            border-bottom: 1px solid #e4e4e7;
        }

        .invoice-card .product-table thead th {
            padding: 12px 8px;
            font-size: 13px;
            font-weight: 600;
            color: #393a3d;
            background: #fff;
            border-bottom: 1px solid #e4e4e7;
        }

        .invoice-card .product-table tbody td {
            padding: 12px 8px;
            font-size: 13px;
            vertical-align: middle;
            border-bottom: 1px solid #e4e4e7;
        }

        .invoice-card .product-table thead th+th,
        .invoice-card .product-table tbody td+td {
            border-left: 1px dotted #e4e4e7;
        }

        .invoice-card .product-table thead th:first-child,
        .invoice-card .product-table tbody td:first-child {
            border-left: none;
        }

        .delete-icon {
            opacity: 1;
            cursor: pointer;
            color: var(--qbo-gray-text);
            transition: opacity 0.2s;
        }

        .drag-handle {
            cursor: grab;
            color: #c4c4c4;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Totals Section */
        .totals-section {
            display: flex;
            flex-direction: column;
            gap: 8px;
            padding-top: 24px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 4px 0;
            font-size: 14px;
        }

        .total-row.final {
            font-size: 16px;
            font-weight: 600;
            color: #393a3d;
            padding-top: 12px;
            border-top: 2px solid #e4e4e7;
        }

        .discount-position-btn {
            border: none;
            background: #ffffff;
            padding: 4px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            color: #6b6c72;
        }

        .btn-qbo-primary {
            background-color: var(--qbo-green);
            color: #fff;
            border: 1px solid var(--qbo-green);
            padding: 8px 24px;
            border-radius: 18px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
        }

        /* Attachment Section Styles */
        .attachment-zone {
            border: 2px dashed #c4c4c4;
            border-radius: 4px;
            padding: 32px;
            text-align: center;
            background: #fafafa;
            cursor: pointer;
        }

        .attachment-row {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 10px;
            border: 1px solid #dcdcdc;
            border-radius: 4px;
            background: #fff;
            margin-bottom: 6px;
        }

        .invoice-footer {
            background: #f7f8fa;
            padding: 16px 32px;
            border-top: 1px solid #e4e4e7;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: auto;
            position: sticky;
            bottom: 0;
            z-index: 100;
        }
    </style>
@endpush

@push('script-page')
    <script src="{{ asset('js/jquery-ui.min.js') }}"></script>
    <script src="{{ asset('js/jquery.repeater.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            var invoiceModal = new bootstrap.Modal(document.getElementById('invoice-modal'), {
                backdrop: 'static',
                keyboard: false
            });
            invoiceModal.show();

            // Initial calculation on page load for edit mode
            recalcTotals();
        });

        function renumberInvoiceLines() {
            $('#sortable-table').find('tbody').each(function(index) {
                $(this).find('.line-number').text(index + 1);
            });
        }
    </script>
    <script>
        var selector = "body";
        if ($(selector + " .repeater").length) {
            var $dragAndDrop = $("body .repeater [data-repeater-list]").sortable({
                handle: '.sort-handler',
                items: 'tbody',
                axis: 'y',
                stop: function() {
                    renumberInvoiceLines();
                }
            });

            var $repeater = $(selector + ' .repeater').repeater({
                initEmpty: false,
                defaultValues: {
                    'status': 1
                },
                show: function() {
                    $(this).slideDown();
                    renumberInvoiceLines();
                    recalcTotals();
                },
                hide: function(deleteElement) {
                    if (confirm('Are you sure you want to delete this element?')) {
                        $(this).slideUp(deleteElement);
                        $(this).remove();
                        renumberInvoiceLines();
                        recalcTotals();
                    }
                },
                ready: function(setIndexes) {
                    $dragAndDrop.on('sortstop', function() {
                        setIndexes();
                        renumberInvoiceLines();
                    });
                }
            });

            // Populate repeater with existing items
            var existingItems = @json($creditMemo->items);
            if (existingItems && existingItems.length > 0) {
                var repeaterItems = existingItems.map(function(item) {
                    return {
                        'item': item.product_id,
                        'description': item.description,
                        'quantity': item.quantity,
                        'price': item.price,
                        'amount': (item.quantity * item.price).toFixed(2), // placeholder, updated by recalc
                        'tax_checkbox': item.tax != 0 ? true : false,
                        'tax': item.tax,
                        'itemTaxRate': item.tax_rate ?? 0, // Need to make sure this is passed
                        'discount': item.discount
                    };
                });
                // $repeater.setList(repeaterItems); // Wait, jquery.repeater setList might not work as expected for complex structures
            }
        }

        $(document).on('change', '#customer', function() {
            var id = $(this).val();
            var url = $(this).data('url');
            if (!id) return;

            $.ajax({
                url: url,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': jQuery('#token').val()
                },
                data: {
                    'id': id
                },
                success: function(data) {
                    if (typeof data === 'object' && data.customer) {
                        $('#customer_email').val(data.customer.email);
                        var addr = (data.customer.billing_name || '') + '\n' + (data.customer
                            .billing_address || '');
                        $('textarea[name="bill_to"]').val(addr.trim());
                    }
                }
            });
        });

        $(document).on('change', '.item', function() {
            var iteams_id = $(this).val();
            var url = $(this).data('url');
            var el = $(this);
            if (!iteams_id) return;

            $.ajax({
                url: url,
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': jQuery('#token').val()
                },
                data: {
                    'product_id': iteams_id
                },
                success: function(data) {
                    var item = JSON.parse(data);
                    var row = el.closest('tr');
                    row.find('.quantity').val(1);
                    row.find('.price').val(item.product.sale_price);
                    row.find('.pro_description').val(item.product.description);

                    var totalItemTaxRate = 0;
                    if (item.taxes != 0) {
                        item.taxes.forEach(t => totalItemTaxRate += parseFloat(t.rate));
                    }
                    row.find('.itemTaxRate').val(totalItemTaxRate.toFixed(2));
                    recalcTotals();
                }
            });
        });

        $(document).on('keyup change', '.quantity, .price, .discount', recalcTotals);
        $(document).on('change', '.product-row .form-check-input[type="checkbox"]', recalcTotals);
        $(document).on('change', 'select[name="sales_tax_rate"]', recalcTotals);

        function recalcTotals() {
            var grandSubtotal = 0;
            var taxableSubtotal = 0;

            $('.product-row').each(function() {
                var qty = parseFloat($(this).find('.quantity').val()) || 0;
                var rate = parseFloat($(this).find('.price').val()) || 0;
                var discount = parseFloat($(this).find('.discount').val()) || 0;
                var lineAmount = (qty * rate) - discount;

                $(this).find('.amount').val(lineAmount.toFixed(2));
                grandSubtotal += lineAmount;

                if ($(this).find('.form-check-input').prop('checked')) {
                    taxableSubtotal += lineAmount;
                }
            });

            var $selectedTax = $('select[name="sales_tax_rate"]').find(':selected');
            var taxRate = parseFloat($selectedTax.data('rate')) || 0;
            var totalTax = taxableSubtotal * taxRate / 100;

            var discType = $('.discount-type-select').val();
            var discVal = parseFloat($('.discount-input').val()) || 0;
            var totalDiscount = (discType === 'percent') ? (grandSubtotal * discVal / 100) : discVal;

            var grandTotal = grandSubtotal - totalDiscount + totalTax;

            $('.subTotal').text(grandSubtotal.toFixed(2));
            $('.taxableSubtotal').text(taxableSubtotal.toFixed(2));
            $('.totalDiscount').text(totalDiscount.toFixed(2));
            $('.totalTax').text(totalTax.toFixed(2));
            $('.totalAmount, .amountReceived').text(grandTotal.toFixed(2));

            $('input[name="subtotal"]').val(grandSubtotal.toFixed(2));
            $('input[name="taxable_subtotal"]').val(taxableSubtotal.toFixed(2));
            $('input[name="total_discount"]').val(totalDiscount.toFixed(2));
            $('input[name="total_tax"], input[name="sales_tax_amount"]').val(totalTax.toFixed(2));
            $('input[name="total_amount"]').val(grandTotal.toFixed(2));
        }
    </script>
@endpush

@section('content')
    <div class="modal fade" id="invoice-modal" tabindex="-1" aria-labelledby="invoiceModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content">
                <div class="invoice-container">
                    {{ Form::model($creditMemo, ['route' => ['creditmemo.update', $creditMemo->id], 'method' => 'POST', 'id' => 'creditmemo-form', 'enctype' => 'multipart/form-data']) }}
                    <input type="hidden" name="_token" id="token" value="{{ csrf_token() }}">

                    {{-- Fixed Top Header --}}
                    <div class="fixed-top-header">
                        <div class="header-top-row">
                            <div class="invoice-label">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    color="currentColor" width="24" height="24">
                                    <path fill="currentColor"
                                        d="M13.007 7a1 1 0 0 0-1 1L12 12a1 1 0 0 0 1 1l3.556.006a1 1 0 0 0 0-2L14 11l.005-3a1 1 0 0 0-.998-1">
                                    </path>
                                    <path fill="currentColor"
                                        d="M19.374 5.647A8.94 8.94 0 0 0 13.014 3H13a8.98 8.98 0 0 0-8.98 8.593l-.312-.312a1 1 0 0 0-1.416 1.412l2 2a1 1 0 0 0 1.414 0l2-2a1 1 0 0 0-1.412-1.416l-.272.272A6.984 6.984 0 0 1 13 5h.012A7 7 0 0 1 13 19h-.012a7 7 0 0 1-4.643-1.775 1 1 0 1 0-1.33 1.494A9 9 0 0 0 12.986 21H13a9 9 0 0 0 6.374-15.353">
                                    </path>
                                </svg>
                                {{ __('Credit Memo') }}
                                #{{ Auth::user()->invoiceNumberFormat($creditMemo->credit_note_id) }}
                            </div>
                            <div class="header-actions">
                                <button type="button" class="close-button"
                                    onclick="location.href = '{{ route('sales.transactions.index') }}';">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                        stroke-linecap="round" stroke-linejoin="round">
                                        <line x1="18" y1="6" x2="6" y2="18"></line>
                                        <line x1="6" y1="6" x2="18" y2="18"></line>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="main-content">
                        {{-- Top Customer Bar --}}
                        <div class="top-customer-bar row">
                            <div class="customer-select-group col-3">
                                <label class="form-label">{{ __('Customer') }}</label>
                                {{ Form::select('customer_id', $customers, null, ['class' => 'form-select', 'id' => 'customer', 'data-url' => route('invoice.customer'), 'required' => 'required']) }}
                            </div>
                            <div class="email-group col-3">
                                <label class="form-label">{{ __('Email') }}</label>
                                {{ Form::text('customer_email', null, ['class' => 'form-control', 'id' => 'customer_email']) }}
                            </div>
                            <div class="col-4"></div>
                            <div class="amount-display col-2">
                                <div class="amount-label">{{ __('AMOUNT') }}</div>
                                <div class="amount-value totalAmount">
                                    {{ Auth::user()->priceFormat($creditMemo->total_amount) }}</div>
                            </div>
                        </div>

                        {{-- Address, Date, Location --}}
                        <div class="row pt-4 pb-4">
                            <div class="col-md-2">
                                <label class="form-label">{{ __('Billing Address') }}</label>
                                {{ Form::textarea('bill_to', null, ['class' => 'form-control', 'rows' => 3]) }}
                            </div>
                            <div class="col-md-2">
                                <label class="form-label">{{ __('Credit Memo Date') }}</label>
                                {{ Form::date('issue_date', null, ['class' => 'form-control']) }}
                            </div>
                            <div class="col-md-5"></div>
                            <div class="col-md-3">
                                <label class="form-label">{{ __('Location of Sale') }}</label>
                                {{ Form::text('location_of_sale', null, ['class' => 'form-control']) }}
                            </div>
                        </div>

                        {{-- Product Section --}}
                        <div class="invoice-card">
                            <div class="product-section repeater">
                                <table class="product-table" id="sortable-table" data-repeater-list="items">
                                    <thead>
                                        <tr>
                                            <th></th>
                                            <th>#</th>
                                            <th>{{ __('Product/service') }}</th>
                                            <th>{{ __('Description') }}</th>
                                            <th>{{ __('Qty') }}</th>
                                            <th>{{ __('Rate') }}</th>
                                            <th>{{ __('Amount') }}</th>
                                            <th>{{ __('Tax') }}</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($creditMemo->items as $item)
                                            <tr class="product-row" data-repeater-item>
                                                <td>
                                                    <div class="drag-handle sort-handler"><i class="ti ti-grid-dots"></i>
                                                    </div>
                                                </td>
                                                <td><span class="line-number">{{ $loop->iteration }}</span></td>
                                                <td>{{ Form::select('item', $product_services, $item->product_id, ['class' => 'form-select item', 'data-url' => route('invoice.product')]) }}
                                                </td>
                                                <td>{{ Form::textarea('description', $item->description, ['class' => 'form-control pro_description', 'rows' => 1]) }}
                                                </td>
                                                <td>{{ Form::text('quantity', $item->quantity, ['class' => 'form-control quantity']) }}
                                                </td>
                                                <td>{{ Form::text('price', $item->price, ['class' => 'form-control price']) }}
                                                </td>
                                                <td><input type="text" name="amount" class="form-control amount"
                                                        value="{{ number_format($item->quantity * $item->price, 2, '.', '') }}"
                                                        readonly></td>
                                                <td>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="tax_checkbox"
                                                            {{ $item->taxable == 1 ? 'checked' : '' }}>
                                                    </div>
                                                    {{ Form::hidden('tax', $item->tax, ['class' => 'tax']) }}
                                                    {{ Form::hidden('itemTaxRate', $item->item_tax_rate ?? 0, ['class' => 'itemTaxRate']) }}
                                                    {{ Form::hidden('discount', $item->discount, ['class' => 'discount']) }}
                                                </td>
                                                <td><span class="delete-icon" data-repeater-delete><svg width="16"
                                                            height="16" viewBox="0 0 24 24" fill="currentColor">
                                                            <path
                                                                d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z" />
                                                        </svg></span></td>
                                            </tr>
                                        @endforeach
                                        @if ($creditMemo->items->count() == 0)
                                            <tr class="product-row" data-repeater-item>
                                                <td>
                                                    <div class="drag-handle sort-handler"><i class="ti ti-grid-dots"></i>
                                                    </div>
                                                </td>
                                                <td><span class="line-number">1</span></td>
                                                <td>{{ Form::select('item', $product_services, '', ['class' => 'form-select item', 'data-url' => route('invoice.product')]) }}
                                                </td>
                                                <td>{{ Form::textarea('description', '', ['class' => 'form-control pro_description', 'rows' => 1]) }}
                                                </td>
                                                <td>{{ Form::text('quantity', '', ['class' => 'form-control quantity']) }}
                                                </td>
                                                <td>{{ Form::text('price', '', ['class' => 'form-control price']) }}</td>
                                                <td><input type="text" name="amount" class="form-control amount"
                                                        value="0.00" readonly></td>
                                                <td>
                                                    <div class="form-check"><input class="form-check-input" type="checkbox"
                                                            name="tax_checkbox">
                                                    </div>
                                                    {{ Form::hidden('tax', '', ['class' => 'tax']) }}
                                                    {{ Form::hidden('itemTaxRate', '0', ['class' => 'itemTaxRate']) }}
                                                    {{ Form::hidden('discount', '0', ['class' => 'discount']) }}
                                                </td>
                                                <td><span class="delete-icon" data-repeater-delete><svg width="16"
                                                            height="16" viewBox="0 0 24 24" fill="currentColor">
                                                            <path
                                                                d="M6 19c0 1.1.9 2 2 2h8c1.1 0 2-.9 2-2V7H6v12zM19 4h-3.5l-1-1h-5l-1 1H5v2h14V4z" />
                                                        </svg></span></td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                                <div class="table-actions">
                                    <button type="button" class="btn-outline"
                                        data-repeater-create>{{ __('Add lines') }}</button>
                                </div>
                            </div>
                        </div>

                        {{-- Footer Totals & Memo --}}
                        <div class="row">
                            <div class="col-md-4">
                                <label class="form-label">{{ __('Message displayed on statement') }}</label>
                                {{ Form::textarea('memo', null, ['class' => 'form-control', 'rows' => 3]) }}
                                <label class="form-label mt-4">{{ __('Attachments') }}</label>
                                <div class="attachment-zone" id="attachment-zone">
                                    <span class="attachment-link">{{ __('Add attachment') }}</span>
                                </div>
                                <div id="attachment-file-inputs" class="d-none"></div>
                                <div id="attachments-list" class="mt-2">
                                    @if ($creditMemo->attachments)
                                        @foreach (json_decode($creditMemo->attachments) as $file)
                                            <div class="attachment-row">
                                                <span class="attachment-name">{{ $file }}</span>
                                                <a href="{{ asset('storage/uploads/credit_memo_attachments/' . $file) }}"
                                                    target="_blank" class="ms-2"><i class="ti ti-eye"></i></a>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-4"></div>
                            <div class="col-md-4">
                                <div class="totals-section">
                                    <div class="total-row"><span>{{ __('Subtotal') }}</span><span
                                            class="subTotal">0.00</span></div>
                                    <div class="total-row">
                                        <select name="discount_type" class="form-select discount-type-select"
                                            style="width: 120px;">
                                            <option value="percent"
                                                {{ $creditMemo->discount_type == 'percent' ? 'selected' : '' }}>
                                                {{ __('Percent') }}</option>
                                            <option value="value"
                                                {{ $creditMemo->discount_type == 'value' ? 'selected' : '' }}>
                                                {{ __('Value') }}</option>
                                        </select>
                                        <input type="number" step="0.01" name="discount_value"
                                            class="form-control discount-input" value="{{ $creditMemo->discount_value }}"
                                            style="width: 100px;">
                                        <span class="totalDiscount">0.00</span>
                                    </div>
                                    <div class="total-row"><span>{{ __('Taxable subtotal') }}</span><span
                                            class="taxableSubtotal">0.00</span></div>
                                    <div class="total-row">
                                        <select name="sales_tax_rate" class="form-select">
                                            <option value="" data-rate="0">{{ __('Select tax') }}</option>
                                            @foreach ($taxes as $tax)
                                                <option value="{{ $tax->id }}" data-rate="{{ $tax->rate }}"
                                                    {{ $creditMemo->sales_tax_rate == $tax->id ? 'selected' : '' }}>
                                                    {{ $tax->name }} ({{ $tax->rate }}%)</option>
                                            @endforeach
                                        </select>
                                        <span class="totalTax">0.00</span>
                                    </div>
                                    <div class="total-row final"><span>{{ __('Total') }}</span><span
                                            class="totalAmount">0.00</span></div>

                                    {{-- Hidden fields for submission --}}
                                    <input type="hidden" name="subtotal" value="{{ $creditMemo->subtotal }}">
                                    <input type="hidden" name="taxable_subtotal"
                                        value="{{ $creditMemo->taxable_subtotal }}">
                                    <input type="hidden" name="total_discount"
                                        value="{{ $creditMemo->total_discount }}">
                                    <input type="hidden" name="total_tax" value="{{ $creditMemo->total_tax }}">
                                    <input type="hidden" name="sales_tax_amount"
                                        value="{{ $creditMemo->sales_tax_amount }}">
                                    <input type="hidden" name="total_amount" value="{{ $creditMemo->total_amount }}">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="invoice-footer">
                        <div class="footer-left"></div>
                        <div class="footer-center"><button type="button" class="footer-link"
                                onclick="window.print()">{{ __('Print or download') }}</button></div>
                        <div class="footer-actions">
                            <button type="submit" class="btn btn-primary">{{ __('Save and close') }}</button>
                        </div>
                    </div>
                    {{ Form::close() }}
                </div>
            </div>
        </div>
    </div>
@endsection
