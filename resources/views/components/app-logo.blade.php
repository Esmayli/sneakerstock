@props([
    'sidebar' => false,
])

@php
    $brandLogo = 'images/brand/logo.png';
    $hasBrandLogo = is_file(public_path($brandLogo));
    $brandName = $hasBrandLogo ? '' : 'SneakerStock';
    $brandLogoClass = $hasBrandLogo ? 'sneaker-brand-mark sneaker-brand-mark--custom' : 'sneaker-brand-mark';
@endphp

@if($sidebar)
    <flux:sidebar.brand :name="$brandName" {{ $attributes }}>
        <x-slot name="logo" class="{{ $brandLogoClass }}">
            @if ($hasBrandLogo)
                <img src="{{ asset($brandLogo) }}" alt="SneakerStock">
            @else
                <span aria-hidden="true">S</span>
            @endif
        </x-slot>
    </flux:sidebar.brand>
@else
    <flux:brand :name="$brandName" {{ $attributes }}>
        <x-slot name="logo" class="{{ $brandLogoClass }}">
            @if ($hasBrandLogo)
                <img src="{{ asset($brandLogo) }}" alt="SneakerStock">
            @else
                <span aria-hidden="true">S</span>
            @endif
        </x-slot>
    </flux:brand>
@endif
