@props(['striped' => false, 'index' => null])

@php
    $classes = $striped && $index !== null
        ? $index % 2 === 0 ? 'bg-white' : 'bg-gray-50'
        : 'hover:bg-gray-50';
@endphp

<tr {{ $attributes->merge(['class' => $classes]) }}>
    {{ $slot }}
</tr> 