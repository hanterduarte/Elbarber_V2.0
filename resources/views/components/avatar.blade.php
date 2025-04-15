@props(['src' => null, 'alt' => null, 'size' => 'md'])

@php
    $sizeClasses = match($size) {
        'xs' => 'w-6 h-6',
        'sm' => 'w-8 h-8',
        'md' => 'w-10 h-10',
        'lg' => 'w-12 h-12',
        'xl' => 'w-14 h-14',
        default => 'w-10 h-10'
    };
@endphp

@if($src)
    <img {{ $attributes->merge(['class' => "{$sizeClasses} rounded-full object-cover", 'src' => $src, 'alt' => $alt]) }}>
@else
    <div {{ $attributes->merge(['class' => "{$sizeClasses} rounded-full bg-gray-200 flex items-center justify-center"]) }}>
        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
            <path d="M24 20.993V24H0v-2.996A14.977 14.977 0 0112.004 15c4.904 0 9.26 2.354 11.996 5.993zM16.002 8.999a4 4 0 11-8 0 4 4 0 018 0z" />
        </svg>
    </div>
@endif 