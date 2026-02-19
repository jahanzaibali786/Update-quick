@extends('layouts.admin')
@section('page-title')
    {{ __('Credit Memo') }}
@endsection
@section('breadcrumb')
    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">{{ __('Dashboard') }}</a></li>
    <li class="breadcrumb-item">{{ __('Credit Memo') }}</li>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-body table-border-style">
                    <div class="table-responsive">
                        <table class="table datatable">
                            <thead>
                                <tr>
                                    <th>{{ __('Credit Memo ID') }}</th>
                                    <th>{{ __('Customer') }}</th>
                                    <th>{{ __('Date') }}</th>
                                    <th>{{ __('Amount') }}</th>
                                    <th>{{ __('Description') }}</th>
                                    <th width="10%">{{ __('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($creditMemos as $creditMemo)
                                    <tr>
                                        <td>{{ Auth::user()->invoiceNumberFormat($creditMemo->credit_note_id) }}</td>
                                        <td>{{ !empty($creditMemo->customer_detail) ? $creditMemo->customer_detail->name : '-' }}
                                        </td>
                                        <td>{{ Auth::user()->dateFormat($creditMemo->date) }}</td>
                                        <td>{{ Auth::user()->priceFormat($creditMemo->amount) }}</td>
                                        <td>{{ $creditMemo->description }}</td>
                                        <td class="Action">
                                            <span>
                                                @can('edit invoice')
                                                    <div class="action-btn bg-info ms-2">
                                                        <a href="{{ route('creditmemo.edit', $creditMemo->id) }}"
                                                            class="mx-3 btn btn-sm align-items-center" data-bs-toggle="tooltip"
                                                            title="{{ __('Edit') }}"
                                                            data-original-title="{{ __('Edit') }}">
                                                            <i class="ti ti-pencil text-white"></i>
                                                        </a>
                                                    </div>
                                                @endcan
                                                @can('delete invoice')
                                                    <div class="action-btn bg-danger ms-2">
                                                        {!! Form::open([
                                                            'method' => 'DELETE',
                                                            'route' => ['creditmemo.destroy', $creditMemo->id],
                                                            'id' => 'delete-form-' . $creditMemo->id,
                                                        ]) !!}
                                                        <a href="#"
                                                            class="mx-3 btn btn-sm align-items-center bs-pass-para"
                                                            data-bs-toggle="tooltip" title="{{ __('Delete') }}"
                                                            data-original-title="{{ __('Delete') }}"
                                                            data-confirm="{{ __('Are You Sure?') . '|' . __('This action can not be undone. Do you want to continue?') }}"
                                                            data-confirm-yes="delete-form-{{ $creditMemo->id }}">
                                                            <i class="ti ti-trash text-white"></i>
                                                        </a>
                                                        {!! Form::close() !!}
                                                    </div>
                                                @endcan
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-12">
            <div class="text-end mb-3">
                <a href="{{ route('creditmemo.create', 0) }}" class="btn btn-primary">
                    <i class="ti ti-plus"></i> {{ __('Create Credit Memo') }}
                </a>
            </div>
        </div>
    </div>
@endsection
