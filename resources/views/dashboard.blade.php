<x-app-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">

            <div class="flex items-center gap-4">
                <span
                    class="w-10 h-10 rounded-full bg-accom-teal text-white flex items-center justify-center font-semibold text-sm">
                    {{ $initials }}
                </span>
                <p class="text-lg text-gray-800">{{ $greeting }}</p>
            </div>

            <x-clock-widget :has-open-record="$hasOpenRecord" />

            <div>
                <h2 class="text-base font-semibold text-gray-800 mb-4">Registros del mes actual</h2>

                @if ($records->isEmpty())
                    <p class="text-gray-400">No records for this month yet.</p>
                @else
                    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-sm">
                            <thead class="bg-accom-teal-light">
                                <tr>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Fecha</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Entrada</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Salida</th>
                                    <th class="px-4 py-3 text-left font-semibold text-gray-700">Total horas</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($records as $record)
                                    <tr>
                                        <td class="px-4 py-3 text-gray-800">
                                            {{ $record->clocked_in_at->format('d/m/Y') }}
                                        </td>
                                        <td class="px-4 py-3 text-gray-800">
                                            {{ $record->clocked_in_at->format('H:i') }}
                                        </td>
                                        <td class="px-4 py-3 text-gray-800">
                                            {{ $record->clocked_out_at?->format('H:i') ?? '—' }}
                                        </td>
                                        <td class="px-4 py-3 text-gray-800">
                                            @if ($record->clocked_out_at)
                                                @php
                                                    $minutes = (int) abs(
                                                        $record->clocked_in_at->diffInMinutes($record->clocked_out_at),
                                                    );
                                                @endphp
                                                {{ intdiv($minutes, 60) }}h {{ $minutes % 60 }}m
                                            @else
                                                —
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4">
                        {{ $records->links() }}
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
