@props(['type' => 'primary'])

@php
    $classes = match($type) {
        'primary' => 'bg-indigo-100 text-indigo-800',
        'secondary' => 'bg-gray-100 text-gray-800',
        'success' => 'bg-green-100 text-green-800',
        'danger' => 'bg-red-100 text-red-800',
        'warning' => 'bg-yellow-100 text-yellow-800',
        'info' => 'bg-blue-100 text-blue-800',
        default => 'bg-gray-100 text-gray-800'
    };
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {$classes}"]) }}>
    {{ $slot }}
</span> 