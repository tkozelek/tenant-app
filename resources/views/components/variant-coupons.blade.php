@props(['variant'])

@if($variant->relationLoaded('activeCoupons') && $variant->activeCoupons->isNotEmpty())
    <div class="flex flex-col gap-0.5">
        @foreach($variant->activeCoupons as $coupon)
            <span class="text-xs text-yellow-400 font-mono">{{ $coupon->code }}</span>
            <span class="text-xs text-neutral-500">
                {{ $coupon->discount_type === 'percentage' ? number_format($coupon->value).'%' : number_format($coupon->value, 2, ',', ' ').' €' }}
                @if($coupon->min_order_amount) min. {{ number_format($coupon->min_order_amount, 2, ',', ' ') }} € @endif
            </span>
        @endforeach
    </div>
@else
    <span class="text-neutral-700">—</span>
@endif
