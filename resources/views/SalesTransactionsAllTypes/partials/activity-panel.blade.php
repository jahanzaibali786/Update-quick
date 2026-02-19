<div style="font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;min-height:100%;">

    {{-- Header --}}
    <div style="display:flex;justify-content:space-between;align-items:center;padding:16px 20px;border-bottom:1px solid #e0e3e5;position:sticky;top:0;background:#fff;z-index:10;">
        <h5 style="margin:0;font-weight:700;font-size:16px;color:#393a3d;">{{ $title }}</h5>
        <button type="button"
                onclick="bootstrap.Offcanvas.getInstance(document.getElementById('txnActivityOffcanvas')).hide()"
                style="background:none;border:none;cursor:pointer;color:#6b6c72;font-size:24px;line-height:1;padding:0;"
                title="{{ __('Close') }}">&#x00D7;</button>
    </div>

    {{-- Amount block --}}
    <div style="padding:20px 20px 16px;">
        @if(!empty($subtitle))
            <div style="font-size:13px;color:#6b6c72;margin-bottom:8px;">{{ $subtitle }}</div>
        @endif
        <div style="font-size:12px;color:#6b6c72;font-weight:500;margin-bottom:2px;">{{ __('Total due') }}</div>
        <div style="font-size:36px;font-weight:700;color:#393a3d;margin-bottom:16px;">
            {{ Auth::user()->priceFormat(abs($total)) }}
        </div>
        @if(!empty($issue_date))
        <div style="margin-bottom:10px;">
            <div style="font-size:12px;color:#6b6c72;font-weight:500;">{{ __('Date') }}</div>
            <div style="font-size:14px;font-weight:600;color:#393a3d;">{{ \Carbon\Carbon::parse($issue_date)->format('n/j/Y') }}</div>
        </div>
        @endif
        @if(!empty($due_date))
        <div style="margin-bottom:10px;">
            <div style="font-size:12px;color:#6b6c72;font-weight:500;">{{ __('Due date') }}</div>
            <div style="font-size:14px;font-weight:600;color:#393a3d;">{{ \Carbon\Carbon::parse($due_date)->format('n/j/Y') }}</div>
        </div>
        @endif
    </div>

    {{-- Customer --}}
    @if($customer)
    <hr style="margin:0;border-color:#e0e3e5;">
    <div style="padding:16px 20px;">
        <div onclick="actPanelToggle('apc_customer')"
             style="display:flex;justify-content:space-between;align-items:center;cursor:pointer;">
            <strong style="font-size:15px;color:#393a3d;">
                {{ $customer->name ?? $customer->company_name ?? __('Customer') }}
            </strong>
            <span id="apc_customer_arrow" style="color:#6b6c72;display:inline-block;transition:transform .2s;">&#9660;</span>
        </div>
        <div id="apc_customer" style="margin-top:12px;">
            @if(!empty($customer->billing_address))
            <div style="font-size:13px;margin-bottom:10px;">
                <div style="font-weight:700;font-size:12px;color:#393a3d;margin-bottom:4px;">{{ __('Billing address') }}</div>
                @if(!empty($customer->billing_name))
                    <div>{{ $customer->billing_name }}</div>
                @endif
                @if(!empty($customer->company_name))
                    <div>{{ $customer->company_name }}</div>
                @endif
                <div>{{ $customer->billing_address }}</div>
                @php
                    $cityLine = implode(', ', array_filter([
                        $customer->billing_city ?? '',
                        trim(($customer->billing_state ?? '') . '  ' . ($customer->billing_zip ?? '')),
                    ]));
                @endphp
                @if($cityLine)
                    <div>{{ $cityLine }}</div>
                @endif
            </div>
            @endif
            @if(!empty($customer->email))
            <div style="margin-bottom:8px;">
                <a href="mailto:{{ $customer->email }}" style="color:#0077c5;font-size:13px;text-decoration:none;">
                    {{ $customer->email }}
                </a>
            </div>
            @endif
            @if(!empty($customer->contact))
            <div style="font-size:13px;color:#393a3d;">
                {{ __('Phone:') }} <span style="color:#0077c5;">{{ $customer->contact }}</span>
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- Activity Timeline --}}
    @if(!empty($activities))
    <hr style="margin:0;border-color:#e0e3e5;">
    <div style="padding:16px 20px;">
        <div onclick="actPanelToggle('apc_activity')"
             style="display:flex;justify-content:space-between;align-items:center;cursor:pointer;margin-bottom:12px;">
            <strong style="font-size:15px;color:#393a3d;">{{ __('Invoice activity') }}</strong>
            <span id="apc_activity_arrow" style="color:#6b6c72;display:inline-block;transition:transform .2s;">&#9660;</span>
        </div>
        <div id="apc_activity">
            <div style="position:relative;padding-left:28px;">
               <style>
                    .act-line{position:absolute;left:7px;top:10px;bottom:10px;width:2px;background:#d0d0d0;}
                    .act-item{position:relative;margin-bottom:24px;}
                    .act-dot{position:absolute;left:-21px;top:3px;width:16px;height:16px;border-radius:50%;box-sizing:border-box;z-index:1;}
                    .act-dot.done{background:#2ca01c;border:2px solid #2ca01c;}
                    .act-dot.pending{background:#fff;border:2px solid #c0c0c0;}
                    .act-label{font-size:14px;}
                    .act-label.done{font-weight:700;color:#393a3d;}
                    .act-label.pending{font-weight:400;color:#9ba0a8;}
                    .act-date{font-size:12px;color:#6b6c72;margin-top:2px;}
                </style>
                <div class="act-line"></div>

                @foreach($activities as $act)
                @php $state = $act['done'] ? 'done' : 'pending'; @endphp
                <div class="act-item">
                    <div class="act-dot {{ $state }}"></div>
                    <div class="act-label {{ $state }}">{{ $act['label'] }}</div>
                    @if(!empty($act['date']))
                    <div class="act-date">{{ $act['date'] }}</div>
                    @endif
                </div>
                @endforeach

            </div>
        </div>
    </div>
    @endif

    {{-- Products and services --}}
    @if(isset($products) && (is_object($products) ? $products->count() : count($products)) > 0)
    <hr style="margin:0;border-color:#e0e3e5;">
    <div style="padding:16px 20px;">
        <div onclick="actPanelToggle('apc_products')"
             style="display:flex;justify-content:space-between;align-items:center;cursor:pointer;margin-bottom:12px;">
            <strong style="font-size:15px;color:#393a3d;">{{ __('Products and services') }}</strong>
            <span id="apc_products_arrow" style="color:#6b6c72;display:inline-block;transition:transform .2s;">&#9660;</span>
        </div>
        <div id="apc_products">
            @foreach($products as $prod)
            <div style="display:flex;justify-content:space-between;margin-bottom:8px;font-size:14px;color:#393a3d;">
                <span>{{ $prod->product_name ?? ($prod->description ?? '') }}</span>
                <span>{{ Auth::user()->priceFormat($prod->amount ?? ($prod->price ?? 0)) }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Footer --}}
    <hr style="margin:0;border-color:#e0e3e5;">
    <div style="padding:16px 20px;display:flex;justify-content:flex-end;gap:10px;">
        @if(!empty($edit_url))
        <a href="{{ $edit_url }}"
           style="display:inline-flex;align-items:center;padding:10px 20px;background:#2ca01c;color:#fff;border-radius:4px;font-size:14px;font-weight:600;text-decoration:none;">
            {{ __('Edit') }}
        </a>
        @endif
    </div>

</div>

<script>
function actPanelToggle(id) {
    var el    = document.getElementById(id);
    var arrow = document.getElementById(id + '_arrow');
    if (!el) return;
    var hidden = el.style.display === 'none';
    el.style.display = hidden ? 'block' : 'none';
    if (arrow) arrow.style.transform = hidden ? '' : 'rotate(180deg)';
}
</script>