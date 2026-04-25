<x-filament-panels::page>

    {{-- Formulaire de sélection --}}
    <x-filament::section>
        <form wire:submit="runAnalysis">
            {{ $this->form }}

            <div class="flex justify-center items-center gap-3 mt-4">
                <x-filament::button type="submit" icon="heroicon-o-play">
                    Lancer l'analyse
                </x-filament::button>

                @if($this->result)
                    <x-filament::button
                        color="gray"
                        icon="heroicon-o-trash"
                        wire:click="invalidateCache"
                    >
                        Vider le cache
                    </x-filament::button>
                @endif
            </div>
        </form>
    </x-filament::section>

    {{-- Badge cache --}}
    @if($this->result && $this->fromCache)
        <x-filament::badge color="info" icon="heroicon-o-archive-box">
            Résultats chargés depuis le cache
        </x-filament::badge>
    @endif

    {{-- Résultats --}}
    @if($this->result)

        {{-- KPIs principaux --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">

            <x-filament::section>
                <div class="text-center">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Population identifiée
                    </p>
                    <p class="mt-1 text-4xl font-bold text-primary-600">
                        {{ $this->getPopulationCount() }}
                    </p>
                    <p class="text-xs text-gray-400 mt-1">
                        usagers correspondants
                    </p>
                </div>
            </x-filament::section>

            <x-filament::section>
                <div class="text-center">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Nombre d'incidents
                    </p>
                    <p class="mt-1 text-4xl font-bold text-primary-600">
                        {{ $this->getIncidentTotal() }}
                    </p>
                </div>
            </x-filament::section>

            <x-filament::section>
                <div class="text-center">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Gravité moyenne des incidents
                    </p>
                    <p class="mt-1 text-4xl font-bold
                        @if($this->getAverageGravite() >= 4) text-danger-600
                        @elseif($this->getAverageGravite() >= 3) text-warning-600
                        @else text-success-600
                        @endif">
                        {{ number_format($this->getAverageGravite(), 1) }}
                        <span class="text-lg font-normal text-gray-400">/ 5</span>
                    </p>
                </div>
            </x-filament::section>

            <x-filament::section>
                <div class="text-center">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Sources interrogées
                    </p>
                    <div class="mt-2 flex flex-wrap justify-center gap-1">
                        @foreach($this->getSources() as $source)
                            <x-filament::badge color="teal">
                                {{ $source }}
                            </x-filament::badge>
                        @endforeach
                    </div>
                </div>
            </x-filament::section>

        </div>

        {{-- Incidents --}}
        <x-filament::section heading="Incidents">
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">

                {{-- Incidents par intitulé --}}
                <div>
                    <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                        Incidents les plus fréquents
                    </h3>
                    <div class="space-y-2">
                        @forelse($this->getIncidentsByTitle() as $title => $count)
                            @php $max = max($this->getIncidentsByTitle()); @endphp
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-gray-500 w-40 truncate" title="{{ $title }}">
                                    {{ $title }}
                                </span>
                                <div class="flex-1 bg-gray-100 dark:bg-gray-800 rounded-full h-2">
                                    <div
                                        class="bg-primary-500 h-2 rounded-full"
                                        style="width: {{ $max > 0 ? round(($count / $max) * 100) : 0 }}%"
                                    ></div>
                                </div>
                                <span class="text-xs font-medium text-gray-700 dark:text-gray-300 w-6 text-right">
                                    {{ $count }}
                                </span>
                            </div>
                        @empty
                            <p class="text-sm text-gray-400">Aucun incident enregistré.</p>
                        @endforelse
                    </div>
                </div>

                {{-- Distribution des gravités --}}
                <div>
                    <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">
                        Distribution des gravités
                    </h3>
                    <div class="space-y-2">
                        @foreach(range(1, 5) as $level)
                            @php
                                $count = $this->getGraviteDistribution()[$level] ?? 0;
                                $total = array_sum($this->getGraviteDistribution());
                                $pct   = $total > 0 ? round(($count / $total) * 100) : 0;
                                $color = match($level) {
                                    1 => 'bg-success-400',
                                    2 => 'bg-success-600',
                                    3 => 'bg-warning-400',
                                    4 => 'bg-danger-400',
                                    5 => 'bg-danger-600',
                                };
                            @endphp
                            <div class="flex items-center gap-2">
                                <span class="text-xs text-gray-500 w-16">
                                    Gravité {{ $level }}
                                </span>
                                <div class="flex-1 bg-gray-100 dark:bg-gray-800 rounded-full h-2">
                                    <div
                                        class="{{ $color }} h-2 rounded-full transition-all"
                                        style="width: {{ $pct }}%"
                                    ></div>
                                </div>
                                <span class="text-xs text-gray-500 w-8 text-right">
                                    {{ $count }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>

            </div>
        </x-filament::section>

        {{-- Transmissions --}}
        {{-- <x-filament::section heading="Indicateurs par activité">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="text-left text-gray-500 dark:text-gray-400 border-b border-gray-200 dark:border-gray-700">
                            <th class="pb-2 font-medium">Activité</th>
                            <th class="pb-2 font-medium text-center">Comportement</th>
                            <th class="pb-2 font-medium text-center">Concentration</th>
                            <th class="pb-2 font-medium text-center">Relationnel</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
                        @forelse(array_keys($this->getAvgBehaviorByActivity()) as $activity)
                            @php
                                $behavior      = $this->getAvgBehaviorByActivity()[$activity] ?? 0;
                                $concentration = $this->getAvgConcentrationByActivity()[$activity] ?? 0;
                                $social        = $this->getAvgSocialByActivity()[$activity] ?? 0;

                                $badge = fn(float $v) => match(true) {
                                    $v >= 4  => 'success',
                                    $v >= 3  => 'warning',
                                    default  => 'danger',
                                };
                            @endphp
                            <tr>
                                <td class="py-2 font-medium text-gray-700 dark:text-gray-300">
                                    {{ $activity }}
                                </td>
                                <td class="py-2 text-center">
                                    <x-filament::badge :color="$badge($behavior)">
                                        {{ number_format($behavior, 1) }} / 5
                                    </x-filament::badge>
                                </td>
                                <td class="py-2 text-center">
                                    <x-filament::badge :color="$badge($concentration)">
                                        {{ number_format($concentration, 1) }} / 5
                                    </x-filament::badge>
                                </td>
                                <td class="py-2 text-center">
                                    <x-filament::badge :color="$badge($social)">
                                        {{ number_format($social, 1) }} / 5
                                    </x-filament::badge>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-4 text-center text-gray-400">
                                    Aucune transmission enregistrée.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </x-filament::section> --}}

    @endif

</x-filament-panels::page>
