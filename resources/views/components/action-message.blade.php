@props(['on' => null])

<div
    x-data="{ show: false }"
    x-show="show"
    x-init="
        @this.on('{{ $on }}', () => {
            show = true;
            setTimeout(() => { show = false }, 2000);
        })
    "
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 transform translate-x-2"
    x-transition:enter-end="opacity-100 transform translate-x-0"
    x-transition:leave="transition ease-in duration-300"
    x-transition:leave-start="opacity-100 transform translate-x-0"
    x-transition:leave-end="opacity-0 transform translate-x-2"
    class="fixed top-0 right-0 m-6 p-4 bg-white rounded-lg shadow-lg"
    style="display: none;"
>
    <div class="flex items-center">
        <div class="flex-shrink-0">
            <svg class="h-6 w-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
        </div>
        <div class="ml-3">
            <p class="text-sm font-medium text-gray-900">
                {{ $slot }}
            </p>
        </div>
    </div>
</div> 