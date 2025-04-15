@props(['label' => null, 'error' => null, 'help' => null])

<div class="space-y-1">
    @if($label)
        <label class="block text-sm font-medium text-gray-700">
            {{ $label }}
        </label>
    @endif

    {{ $slot }}

    @if($error)
        <p class="mt-1 text-sm text-red-600">{{ $error }}</p>
    @endif

    @if($help)
        <p class="mt-1 text-sm text-gray-500">{{ $help }}</p>
    @endif
</div> 