@props(['disabled' => false, 'error' => false])

@php
$classes = 'border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm';

if ($error) {
    $classes = 'border-red-300 focus:border-red-500 focus:ring-red-500 rounded-md shadow-sm';
}
@endphp

<select {{ $disabled ? 'disabled' : '' }} {!! $attributes->merge(['class' => $classes]) !!}>
    {{ $slot }}
</select> 