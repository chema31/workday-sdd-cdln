@props(['hasOpenRecord' => false])

<div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 max-w-xs">
    <div class="flex items-center justify-between mb-4">
        <span class="text-sm font-medium text-gray-500">Fichaje</span>
        <span class="text-gray-400 text-sm">&rsaquo;</span>
    </div>

    <div class="flex items-center gap-2 mb-4">
        @if ($hasOpenRecord)
            <span class="w-2.5 h-2.5 rounded-full bg-accom-teal animate-pulse"></span>
            <span class="text-accom-teal font-semibold text-sm">Fichado</span>
        @else
            <span class="w-2.5 h-2.5 rounded-full bg-gray-300"></span>
            <span class="text-gray-400 text-sm">Sin fichar</span>
        @endif
    </div>

    {{-- Placeholder: the POST action is wired in US-002. --}}
    @if ($hasOpenRecord)
        <button type="button"
            class="bg-accom-pink text-white rounded-lg px-5 py-2.5 font-medium text-sm hover:bg-pink-700 transition">
            Fichar salida
        </button>
    @else
        <button type="button"
            class="bg-accom-teal text-white rounded-lg px-5 py-2.5 font-medium text-sm hover:bg-teal-600 transition">
            Fichar entrada
        </button>
    @endif
</div>
