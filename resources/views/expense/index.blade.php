@extends('layouts.admin')

@section('page-title')
    {{ __('Expenses') }}
@endsection

@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Expenses') }}</li>
@endsection

@push('css-page')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
    <style>
        /* =========================================
                                               QBO Expense Transactions - Exact Design
                                               ========================================= */

        /* Page container */
        .qbo-expense-container {
            background: #fff;
            min-height: 100vh;
            padding: 20px 24px;
        }

        /* Page Header */
        .qbo-page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .qbo-page-title {
            font-size: 28px;
            font-weight: 700;
            color: #393A3D;
            margin: 0;
        }

        .qbo-header-actions {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        /* Buttons */
        .qbo-btn-tertiary {
            background: none;
            border: none;
            color: #393a3d;
            font-size: 14px;
            padding: 8px 16px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border-radius: 4px;
        }

        .qbo-btn-tertiary:hover {
            background: #f4f5f7;
        }

        .qbo-btn-secondary {
            background: #fff;
            border: 1px solid #8d9096;
            color: #393a3d;
            font-size: 14px;
            padding: 8px 16px;
            cursor: pointer;
            border-radius: 4px;
        }

        .qbo-btn-secondary:hover {
            background: #f4f5f7;
        }

        .qbo-btn-primary {
            background: #2ca01c;
            border: none;
            color: #fff;
            font-size: 14px;
            font-weight: 600;
            padding: 8px 20px;
            cursor: pointer;
            border-radius: 4px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .qbo-btn-primary:hover {
            background: #1e8012;
            color: #fff;
        }

        .qbo-btn-primary svg {
            width: 16px;
            height: 16px;
        }

        /* Split Button */
        .qbo-split-btn {
            display: inline-flex;
        }

        .qbo-split-btn .qbo-btn-secondary:first-child {
            border-radius: 4px 0 0 4px;
            border-right: none;
        }

        .qbo-split-btn .qbo-btn-secondary:last-child {
            border-radius: 0 4px 4px 0;
            padding: 8px 10px;
        }

        /* Filter Bar */
        .qbo-filter-bar {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 16px;
            flex-wrap: wrap;
        }

        .qbo-filter-dropdown {
            position: relative;
        }

        .qbo-filter-btn {
            background: #fff;
            border: 1px solid #c1c4c8;
            border-radius: 4px;
            padding: 8px 36px 8px 12px;
            font-size: 14px;
            color: #393a3d;
            cursor: pointer;
            min-width: 180px;
            text-align: left;
            position: relative;
        }

        .qbo-filter-btn::after {
            content: '';
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            border: solid #6b6c72;
            border-width: 0 2px 2px 0;
            padding: 3px;
            transform: translateY(-70%) rotate(45deg);
        }

        .qbo-filter-dropdown-menu {
            position: absolute;
            top: 100%;
            left: 0;
            background: #fff;
            border: 1px solid #e0e3e5;
            border-radius: 4px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            min-width: 240px;
            max-height: 300px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
        }

        .qbo-filter-dropdown.show .qbo-filter-dropdown-menu {
            display: block;
        }

        .qbo-filter-dropdown-item {
            padding: 10px 16px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            color: #393a3d;
        }

        .qbo-filter-dropdown-item:hover {
            background: #f4f5f7;
        }

        .qbo-filter-dropdown-item.active {
            font-weight: 600;
        }

        .qbo-filter-dropdown-item.active::before {
            content: '✓';
            color: #2ca01c;
            font-weight: bold;
        }

        /* Filter Icon Button */
        .qbo-filter-icon-btn {
            background: none;
            border: none;
            color: #393a3d;
            font-size: 14px;
            padding: 8px 12px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        .qbo-filter-icon-btn:hover {
            background: #f4f5f7;
            border-radius: 4px;
        }

        /* Date Chip */
        .qbo-date-chip {
            background: #e4f7e1;
            border: 1px solid #2ca01c;
            border-radius: 16px;
            padding: 6px 14px;
            font-size: 13px;
            color: #1e7817;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .qbo-date-chip-label {
            font-weight: 600;
        }

        /* Table Actions (right side) */
        .qbo-table-header-actions {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-left: auto;
        }

        .qbo-icon-btn {
            background: none;
            border: none;
            color: #6b6c72;
            padding: 8px;
            cursor: pointer;
            border-radius: 4px;
        }

        .qbo-icon-btn:hover {
            background: #f4f5f7;
            color: #393a3d;
        }

        /* =========================================
                                               Table - QBO Style
                                               ========================================= */
        .qbo-table-wrapper {
            background: #fff;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        /* Custom scrollbar */
        .qbo-table-wrapper::-webkit-scrollbar {
            width: 12px;
            height: 12px;
        }

        .qbo-table-wrapper::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 6px;
        }

        .qbo-table-wrapper::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 6px;
        }

        .qbo-table-wrapper::-webkit-scrollbar-thumb:hover {
            background: #a1a1a1;
        }

        /* Table styling */
        #expenseTable {
            width: 100% !important;
            min-width: 1000px;
            margin: 0 !important;
            border-collapse: collapse;
        }

        #expenseTable thead th {
            background: #fff;
            font-weight: 400;
            font-size: 12px;
            color: #6b6c72;
            text-transform: uppercase;
            border-top: none;
            border-bottom: 1px solid #e0e3e5;
            padding: 12px 16px;
            text-align: left;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        #expenseTable thead th.text-end {
            text-align: right;
        }

        #expenseTable tbody td {
            padding: 14px 16px;
            border-bottom: 1px solid #e0e3e5;
            font-size: 14px;
            color: #393a3d;
            vertical-align: middle;
            white-space: nowrap;
        }

        #expenseTable tbody tr:hover {
            background: #f8f9fa;
        }

        /* Category dropdown in table */
        .qbo-category-select {
            background: #fff;
            border: 1px solid #e0e3e5;
            border-radius: 4px;
            padding: 6px 28px 6px 10px;
            font-size: 13px;
            color: #393a3d;
            min-width: 140px;
            cursor: pointer;
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24'%3E%3Cpath fill='%236b6c72' d='M12.014 16.018a1 1 0 0 1-.708-.294L5.314 9.715A1.001 1.001 0 0 1 6.73 8.3l5.286 5.3 5.3-5.285a1 1 0 0 1 1.413 1.416l-6.009 5.995a1 1 0 0 1-.706.292'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 8px center;
            background-size: 16px;
        }

        /* Action button */
        .qbo-action-btn {
            background: none;
            border: none;
            color: #0077c5;
            font-size: 13px;
            padding: 6px 12px;
            cursor: pointer;
        }

        .qbo-action-btn:hover {
            text-decoration: underline;
        }

        /* Total row */
        .qbo-total-row td {
            font-weight: 600 !important;
            background: #fff;
            border-top: 1px solid #e0e3e5;
            padding: 16px !important;
        }

        /* =========================================
                                               Footer/Pagination
                                               ========================================= */
        .qbo-table-footer {
            display: flex;
            justify-content: flex-end;
            align-items: center;
            padding: 16px 0;
            gap: 16px;
            font-size: 14px;
            color: #6b6c72;
        }

        .qbo-pagination {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .qbo-pagination-btn {
            background: none;
            border: none;
            color: #0077c5;
            padding: 6px 12px;
            cursor: pointer;
            font-size: 14px;
        }

        .qbo-pagination-btn:hover {
            text-decoration: underline;
        }

        .qbo-pagination-btn:disabled {
            color: #c1c4c8;
            cursor: not-allowed;
        }

        .qbo-pagination-btn:disabled:hover {
            text-decoration: none;
        }

        .qbo-pagination-info {
            color: #393a3d;
        }

        /* New Transaction Dropdown */
        .qbo-new-txn-dropdown {
            position: relative;
            display: inline-block;
        }

        .qbo-new-txn-menu {
            position: absolute;
            top: 100%;
            right: 0;
            background: #fff;
            border: 1px solid #e0e3e5;
            border-radius: 4px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            min-width: 200px;
            z-index: 1000;
            display: none;
        }

        .qbo-new-txn-dropdown.show .qbo-new-txn-menu {
            display: block;
        }

        .qbo-new-txn-menu a {
            display: block;
            padding: 10px 16px;
            color: #393a3d;
            text-decoration: none;
            font-size: 14px;
        }

        .qbo-new-txn-menu a:hover {
            background: #f4f5f7;
        }

        /* Hide DataTables default styling */
        .dataTables_wrapper .dataTables_length,
        .dataTables_wrapper .dataTables_filter,
        .dataTables_wrapper .dataTables_info,
        .dataTables_wrapper .dataTables_paginate {
            display: none;
        }

        /* Header action icons in column */
        .qbo-header-icons {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .qbo-header-icon-btn {
            background: none;
            border: none;
            color: #6b6c72;
            padding: 4px;
            cursor: pointer;
            font-size: 16px;
        }

        .qbo-header-icon-btn:hover {
            color: #393a3d;
        }

        /* Filter Modal */
        .qbo-filter-modal {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 2000;
            display: none;
            align-items: flex-start;
            justify-content: center;
            padding-top: 100px;
        }

        .qbo-filter-modal.show {
            display: flex;
        }

        .qbo-filter-modal-content {
            background: #fff;
            border-radius: 8px;
            padding: 24px;
            min-width: 400px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
        }

        .qbo-filter-modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .qbo-filter-modal-close {
            background: none;
            border: none;
            font-size: 24px;
            cursor: pointer;
            color: #6b6c72;
        }

        .qbo-filter-group {
            margin-bottom: 16px;
        }

        .qbo-filter-label {
            font-size: 13px;
            color: #6b6c72;
            margin-bottom: 6px;
            display: block;
        }

        .qbo-filter-select {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #c1c4c8;
            border-radius: 4px;
            font-size: 14px;
        }

        .qbo-filter-row {
            display: flex;
            gap: 12px;
        }

        .qbo-filter-row .qbo-filter-group {
            flex: 1;
        }

        .qbo-filter-actions {
            display: flex;
            gap: 12px;
            margin-top: 20px;
        }

        .qbo-filter-reset {
            background: #fff;
            border: 1px solid #c1c4c8;
            padding: 8px 20px;
            border-radius: 4px;
            cursor: pointer;
        }

        .qbo-filter-apply {
            background: #2ca01c;
            border: none;
            color: #fff;
            padding: 8px 24px;
            border-radius: 4px;
            cursor: pointer;
        }
    </style>
@endpush

@section('content')
    {{-- MY APPS Sidebar (Fixed Position) --}}
    @include('partials.admin.allApps-subMenu-Sidebar', [
        'activeSection' => 'expenses',
        'activeItem' => 'expense_transactions',
    ])

    <div class="qbo-expense-container">
        {{-- Page Header --}}
        <div class="qbo-page-header">
            <h1 class="qbo-page-title">{{ __('Expenses') }}</h1>
            <div class="qbo-header-actions">
                {{-- Give feedback --}}
                <button class="qbo-btn-tertiary">
                    <i class="ti ti-message-circle"></i>
                    {{ __('Give feedback') }}
                </button>

                {{-- Purchase notifications --}}
                <button class="qbo-btn-secondary">
                    {{ __('Purchase notifications') }}
                </button>

                {{-- Print Checks Split Button --}}
                <div class="qbo-split-btn">

                    <button id="open-pay-modal" class="qbo-btn-secondary" data-bs-toggle="tooltip"
                        title="{{ __('Pay Bill') }}">
                        <i class="ti ti-cash"></i> {{ __('Pay Bill') }}
                    </button>
                    <button class="qbo-btn-secondary dropdown-toggle" data-bs-toggle="dropdown">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" width="16"
                            height="16">
                            <path fill="currentColor"
                                d="M12.014 16.018a1 1 0 0 1-.708-.294L5.314 9.715A1.001 1.001 0 0 1 6.73 8.3l5.286 5.3 5.3-5.285a1 1 0 0 1 1.413 1.416l-6.009 5.995a1 1 0 0 1-.706.292">
                            </path>
                        </svg>
                    </button>

                </div>

                {{-- New Transaction Dropdown --}}
                <div class="qbo-new-txn-dropdown">
                    <button class="qbo-btn-primary" onclick="toggleNewTxnDropdown()">
                        {{ __('New transaction') }}
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" width="16"
                            height="16">
                            <path fill="currentColor"
                                d="M12.014 16.018a1 1 0 0 1-.708-.294L5.314 9.715A1.001 1.001 0 0 1 6.73 8.3l5.286 5.3 5.3-5.285a1 1 0 0 1 1.413 1.416l-6.009 5.995a1 1 0 0 1-.706.292">
                            </path>
                        </svg>
                    </button>
                    <div class="qbo-new-txn-menu">
                        <a href="{{ route('timeActivity.create') }}">{{ __('Time activity') }}</a>
                        <a href="{{ route('bill.create', 0) }}">{{ __('Bill') }}</a>
                        <a href="{{ route('expense.create', 0) }}">{{ __('Expense') }}</a>
                        <a href="#" class="openChecksModal"
                            data-url="{{ route('checks.create') }}">{{ __('Check') }}</a>
                        <a href="{{ url('purchase/create/0') }}">{{ __('Purchase order') }}</a>
                        <a href="#" class="openChecksModal"
                            data-url="{{ route('vendor-credit.create') }}">{{ __('Vendor credit') }}</a>
                        <a href="{{ route('creditcreditcard.create', 0) }}">{{ __('Credit card credit') }}</a>
                        <a href="#" class="openChecksModal"
                            data-url="{{ route('paydowncreditcard.create') }}">{{ __('Pay down credit card') }}</a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filter Bar --}}
        <div class="qbo-filter-bar">
            {{-- All Transactions Dropdown --}}
            <div class="qbo-filter-dropdown" id="txnTypeDropdown">
                <button class="qbo-filter-btn" onclick="toggleFilterDropdown('txnTypeDropdown')">
                    <span id="selectedTxnType">{{ __('All transactions') }}</span>
                </button>
                <div class="qbo-filter-dropdown-menu">
                    <div class="qbo-filter-dropdown-item active" data-value="all">{{ __('All transactions') }}</div>
                    <div class="qbo-filter-dropdown-item" data-value="expense">{{ __('Expense') }}</div>
                    <div class="qbo-filter-dropdown-item" data-value="bill">{{ __('Bill') }}</div>
                    <div class="qbo-filter-dropdown-item" data-value="bill_payment">{{ __('Bill payment') }}</div>
                    <div class="qbo-filter-dropdown-item" data-value="check">{{ __('Check') }}</div>
                    <div class="qbo-filter-dropdown-item" data-value="purchase_order">{{ __('Purchase order') }}</div>
                    <div class="qbo-filter-dropdown-item" data-value="recently_paid">{{ __('Recently paid') }}</div>
                    <div class="qbo-filter-dropdown-item" data-value="vendor_credit">{{ __('Vendor credit') }}</div>
                    <div class="qbo-filter-dropdown-item" data-value="cc_payment">{{ __('Credit card payment') }}</div>
                </div>
            </div>

            {{-- Filter Button --}}
            <button class="qbo-filter-icon-btn" onclick="openFilterModal()">
                <i class="ti ti-adjustments-horizontal"></i>
                {{ __('Filter') }}
            </button>

            {{-- Date Chip --}}
            <div class="qbo-date-chip">
                <span class="qbo-date-chip-label">{{ __('Dates:') }}</span>
                {{ __('Last 12 months') }}
            </div>

            {{-- Right side icons --}}
            <div class="qbo-table-header-actions">
                <button class="qbo-icon-btn" title="{{ __('Export to Excel') }}">
                    <i class="ti ti-download"></i>
                </button>
                <button class="qbo-icon-btn" title="{{ __('Print') }}">
                    <i class="ti ti-printer"></i>
                </button>
                <button class="qbo-icon-btn" title="{{ __('Settings') }}">
                    <i class="ti ti-settings"></i>
                </button>
            </div>
        </div>

        {{-- Table --}}
        <div class="qbo-table-wrapper">
            <table id="expenseTable" class="display" style="width:100%">
                <thead>
                    <tr>
                        <th width="40"><input type="checkbox" class="form-check-input" id="selectAllHeader"></th>
                        <th>{{ __('DATE') }}</th>
                        <th>{{ __('TYPE') }}</th>
                        <th>{{ __('NO.') }}</th>
                        <th>{{ __('PAYEE') }}</th>
                        <th>{{ __('CLASS') }}</th>
                        <th>{{ __('LOCATION') }}</th>
                        <th>{{ __('STATUS') }}</th>
                        <th>{{ __('METHOD') }}</th>
                        <th>{{ __('SOURCE') }}</th>
                        <th>{{ __('CATEGORY') }}</th>
                        <th>{{ __('MEMO') }}</th>
                        <th>{{ __('DUE DATE') }}</th>
                        <th class="text-end">{{ __('BALANCE') }}</th>
                        <th class="text-end">{{ __('TOTAL') }}</th>
                        <th width="50">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" width="20"
                                height="20">
                                <path fill="currentColor"
                                    d="M10 22a4 4 0 0 1-4-4V7.5a5.5 5.5 0 1 1 11 0V19a1 1 0 0 1-2 0V7.5a3.5 3.5 0 1 0-7 0V18a2 2 0 0 0 4 0V8.5a.5.5 0 0 0-1 0V17a1 1 0 0 1-2 0V8.5a2.5 2.5 0 1 1 5 0V18a4 4 0 0 1-4 4">
                                </path>
                            </svg>
                        </th>
                        <th>{{ __('ACTION') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($transactions ?? [] as $txn)
                        @php
                            // Handle both array and object access
                            $id = is_array($txn) ? $txn['id'] ?? '' : $txn->id ?? '';
                            $date = is_array($txn) ? $txn['date'] ?? '' : $txn->date ?? '';
                            $type = is_array($txn) ? $txn['type'] ?? '' : $txn->type ?? '';
                            $no = is_array($txn) ? $txn['no'] ?? '' : $txn->no ?? '';
                            $customer = is_array($txn) ? $txn['customer'] ?? '' : $txn->customer ?? '';
                            $memo = is_array($txn) ? $txn['memo'] ?? '' : $txn->memo ?? '';
                            $amount = is_array($txn) ? $txn['amount'] ?? 0 : $txn->amount ?? 0;
                            $statusVal = is_array($txn) ? $txn['status'] ?? '' : $txn->status ?? '';
                            $viewUrl = is_array($txn) ? $txn['view_url'] ?? '' : $txn->view_url ?? '';
                            $editPaymentUrl = is_array($txn)
                                ? $txn['edit_payment_url'] ?? ''
                                : $txn->edit_payment_url ?? '';
                            $deleteUrl = is_array($txn) ? $txn['delete_url'] ?? '' : $txn->delete_url ?? '';
                            $activityUrl = is_array($txn) ? $txn['activity_url'] ?? '' : $txn->activity_url ?? '';
                            $convertUrl = is_array($txn) ? $txn['convert_url'] ?? '' : $txn->convert_url ?? '';
                        @endphp
                        <tr class="{{ strtolower($txn['type']) === 'bill' ? 'bills-main-row' : '' }}"
                            data-bill-id="{{ $txn['id'] }}" data-bill-due="{{ $txn['balance'] }}">
                            <td><input type="checkbox"
                                    class="form-check-input row-checkbox {{ strtolower($txn['type']) === 'bill' ? 'bill-row-checkbox' : '' }}"
                                    value="{{ $txn['id'] }}" data-type="{{ $txn['type_key'] }}"
                                    data-bill-id="{{ $txn['id'] }}"></td>
                            <td>{{ \Auth::user()->dateFormat($txn['date']) }}</td>
                            <td>{{ $txn['type'] }}</td>
                            <td class="bill-number">{{ $txn['no'] }}</td>
                            <td class="bill-vendor">{{ $txn['payee'] }}</td>
                            <td>{{ $txn['class'] }}</td>
                            <td>{{ $txn['location'] }}</td>
                            <td class="bill-status">
                                @php
                                    $status = strtolower($txn['status']);
                                    $statusClass = 'default';
                                    if (str_contains($status, 'overdue')) {
                                        $statusClass = 'danger';
                                    } elseif ($status === 'paid' || $status === 'applied') {
                                        $statusClass = 'success';
                                    } elseif ($status === 'partial') {
                                        $statusClass = 'warning';
                                    } elseif ($status === 'open' || $status === 'unapplied') {
                                        $statusClass = 'info';
                                    }
                                @endphp
                                <span class="qbo-status-{{ $statusClass }}">{{ $txn['status'] }}</span>
                            </td>
                            <td>{{ $txn['method'] }}</td>
                            <td>{{ $txn['source'] }}</td>
                            <td>{{ $txn['category'] }}</td>
                            <td>{{ $txn['memo'] }}</td>
                            <td class="bill-due-date">{{ $txn['due_date'] }}</td>
                            <td class="text-end">{{ \Auth::user()->priceFormat($txn['balance']) }}</td>
                            <td class="text-end" data-amount="{{ $txn['total'] }}">
                                @if ($txn['total'] < 0)
                                    -{{ \Auth::user()->priceFormat(abs($txn['total'])) }}
                                @else
                                    {{ \Auth::user()->priceFormat($txn['total']) }}
                                @endif
                            </td>
                            <td>{{ $txn['attachments'] }}</td>
                            <td style="white-space:nowrap;">
                                <a href="{{ $txn['view_url'] }}" class="qbo-action-btn">{{ __('View/Edit') }}</a>
                                {{-- Dropup ▼ --}}
                                <div class="dropup d-inline-block ms-1">
                                    <button type="button" class="btn btn-sm p-0 border-0 bg-transparent txn-dropup-btn"
                                        data-bs-toggle="dropdown" data-bs-strategy="fixed" aria-expanded="false"
                                        title="{{ __('More actions') }}"
                                        style="color:#0077c5;font-size:9px;vertical-align:middle;line-height:1;">&#9650;</button>
                                    <ul class="dropdown-menu dropdown-menu-end shadow"
                                        style="min-width:200px;border-radius:8px;padding:4px 0;border:1px solid #e0e3e5;z-index:99999;">

                                        {{-- View/Edit — all types --}}
                                        @if (!empty($viewUrl))
                                            <li>
                                                <a class="dropdown-item" href="{{ $viewUrl }}"
                                                    style="padding:10px 16px;font-size:14px;color:#393a3d;">
                                                    {{ __('View/Edit') }}
                                                </a>
                                            </li>
                                        @endif

                                        {{-- ── INVOICE ── controller pushes __('Invoice') --}}
                                        @if (strtolower($type) === 'invoice')
                                            {{-- <li><a class="dropdown-item" href="#" onclick="showComingSoon();return false;" style="padding:10px 16px;font-size:14px;color:#393a3d;">{{ __('Duplicate') }}</a></li> --}}
                                            {{-- <li><a class="dropdown-item" href="#" onclick="showComingSoon();return false;" style="padding:10px 16px;font-size:14px;color:#393a3d;">{{ __('Send') }}</a></li> --}}
                                            {{-- <li><a class="dropdown-item" href="#" onclick="showComingSoon();return false;" style="padding:10px 16px;font-size:14px;color:#393a3d;">{{ __('Send reminder') }}</a></li> --}}
                                            <li><a class="dropdown-item" href="#"
                                                    onclick="showComingSoon();return false;"
                                                    style="padding:10px 16px;font-size:14px;color:#393a3d;">{{ __('Create task') }}</a>
                                            </li>
                                            {{-- <li><a class="dropdown-item" href="#" onclick="showComingSoon();return false;" style="padding:10px 16px;font-size:14px;color:#393a3d;">{{ __('Share invoice link') }}</a></li> --}}
                                            {{-- <li><a class="dropdown-item" href="#" onclick="showComingSoon();return false;" style="padding:10px 16px;font-size:14px;color:#393a3d;">{{ __('Make recurring payment') }}</a></li> --}}
                                            {{-- <li><a class="dropdown-item" href="#" onclick="showComingSoon();return false;" style="padding:10px 16px;font-size:14px;color:#393a3d;">{{ __('Print') }}</a></li> --}}
                                            {{-- <li><a class="dropdown-item" href="#" onclick="showComingSoon();return false;" style="padding:10px 16px;font-size:14px;color:#393a3d;">{{ __('Print packing slip') }}</a></li> --}}
                                            <li><a class="dropdown-item" href="#"
                                                    onclick="showComingSoon();return false;"
                                                    style="padding:10px 16px;font-size:14px;color:#393a3d;">{{ __('Void') }}</a>
                                            </li>

                                            {{-- ── ESTIMATE ── controller pushes __('Estimate') --}}
                                        @elseif (strtolower($type) === 'estimate')
                                            {{-- <li><a class="dropdown-item" href="#" onclick="showComingSoon();return false;" style="padding:10px 16px;font-size:14px;color:#393a3d;">{{ __('Duplicate') }}</a></li> --}}
                                            {{-- <li><a class="dropdown-item" href="#" onclick="showComingSoon();return false;" style="padding:10px 16px;font-size:14px;color:#393a3d;">{{ __('Send') }}</a></li> --}}
                                            {{-- <li><a class="dropdown-item" href="#" onclick="showComingSoon();return false;" style="padding:10px 16px;font-size:14px;color:#393a3d;">{{ __('Share estimate link') }}</a></li> --}}
                                            {{-- <li><a class="dropdown-item" href="#" onclick="showComingSoon();return false;" style="padding:10px 16px;font-size:14px;color:#393a3d;">{{ __('Copy to purchase order') }}</a></li> --}}
                                            @if (!empty($convertUrl))
                                                <li>
                                                    <a class="dropdown-item" href="{{ $convertUrl }}"
                                                        style="padding:10px 16px;font-size:14px;color:#393a3d;">
                                                        {{ __('Estimate to Invoice') }}
                                                    </a>
                                                </li>
                                            @endif

                                            {{-- ── CREDIT MEMO ── controller pushes __('Credit Memo') --}}
                                        @elseif (strtolower($type) === 'credit memo')
                                            {{-- <li><a class="dropdown-item" href="#" onclick="showComingSoon();return false;" style="padding:10px 16px;font-size:14px;color:#393a3d;">{{ __('Duplicate') }}</a></li>
            <li><a class="dropdown-item" href="#" onclick="showComingSoon();return false;" style="padding:10px 16px;font-size:14px;color:#393a3d;">{{ __('Send') }}</a></li>
            <li><a class="dropdown-item" href="#" onclick="showComingSoon();return false;" style="padding:10px 16px;font-size:14px;color:#393a3d;">{{ __('Void') }}</a></li> --}}

                                            {{-- ── SALES RECEIPT ── controller pushes __('Sales Receipt') --}}
                                        @elseif (strtolower($type) === 'sales receipt')
                                            {{-- <li><a class="dropdown-item" href="#" onclick="showComingSoon();return false;" style="padding:10px 16px;font-size:14px;color:#393a3d;">{{ __('Duplicate') }}</a></li>
            <li><a class="dropdown-item" href="#" onclick="showComingSoon();return false;" style="padding:10px 16px;font-size:14px;color:#393a3d;">{{ __('Send') }}</a></li>
            <li><a class="dropdown-item" href="#" onclick="showComingSoon();return false;" style="padding:10px 16px;font-size:14px;color:#393a3d;">{{ __('Print packing slip') }}</a></li>
            <li><a class="dropdown-item" href="#" onclick="showComingSoon();return false;" style="padding:10px 16px;font-size:14px;color:#393a3d;">{{ __('Void') }}</a></li> --}}

                                            {{-- All other types (Payment, Refund, Delayed Credit/Charge, Time Charge) — no extra items --}}
                                        @endif

                                        {{-- Delete — all types --}}
                                        @if (!empty($deleteUrl))
                                            <li>
                                                <a class="dropdown-item txn-delete-link" href="#"
                                                    data-url="{{ $deleteUrl }}"
                                                    style="padding:10px 16px;font-size:14px;color:#393a3d;">
                                                    {{ __('Delete') }}
                                                </a>
                                            </li>
                                        @endif

                                        {{-- View activity — all types --}}
                                        @if (!empty($activityUrl))
                                            <li>
                                                <a class="dropdown-item txn-activity-link" href="#"
                                                    data-url="{{ $activityUrl }}"
                                                    style="padding:10px 16px;font-size:14px;color:#393a3d;">
                                                    {{ __('View activity') }}
                                                </a>
                                            </li>
                                        @endif

                                    </ul>
                                </div>
                            </td>

                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="qbo-total-row">
                        <td></td>
                        <td>{{ __('Total') }}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td class="text-end" id="totalAmount">{{ \Auth::user()->priceFormat($totalAmount ?? 0) }}</td>
                        <td></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        {{-- Footer/Pagination --}}
        <div class="qbo-table-footer">
            <div class="qbo-pagination">
                <button class="qbo-pagination-btn" id="paginateFirst">{{ __('First') }}</button>
                <button class="qbo-pagination-btn" id="paginatePrev">{{ __('Previous') }}</button>
                <span class="qbo-pagination-info" id="paginateInfo">1-{{ count($transactions ?? []) }} of
                    {{ count($transactions ?? []) }}</span>
                <button class="qbo-pagination-btn" id="paginateNext">{{ __('Next') }}</button>
                <button class="qbo-pagination-btn" id="paginateLast">{{ __('Last') }}</button>
            </div>
        </div>
    </div>

    {{-- Filter Modal --}}
    <div class="qbo-filter-modal" id="filterModal">
        <div class="qbo-filter-modal-content">
            <div class="qbo-filter-modal-header">
                <h3>{{ __('Filter') }}</h3>
                <button class="qbo-filter-modal-close" onclick="closeFilterModal()">&times;</button>
            </div>

            <form method="GET" action="{{ route('expense.index') }}">
                <div class="qbo-filter-group">
                    <label class="qbo-filter-label">{{ __('Status') }}</label>
                    <select class="qbo-filter-select" name="status">
                        <option value="">{{ __('All statuses') }}</option>
                        <option value="open">{{ __('Open') }}</option>
                        <option value="paid">{{ __('Paid') }}</option>
                    </select>
                </div>

                <div class="qbo-filter-group">
                    <label class="qbo-filter-label">{{ __('Delivery method') }}</label>
                    <select class="qbo-filter-select" name="delivery">
                        <option value="">{{ __('Any') }}</option>
                    </select>
                </div>

                <div class="qbo-filter-group">
                    <label class="qbo-filter-label">{{ __('Date') }}</label>
                    <select class="qbo-filter-select" name="date_range">
                        <option value="last_12_months">{{ __('Last 12 months') }}</option>
                        <option value="this_month">{{ __('This month') }}</option>
                        <option value="last_month">{{ __('Last month') }}</option>
                        <option value="custom">{{ __('Custom') }}</option>
                    </select>
                </div>

                <div class="qbo-filter-row">
                    <div class="qbo-filter-group">
                        <label class="qbo-filter-label">{{ __('From') }}</label>
                        <input type="date" class="qbo-filter-select" name="date_from">
                    </div>
                    <div class="qbo-filter-group">
                        <label class="qbo-filter-label">{{ __('To') }}</label>
                        <input type="date" class="qbo-filter-select" name="date_to">
                    </div>
                </div>

                <div class="qbo-filter-group">
                    <label class="qbo-filter-label">{{ __('Payee') }}</label>
                    <select class="qbo-filter-select" name="payee">
                        <option value="">{{ __('All') }}</option>
                    </select>
                </div>

                <div class="qbo-filter-group">
                    <label class="qbo-filter-label">{{ __('Category') }}</label>
                    <select class="qbo-filter-select" name="category">
                        <option value="">{{ __('All') }}</option>
                        @foreach ($category ?? [] as $id => $name)
                            <option value="{{ $id }}">{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="qbo-filter-actions">
                    <button type="button" class="qbo-filter-reset"
                        onclick="closeFilterModal()">{{ __('Reset') }}</button>
                    <button type="submit" class="qbo-filter-apply">{{ __('Apply') }}</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Pay Bill Modal --}}
    <div class="modal fade" id="payBillModal" tabindex="-1" aria-labelledby="payBillModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-fullscreen">
            <div class="modal-content" style="background-color: #fff;">
                {{-- QBO Style Header --}}
                <div class="modal-header border-0 pb-0" style="background: #fff; padding: 16px 24px;">
                    <h4 class="modal-title fw-normal" id="payBillModalLabel" style="font-size: 24px; color: #333;">
                        {{ __('Pay Bills') }}</h4>
                    <div class="ms-auto d-flex align-items-center gap-3">
                        <a href="#" class="text-success text-decoration-none d-flex align-items-center"
                            style="font-size: 14px;">
                            <i class="ti ti-message-circle me-1"></i> {{ __('Give feedback') }}
                        </a>
                        <button type="button" class="btn btn-link p-0 text-muted" style="font-size: 20px;"
                            data-bs-toggle="tooltip" title="{{ __('Help') }}">
                            <i class="ti ti-help-circle"></i>
                        </button>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                            style="font-size: 12px;"></button>
                    </div>
                </div>

                {{-- FORM START --}}
                {{ Form::open(['route' => ['bill.bulk.payment'], 'method' => 'post', 'id' => 'bulkPaymentForm', 'enctype' => 'multipart/form-data', 'style' => 'display: contents;']) }}

                <div class="modal-body p-0" style="overflow-y: auto;">
                    {{-- QBO Style Transaction Controls Header --}}
                    <div class="px-4 py-3">
                        <div class="row align-items-end px-4 py-3" style="background: #ECEEF1;">
                            <div class="col-auto">
                                <label class="form-label text-muted mb-1"
                                    style="font-size: 12px;">{{ __('Payment account') }}</label>
                                <div class="position-relative">
                                    {{ Form::select('account_id', $accounts ?? [], null, [
                                        'class' => 'form-select',
                                        'placeholder' => __('Select an account'),
                                        'required' => true,
                                        'style' => 'min-width: 180px; font-size: 14px; border-color: #c0c0c0;',
                                    ]) }}
                                </div>
                            </div>

                            <div class="col-auto">
                                <label class="form-label text-muted mb-1"
                                    style="font-size: 12px;">{{ __('Payment date') }}</label>
                                {{ Form::date('date', now()->format('Y-m-d'), [
                                    'class' => 'form-control',
                                    'required' => true,
                                    'style' => 'min-width: 160px; font-size: 14px; border-color: #c0c0c0;',
                                ]) }}
                            </div>

                            <div class="col text-end">
                                <div class="text-muted text-uppercase" style="font-size: 11px; letter-spacing: 0.5px;">
                                    {{ __('TOTAL PAYMENT AMOUNT') }}</div>
                                <div id="modal-grand-total" class="fw-normal" style="font-size: 36px; color: #333;">
                                    $<span class="grand-total-value">0.00</span></div>
                            </div>
                        </div>
                    </div>

                    {{-- QBO Style Filters Section --}}
                    <div class="px-4 py-3 d-flex align-items-center justify-content-between"
                        style="border-bottom: 1px solid #e9ecef;">
                        <input type="text" id="modal-date-filter" class="form-control"
                            placeholder="Filter by date..." style="width: 250px;">
                        <div class="d-flex align-items-center gap-2">
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary dropdown-toggle d-flex align-items-center"
                                    type="button" id="payBillFiltersDropdown" data-bs-toggle="dropdown"
                                    aria-expanded="false" style="font-size: 14px; border-color: #2ca01c; color: #2ca01c;">
                                    <i class="ti ti-filter me-1"></i> {{ __('Filters') }}
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="payBillFiltersDropdown">
                                    <li><a class="dropdown-item" href="#">{{ __('All Bills') }}</a></li>
                                    <li><a class="dropdown-item" href="#">{{ __('Overdue Only') }}</a></li>
                                    <li><a class="dropdown-item" href="#">{{ __('Due This Week') }}</a></li>
                                </ul>
                            </div>
                            <span class="badge rounded-pill"
                                style="background: #e8e8e8; color: #333; font-weight: normal; font-size: 13px; padding: 6px 12px;">{{ __('Last 12 months') }}</span>
                        </div>
                        <button type="button" class="btn btn-link text-muted p-0" data-bs-toggle="tooltip"
                            title="{{ __('Settings') }}">
                            <i class="ti ti-settings" style="font-size: 20px;"></i>
                        </button>
                    </div>

                    {{-- QBO Style Bills Table --}}
                    <div class="table-responsive px-4">
                        <table id="payModalTable" class="table table-hover mb-0" style="font-size: 14px;">
                            <thead>
                                <tr style="border-bottom: 2px solid #e0e0e0;">
                                    <th class="text-center" style="width: 40px; padding: 12px 8px;">
                                        <input type="checkbox" id="modalSelectAll" class="form-check-input"
                                            style="cursor: pointer;">
                                    </th>
                                    <th style="font-weight: 500; color: #6b6b6b; padding: 12px 8px;">{{ __('PAYEE') }}
                                    </th>
                                    <th style="font-weight: 500; color: #6b6b6b; padding: 12px 8px;">{{ __('REF NO.') }}
                                        <i class="ti ti-arrow-down" style="font-size: 12px;"></i>
                                    </th>
                                    <th style="font-weight: 500; color: #6b6b6b; padding: 12px 8px;">{{ __('DUE DATE') }}
                                    </th>
                                    <th style="font-weight: 500; color: #6b6b6b; padding: 12px 8px;">{{ __('STATUS') }}
                                    </th>
                                    <th class="text-end" style="font-weight: 500; color: #6b6b6b; padding: 12px 8px;">
                                        {{ __('OPEN BALANCE') }}</th>
                                    <th class="text-center" style="font-weight: 500; color: #6b6b6b; padding: 12px 8px;">
                                        {{ __('CREDIT APPLIED') }}</th>
                                    <th class="text-center"
                                        style="font-weight: 500; color: #6b6b6b; padding: 12px 8px; width: 120px;">
                                        {{ __('PAYMENT') }}</th>
                                    <th class="text-end" style="font-weight: 500; color: #6b6b6b; padding: 12px 8px;">
                                        {{ __('TOTAL AMOUNT') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                {{-- Populated dynamically by JS --}}
                            </tbody>
                            <tfoot>
                                <tr style="border-top: 2px solid #e0e0e0;">
                                    <td colspan="5" class="text-start" style="padding: 12px 8px;">
                                        <strong style="color: #333;">{{ __('Total payment') }}</strong>
                                    </td>
                                    <td class="text-end" style="padding: 12px 8px;"><strong
                                            class="modal-footer-open-balance" style="color: #333;">$0.00</strong></td>
                                    <td class="text-center" style="padding: 12px 8px;"><strong
                                            class="modal-footer-credit" style="color: #333;">$0.00</strong></td>
                                    <td class="text-center" style="padding: 12px 8px;"><strong
                                            class="modal-footer-payment" style="color: #333;">$0.00</strong></td>
                                    <td class="text-end" style="padding: 12px 8px;"><strong class="modal-footer-total"
                                            style="color: #333;">$0.00</strong></td>
                                </tr>
                                <tr>
                                    <td colspan="9" class="text-end" style="border: none; padding: 8px;">
                                        <small class="text-muted me-2">{{ __('First') }}</small>
                                        <small class="text-muted me-2">{{ __('Previous') }}</small>
                                        <small style="color: #333;">1 - <span class="total-bills-count">0</span>
                                            {{ __('of') }} <span class="total-bills-count">0</span></small>
                                        <small class="text-muted ms-2">{{ __('Next') }}</small>
                                        <small class="text-muted ms-2">{{ __('Last') }}</small>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                {{-- QBO Style Footer --}}
                <div class="modal-footer border-top d-flex justify-content-between align-items-center"
                    style="background: #fff; padding: 16px 24px;">
                    <a href="#" class="text-success text-decoration-none" data-bs-dismiss="modal"
                        style="font-size: 14px;">{{ __('Cancel') }}</a>
                    <div class="btn-group">
                        <button type="submit" id="modal-proceed-payment" class="btn btn-success px-4"
                            style="background-color: #2ca01c; border-color: #2ca01c; font-size: 14px;">
                            {{ __('Schedule payment') }}
                        </button>
                        <button type="button" class="btn btn-success dropdown-toggle dropdown-toggle-split"
                            data-bs-toggle="dropdown" aria-expanded="false"
                            style="background-color: #2ca01c; border-color: #2ca01c;">
                            <span class="visually-hidden">Toggle Dropdown</span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#">{{ __('Pay now') }}</a></li>
                            <li><a class="dropdown-item" href="#">{{ __('Print checks') }}</a></li>
                        </ul>
                    </div>
                </div>
                {{ Form::close() }}
                {{-- FORM END --}}
            </div>
        </div>
    </div>

    {{-- Modal for Create Forms --}}
    <div class="modal fade" id="ajaxModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-fullscreen">
            <div class="modal-content">
            </div>
        </div>
    </div>
@endsection

@push('script-page')
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script>
        var expenseTable;
        var currentTxnType = '{{ $type ?? 'all' }}';

        $(document).ready(function() {
            // Initialize DataTable - client-side (no AJAX)
            expenseTable = $('#expenseTable').DataTable({
                processing: false,
                serverSide: false,
                order: [
                    [1, 'desc']
                ],
                pageLength: 50,
                lengthMenu: [
                    [50, 75, 100, 150, 300],
                    [50, 75, 100, 150, 300]
                ],
                dom: 'rt', // Hide default pagination, we use custom
                retrieve: true,
                destroy: true,
                language: {
                    emptyTable: "{{ __('No transactions found.') }}",
                },
                drawCallback: function(settings) {
                    var api = this.api();
                    updatePagination(api);
                    $('[data-bs-toggle="tooltip"]').tooltip();
                }
            });

            // Update pagination info
            function updatePagination(api) {
                if (!api) return;
                var pageInfo = api.page.info();
                var start = pageInfo.recordsTotal > 0 ? pageInfo.start + 1 : 0;
                var end = pageInfo.end;
                var total = pageInfo.recordsTotal;

                $('#paginateInfo').text(start + '-' + end + ' of ' + total);

                // Enable/disable buttons
                $('#paginateFirst, #paginatePrev').prop('disabled', pageInfo.page === 0);
                $('#paginateNext, #paginateLast').prop('disabled', pageInfo.page >= pageInfo.pages - 1);
            }

            // Custom pagination buttons
            $('#paginateFirst').on('click', function() {
                expenseTable.page('first').draw('page');
            });
            $('#paginatePrev').on('click', function() {
                expenseTable.page('previous').draw('page');
            });
            $('#paginateNext').on('click', function() {
                expenseTable.page('next').draw('page');
            });
            $('#paginateLast').on('click', function() {
                expenseTable.page('last').draw('page');
            });

            // Select all checkbox
            $('#selectAllHeader').on('change', function() {
                var checked = this.checked;
                $('.row-checkbox').each(function() {
                    this.checked = checked;
                });
            });
        });

        // Toggle New Transaction Dropdown
        function toggleNewTxnDropdown() {
            document.querySelector('.qbo-new-txn-dropdown').classList.toggle('show');
        }

        // Toggle Filter Dropdown
        function toggleFilterDropdown(id) {
            document.getElementById(id).classList.toggle('show');
        }

        // Filter dropdown item selection - reload page with new filter
        document.querySelectorAll('.qbo-filter-dropdown-item').forEach(item => {
            item.addEventListener('click', function() {
                const dropdown = this.closest('.qbo-filter-dropdown');
                dropdown.querySelectorAll('.qbo-filter-dropdown-item').forEach(i => i.classList.remove(
                    'active'));
                this.classList.add('active');
                dropdown.querySelector('.qbo-filter-btn span').textContent = this.textContent;
                dropdown.classList.remove('show');

                // Reload page with new filter
                var txnType = this.dataset.value;
                var currentUrl = new URL(window.location.href);
                currentUrl.searchParams.set('txn_type', txnType);
                window.location.href = currentUrl.toString();
            });
        });

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.qbo-new-txn-dropdown')) {
                document.querySelector('.qbo-new-txn-dropdown')?.classList.remove('show');
            }
            if (!e.target.closest('.qbo-filter-dropdown')) {
                document.querySelectorAll('.qbo-filter-dropdown').forEach(d => d.classList.remove('show'));
            }
        });

        // Filter Modal
        function openFilterModal() {
            document.getElementById('filterModal').classList.add('show');
        }

        function closeFilterModal() {
            document.getElementById('filterModal').classList.remove('show');
        }

        // Select All checkbox
        document.getElementById('selectAll')?.addEventListener('change', function() {
            document.querySelectorAll('.row-checkbox').forEach(cb => {
                cb.checked = this.checked;
            });
        });

        // AJAX Modal for create forms
        $(document).on('click', '.openChecksModal', function(e) {
            e.preventDefault();
            var url = $(this).data('url');
            $('#ajaxModal').modal('show');
            $.ajax({
                url: url,
                type: 'GET',
                success: function(res) {
                    $('#ajaxModal .modal-content').html(res);
                },
                error: function() {
                    alert('Something went wrong!');
                }
            });
        });

        // Coming Soon toast
        function showComingSoon() {
            show_toastr('info', 'Coming soon!', 'info');
        }
        // ── Activity panel ──────────────────────────────────────
        $(document).on('click', '.txn-activity-link', function(e) {
            e.preventDefault();
            var url = $(this).data('url');
            var offcanvas = new bootstrap.Offcanvas(document.getElementById('txnActivityOffcanvas'));
            $('#txnActivityContent').html(
                '<div class="text-center p-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div></div>'
            );
            offcanvas.show();
            fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(function(r) {
                    return r.text();
                })
                .then(function(html) {
                    document.getElementById('txnActivityContent').innerHTML = html;
                })
                .catch(function() {
                    $('#txnActivityContent').html(
                        '<div class="text-center text-danger p-5">{{ __('Failed to load.') }}</div>');
                });
        });

        // ── Delete ───────────────────────────────────────────────
        $(document).on('click', '.txn-delete-link', function(e) {
            e.preventDefault();
            if (!confirm('{{ __('Are you sure you want to delete this transaction?') }}')) return;
            var url = $(this).data('url');
            var $form = $('<form method="POST"></form>').attr('action', url);
            $form.append('<input type="hidden" name="_token" value="{{ csrf_token() }}">');
            $form.append('<input type="hidden" name="_method" value="DELETE">');
            $('body').append($form);
            $form.submit();
        });
    </script>
    <script>
        $(document).ready(function() {


            // copy link handler from your existing code
            $('.copy_link').click(function(e) {
                e.preventDefault();
                var copyText = $(this).attr('href');

                document.addEventListener('copy', function(e) {
                    e.clipboardData.setData('text/plain', copyText);
                    e.preventDefault();
                }, true);

                document.execCommand('copy');
                show_toastr('success', 'Url copied to clipboard', 'success');
            });

            // ----- MAIN TABLE CHECKBOX HANDLING -----
            // header select all checkbox toggles row checkboxes
            $(document).on('change', '#select-all-bills', function() {
                var checked = $(this).is(':checked');
                $('.bill-row-checkbox').prop('checked', checked);
            });

            // if any row checkbox changes: toggle header checkbox accordingly
            $(document).on('change', '.bill-row-checkbox', function() {
                var total = $('.bill-row-checkbox').length;
                var checked = $('.bill-row-checkbox:checked').length;
                $('#select-all-bills').prop('checked', total === checked);
            });

            // ----- OPEN PAYMENT MODAL -----
            $('#open-pay-modal').on('click', function(e) {
                e.preventDefault();

                // collect selected bill ids from main table
                var selectedIds = [];
                $('.bill-row-checkbox:checked').each(function() {
                    selectedIds.push($(this).data('bill-id').toString());
                });

                // clear modal table
                var $modalTableBody = $('#payModalTable tbody');
                $modalTableBody.empty();

                // iterate through original table rows and populate modal rows
                $('.bills-main-row').each(function() {
                    var $orig = $(this);
                    var openBalance = parseFloat($orig.data('bill-due')) || 0;

                    // Skip fully paid bills (Open Balance must be > 0)
                    if (openBalance <= 0) return;

                    var billId = $orig.data('bill-id').toString();
                    var billNumber = $orig.find('.bill-number').text().trim();
                    var vendorName = $orig.find('.bill-vendor').text().trim();
                    var dueDate = $orig.find('.bill-due-date').text().trim();
                    var statusHtml = $orig.find('.bill-status').html();

                    // Check if bill is overdue
                    var isOverdue = statusHtml && (statusHtml.toLowerCase().includes('overdue') ||
                        statusHtml.toLowerCase().includes('unpaid') ||
                        statusHtml.toLowerCase().includes('partial'));

                    // Status display QBO style
                    var statusDisplay = isOverdue ?
                        '<span style="color: #d9534f; font-weight: 500;">Overdue</span><br><small style="color: #999; font-size: 11px;">days ago</small>' :
                        statusHtml;

                    // default selected if was checked on main table
                    var selected = selectedIds.indexOf(billId) !== -1;
                    var checkedAttr = selected ? 'checked' : '';
                    var paymentValue = selected ? openBalance.toFixed(2) : '0.00';
                    var totalValue = selected ? '$' + openBalance.toFixed(2) : '$0.00';

                    // QBO Style row
                    var tr = '<tr data-bill-id="' + billId + '" data-bill-amount="' + openBalance +
                        '" data-bill-due="' + openBalance +
                        '" style="border-bottom: 1px solid #e9ecef;">' +
                        '<td class="text-center align-middle" style="padding: 12px 8px;">' +
                        '<input name="bill_ids[]" value="' + billId +
                        '" type="checkbox" class="modal-row-checkbox form-check-input" ' +
                        checkedAttr + ' style="cursor: pointer;">' +
                        '</td>' +
                        '<td class="align-middle" style="padding: 12px 8px; color: #333;">' +
                        vendorName + '</td>' +
                        '<td class="align-middle" style="padding: 12px 8px; color: #333;">' +
                        billNumber + '</td>' +
                        '<td class="align-middle" style="padding: 12px 8px; color: #333;">' +
                        dueDate + '</td>' +
                        '<td class="align-middle bill-status-display" style="padding: 12px 8px;">' +
                        statusDisplay + '</td>' +
                        '<td class="align-middle text-end bill-open-col" style="padding: 12px 8px; color: #333;">$' +
                        openBalance.toFixed(2) + '</td>' +
                        '<td class="align-middle text-center" style="padding: 12px 8px; color: #999;">Not available</td>' +
                        '<td class="align-middle text-center payment-col" style="padding: 12px 8px;">' +
                        '<input type="number" step="0.01" min="0" class="form-control form-control-sm payment-input text-center" ' +
                        'value="' + paymentValue + '" name="payment_amounts[' + billId +
                        ']" style="width: 90px; margin: 0 auto; border-color: #c0c0c0;">' +
                        '</td>' +
                        '<td class="align-middle text-end total-col" style="padding: 12px 8px; color: #333;">' +
                        totalValue + '</td>' +
                        '</tr>';

                    $modalTableBody.append(tr);
                });

                // Update bill count for pagination
                var billCount = $('#payModalTable tbody tr').length;
                $('.total-bills-count').text(billCount);

                // recalc totals and UI in modal
                recalcModalTotals();

                // show modal
                var payModal = new bootstrap.Modal(document.getElementById('payBillModal'), {
                    backdrop: 'static',
                    keyboard: false
                });
                payModal.show();
            });

            // ----- MODAL: checkbox and payment input logic -----
            // when modal checkbox toggled, if checked set payment input to openBalance, if unchecked set payment input to 0
            $(document).on('change', '.modal-row-checkbox', function() {
                var $tr = $(this).closest('tr');
                var openBal = parseFloat($tr.data('bill-due')) || 0;
                var $paymentInput = $tr.find('.payment-input');

                if ($(this).is(':checked')) {
                    // select row: set payment default to open balance
                    $paymentInput.val(openBal.toFixed(2));
                    $tr.find('.total-col').text('$' + openBal.toFixed(2));
                } else {
                    // unselect row: set payment to 0 and total to 0
                    $paymentInput.val('0.00');
                    $tr.find('.total-col').text('$0.00');
                }

                recalcModalTotals();
            });

            // when payment input changes
            $(document).on('input', '.payment-input', function() {
                var $tr = $(this).closest('tr');
                var val = parseFloat($(this).val()) || 0;
                var total = parseFloat($tr.data('bill-due')) || 0;

                // Sync checkbox state with input value
                var $checkbox = $tr.find('.modal-row-checkbox');
                if (val > 0) {
                    $checkbox.prop('checked', true);
                } else {
                    $checkbox.prop('checked', false);
                }
                updateModalSelectAllState();

                // if entered amount greater than total: reduce to total
                if (val > total) {
                    val = total;
                    $(this).val(total.toFixed(2));
                }

                // update total-col: payment value is added in total column per spec
                var isChecked = $checkbox.is(':checked');
                if (isChecked) {
                    $tr.find('.total-col').text('$' + (parseFloat($tr.data('bill-due')) || 0).toFixed(2));
                } else {
                    $tr.find('.total-col').text('$0.00');
                }

                // show partially paid label if payment < total (and payment > 0)
                if (val > 0 && val < total) {
                    $tr.find('.partial-label-container').html(
                        '<span class="badge bg-warning small ms-2">Partially Paid</span>');
                } else {
                    $tr.find('.partial-label-container').empty();
                }

                recalcModalTotals();
            });


            // recalc total display (modal top-right and footer total row)
            function recalcModalTotals() {
                var grandPayment = 0.00;
                var grandOpenBalance = 0.00;
                var grandTotal = 0.00;

                $('#payModalTable tbody tr').each(function() {
                    var $tr = $(this);
                    var isChecked = $tr.find('.modal-row-checkbox').is(':checked');
                    var pay = parseFloat($tr.find('.payment-input').val()) || 0;
                    var openBal = parseFloat($tr.data('bill-due')) || 0;

                    grandPayment += pay;
                    if (isChecked) {
                        grandOpenBalance += openBal;
                        grandTotal += openBal;
                    }
                });

                // top right big total (with $ prefix)
                $('.grand-total-value').text(grandPayment.toFixed(2));

                // footer totals with $ prefix
                $('.modal-footer-open-balance').text('$' + grandOpenBalance.toFixed(2));
                $('.modal-footer-credit').text('$0.00');
                $('.modal-footer-payment').text('$0.00');
                $('.modal-footer-total').text('$' + grandPayment.toFixed(2));
            }

            // date filter behavior (in-modal) - this just filters rows client-side by bill date substring
            $(document).on('input', '#modal-date-filter', function() {
                var filter = $(this).val().toLowerCase().trim();
                $('#payModalTable tbody tr').each(function() {
                    var billDate = $(this).find('td:nth-child(4)').text().toLowerCase();
                    if (billDate.indexOf(filter) !== -1 || filter === '') {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });

            // Handle "Select All" inside the modal
            $(document).on('change', '#modalSelectAll', function() {
                const checked = $(this).is(':checked');
                $('.modal-row-checkbox').prop('checked', checked).trigger('change');
                console.log("AAL", $('.modal-row-checkbox'))
            });

            function updateModalSelectAllState() {
                const totalRows = $('.modal-row-checkbox').length;
                const selectedRows = $('.modal-row-checkbox:checked').length;
                $('#modalSelectAll').prop('checked', totalRows === selectedRows);
            }
            // When individual modal checkboxes change, update Select All state
            $(document).on('change', '.modal-row-checkbox', updateModalSelectAllState);

            $(document).on('change', '.item-product', function() {
                const productId = $(this).val();
                const currentRow = $(this).closest('tr');

                if (!productId) return;

                $.ajax({
                    url: '{{ route('bill.product') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        product_id: productId
                    },
                    success: function(response) {
                        // Parse JSON if it's a string
                        const data = typeof response === 'string' ? JSON.parse(response) :
                            response;

                        if (data.product) {
                            // Get description (or name if description doesn't exist)
                            const description = data.product.description || data.product.name ||
                                '';
                            currentRow.find('.item-description').val(description);

                            // Get purchase price
                            const rate = parseFloat(data.product.purchase_price) || 0;
                            currentRow.find('.item-rate').val(rate.toFixed(2));

                            // Set quantity to 1
                            const qty = 1;
                            currentRow.find('.item-qty').val(qty);

                            // Calculate amount (qty × rate)
                            const amount = qty * rate;
                            currentRow.find('.item-amount').val(amount.toFixed(2));

                            // Recalculate subtotal and grand total
                            calculateBillTotal();
                        }
                    },
                    error: function(xhr) {
                        console.error('Error fetching product:', xhr);
                        alert('Failed to load product details. Please try again.');
                    }
                });
            });

            // Function to calculate bill totals
            function calculateBillTotal() {
                let subtotal = 0;

                // Sum all category amounts
                $('.category-amount').each(function() {
                    subtotal += parseFloat($(this).val()) || 0;
                });

                // Sum all item amounts
                $('.item-amount').each(function() {
                    subtotal += parseFloat($(this).val()) || 0;
                });

                // Update displays
                $('#subtotal').val(subtotal.toFixed(2));
                $('#total').val(subtotal.toFixed(2));
                $('#subtotal-display').text('$' + subtotal.toFixed(2));
                $('#total-display').text('$' + subtotal.toFixed(2));
                $('#grand-total-display').text('$' + subtotal.toFixed(2));
            }

            // Recalculate when qty or rate changes
            $(document).on('input', '.item-qty, .item-rate', function() {
                const row = $(this).closest('tr');
                const qty = parseFloat(row.find('.item-qty').val()) || 0;
                const rate = parseFloat(row.find('.item-rate').val()) || 0;
                row.find('.item-amount').val((qty * rate).toFixed(2));
                calculateBillTotal();
            });

            // Recalculate when category amount changes
            $(document).on('input', '.category-amount', function() {
                calculateBillTotal();
            });
            // modal proceed payment button - does nothing (explicit per requirements)
            $('#modal-proceed-payment').on('click', function(e) {
                // e.preventDefault();
                // intentionally no-op
                // You can read values now and send to server when you build backend endpoint
            });

            // ensure when modal closed, clear date filter
            $('#payBillModal').on('hidden.bs.modal', function() {
                $('#modal-date-filter').val('');
            });

            $('#bulkPaymentForm').on('submit', function(e) {
                if ($('.modal-row-checkbox:checked').length === 0) {
                    e.preventDefault();
                    alert('Please select at least one bill before proceeding.');
                }
            });


            //remove datatable sorter from select all a
            $('#select-all-bills').closest('a').removeClass('dataTable-sorter');

            // Receive Bill Payment button - navigate to bill payment create page
            $('#open-receive-modal').on('click', function(e) {
                e.preventDefault();
                window.location.href = '{{ route('receive-bill-payment.create') }}';
            });

        });
    </script>
@endpush
