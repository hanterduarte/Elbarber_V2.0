@props(['striped' => false])

<tbody {{ $attributes->merge(['class' => 'bg-white divide-y divide-gray-200' . ($striped ? ' divide-y-0' : '')]) }}>
    {{ $slot }}
</tbody> 