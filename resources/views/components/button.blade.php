@props(['type' => 'primary', 'size' => 'md'])

@php
    $baseClasses = 'inline-flex items-center justify-center font-medium rounded-md focus:outline-none transition duration-150 ease-in-out';
    
    $sizeClasses = match($size) {
        'xs' => 'px-2.5 py-1.5 text-xs',
        'sm' => 'px-3 py-2 text-sm leading-4',
        'md' => 'px-4 py-2 text-sm',
        'lg' => 'px-4 py-2 text-base',
        'xl' => 'px-6 py-3 text-base',
        default => 'px-4 py-2 text-sm'
    };

    $typeClasses = match($type) {
        'primary' => 'border border-transparent text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500',
        'secondary' => 'border border-transparent text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500',
        'danger' => 'border border-transparent text-white bg-red-600 hover:bg-red-700 focus:ring-2 focus:ring-offset-2 focus:ring-red-500',
        'warning' => 'border border-transparent text-white bg-yellow-600 hover:bg-yellow-700 focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500',
        'success' => 'border border-transparent text-white bg-green-600 hover:bg-green-700 focus:ring-2 focus:ring-offset-2 focus:ring-green-500',
        'white' => 'border border-gray-300 text-gray-700 bg-white hover:bg-gray-50 focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500',
        default => 'border border-transparent text-white bg-indigo-600 hover:bg-indigo-700 focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500'
    };

    $classes = "{$baseClasses} {$sizeClasses} {$typeClasses}";

    $attributes = $attributes->class([
        'opacity-50 cursor-not-allowed' => $attributes->get('disabled'),
        $classes => true,
    ]);
@endphp

<button {{ $attributes }}>
    {{ $slot }}
</button> 