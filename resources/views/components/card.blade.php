@props(['title' => null, 'footer' => null])

<div {{ $attributes->merge(['class' => 'bg-white overflow-hidden shadow-sm sm:rounded-lg']) }}>
    @if($title)
        <div class="p-6 border-b border-gray-200">
            <h3 class="text-lg font-medium text-gray-900">{{ $title }}</h3>
        </div>
    @endif

    <div class="p-6">
        {{ $slot }}
    </div>

    @if($footer)
        <div class="p-6 bg-gray-50 border-t border-gray-200">
            {{ $footer }}
        </div>
    @endif
</div> 