<x-dynamic-component :component="$getEntryWrapperView()" :entry="$entry">
    @if ($getState())
        <div class="mt-1">
            <img src="{{ $getState() }}" class="max-h-24 w-auto object-contain bg-white border border-gray-200 rounded-lg p-1 shadow-sm" alt="Signature" />
        </div>
    @else
        <span class="text-sm text-gray-500 italic">Tidak ada</span>
    @endif
</x-dynamic-component>
