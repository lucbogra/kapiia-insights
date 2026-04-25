<x-filament-panels::page>

    {{-- Carte récapitulative du scénario --}}
    <x-filament::section>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
            @php($summary = $this->getScenarioSummary())

            <div>
                <p class="text-gray-500 dark:text-gray-400">Nom</p>
                <p class="font-medium text-gray-900 dark:text-gray-100">{{ $summary['name'] }}</p>
            </div>

            <div>
                <p class="text-gray-500 dark:text-gray-400">Propriétaire</p>
                <p class="font-medium text-gray-900 dark:text-gray-100">
                    {{ $summary['owner'] }}
                    @if($summary['is_shared'])
                        <x-filament::badge color="info" class="ml-1">Partagé</x-filament::badge>
                    @endif
                </p>
            </div>

            <div>
                <p class="text-gray-500 dark:text-gray-400">Population</p>
                <p class="font-medium text-gray-900 dark:text-gray-100">{{ $summary['population'] }}</p>
            </div>

            <div>
                <p class="text-gray-500 dark:text-gray-400">Configuration</p>
                <p class="font-medium text-gray-900 dark:text-gray-100">
                    {{ $summary['precisions'] }} précision(s), {{ $summary['indicators'] }} indicateur(s)
                </p>
            </div>

            <div class="sm:col-span-2">
                <p class="text-gray-500 dark:text-gray-400 mb-1">Sources interrogées</p>
                <div class="flex flex-wrap gap-1">
                    @foreach($summary['sources'] as $source)
                        <x-filament::badge color="teal">{{ $source }}</x-filament::badge>
                    @endforeach
                </div>
            </div>

            @if($summary['description'])
                <div class="sm:col-span-2">
                    <p class="text-gray-500 dark:text-gray-400">Description</p>
                    <p class="text-gray-700 dark:text-gray-300">{{ $summary['description'] }}</p>
                </div>
            @endif
        </div>

        <div class="mt-4">
            <x-filament::button
                wire:click="run"
                wire:loading.attr="disabled"
                icon="heroicon-o-play"
                size="lg"
            >
                <span wire:loading.remove wire:target="run">Exécuter l'analyse</span>
                <span wire:loading wire:target="run">Exécution en cours...</span>
            </x-filament::button>
        </div>
    </x-filament::section>

    {{-- Résultats --}}
    @if($this->result)

        {{-- KPIs globaux --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">

            <x-filament::section>
                <div class="text-center">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Population</p>
                    <p class="mt-1 text-4xl font-bold text-primary-600">
                        {{ $this->result['population_count'] }}
                    </p>
                    <p class="text-xs text-gray-400 mt-1">usagers correspondants</p>
                </div>
            </x-filament::section>

            <x-filament::section>
                <div class="text-center">
                    <p class="text-sm text-gray-500 dark:text-gray-400">Incidents collectés</p>
                    <p class="mt-1 text-4xl font-bold text-warning-600">
                        {{ $this->result['incident_count'] }}
                    </p>
                </div>
            </x-filament::section>
        </div>

        {{-- Chaque indicateur, adapté à son output_type --}}
        <x-filament::section heading="Indicateurs mesurés">
            <div class="space-y-6">
                @foreach($this->result['indicator_results'] as $key => $indicator)
                    <div>
                        <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                            {{ $indicator['label'] }}
                        </h3>

                        {{-- Valeur scalaire --}}
                        @if($indicator['output_type'] === 'scalar')
                            <div class="text-3xl font-bold text-primary-600">
                                {{ is_numeric($indicator['value']) ? number_format($indicator['value'], 2) : $indicator['value'] }}
                            </div>

                        {{-- Liste triée --}}
                        @elseif($indicator['output_type'] === 'list')
                            @if(empty($indicator['value']))
                                <p class="text-sm text-gray-400">Aucune donnée.</p>
                            @else
                                <div class="space-y-2">
                                    @foreach($indicator['value'] as $label => $count)
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs text-gray-600 dark:text-gray-400 w-48 truncate" title="{{ $label }}">
                                                {{ $label }}
                                            </span>
                                            <div class="flex-1 bg-gray-100 dark:bg-gray-800 rounded-full h-2">
                                                <div
                                                    class="bg-primary-500 h-2 rounded-full"
                                                    style="width: {{ round(($count / (max($indicator['value']) ?: 1)) * 100) }}%"
                                                ></div>
                                            </div>
                                            <span class="text-xs font-medium text-gray-700 dark:text-gray-300 w-10 text-right">
                                                {{ $count }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                        {{-- Distribution (ex: gravité 1 à 5) --}}
                        @elseif($indicator['output_type'] === 'distribution')
                            <div class="space-y-2">
                                @foreach($indicator['value'] as $bucket => $count)
                                    <div class="flex items-center gap-2">
                                        <span class="text-xs text-gray-600 dark:text-gray-400 w-24">
                                            {{ $bucket }}
                                        </span>
                                        <div class="flex-1 bg-gray-100 dark:bg-gray-800 rounded-full h-2">
                                            <div
                                                class="bg-warning-500 h-2 rounded-full"
                                                style="width: {{ round(($count / (array_sum($indicator['value']) ?: 1)) * 100) }}%"
                                            ></div>
                                        </div>
                                        <span class="text-xs text-gray-500 w-12 text-right">
                                            {{ $count }} ({{ round(($count / (array_sum($indicator['value']) ?: 1)) * 100) }}%)
                                        </span>
                                    </div>
                                @endforeach
                            </div>

                        {{-- Moyennes groupées (ex: comportement par activité) --}}
                        @elseif($indicator['output_type'] === 'grouped_average')
                            <div class="overflow-x-auto">
                                <table class="w-full text-sm">
                                    <thead>
                                        <tr class="text-left text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">
                                            <th class="pb-2 font-medium">Groupe</th>
                                            <th class="pb-2 font-medium text-right">Moyenne</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                                        @foreach($indicator['value'] as $group => $avg)
                                            <tr>
                                                <td class="py-2 text-gray-700 dark:text-gray-300">{{ $group }}</td>
                                                <td class="py-2 text-right">
                                                    <x-filament::badge :color="$avg >= 4 ? 'success' : ($avg >= 3 ? 'warning' : 'danger')">
                                                        {{ number_format($avg, 2) }} / 5
                                                    </x-filament::badge>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                        @else
                            <pre class="text-xs text-gray-500 bg-gray-50 dark:bg-gray-800 p-2 rounded overflow-x-auto">{{ json_encode($indicator['value'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                        @endif

                    </div>
                @endforeach
            </div>
        </x-filament::section>

        <p class="text-xs text-gray-400 text-right">
            Analyse calculée le {{ \Carbon\Carbon::parse($this->result['computed_at'])->format('d/m/Y à H:i:s') }}
        </p>

    @endif

</x-filament-panels::page>
