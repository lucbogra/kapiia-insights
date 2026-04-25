<?php

namespace App\BI\Analysis\Application;

use App\BI\Analysis\Domain\Indicator\IndicatorTargetEnum;

final class ScenarioIndicatorCalculator
{
    /**
     * @param  array  $resolvedIndicators  [['indicator' => IndicatorInterface, 'definition' => IndicatorDefinition, 'parameters' => array], ...]
     * @param  array  $dataset  retourné par ScenarioQueryOrchestrator
     * @return array<string, array{label: string, output_type: string, value: mixed}>
     */
    public function compute(array $resolvedIndicators, array $dataset): array
    {
        $results = [];

        foreach ($resolvedIndicators as $entry) {
            $indicator = $entry['indicator'];
            $definition = $entry['definition'];
            $parameters = $entry['parameters'];

            $sourceDataset = match ($indicator->target()) {
                IndicatorTargetEnum::Incidents => $dataset['incidents']->all(),
                IndicatorTargetEnum::Transmissions => $dataset['transmissions']->all(),
                IndicatorTargetEnum::Global => $dataset,
            };

            $results[$indicator->key()] = [
                'label' => $definition->label,
                'output_type' => $indicator->outputType()->value,
                'target' => $indicator->target()->value,
                'value' => $indicator->compute($sourceDataset, $parameters),
            ];
        }

        return $results;
    }
}
