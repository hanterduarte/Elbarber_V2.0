@props(['header' => false])

@php
    $classes = $header
        ? 'px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider'
        : 'px-6 py-4 whitespace-nowrap text-sm text-gray-500';
@endphp

<td {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</td> 