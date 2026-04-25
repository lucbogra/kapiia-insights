<?php

namespace Database\Seeders;

use App\BI\Analysis\Domain\Indicator\IndicatorRegistry;
use App\BI\Analysis\Infrastructure\Persistence\IndicatorDefinitionModel;
use Illuminate\Database\Seeder;

class IndicatorDefinitionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(IndicatorRegistry $registry): void
    {
        $order = 0;

        foreach ($registry->all() as $indicator) {
            IndicatorDefinitionModel::updateOrCreate(
                ['key' => $indicator->key()],
                [
                    'label' => $indicator->label(),
                    'target' => $indicator->target(),
                    'output_type' => $indicator->outputType(),
                    'parameters_schema' => $indicator->parametersSchema(),
                    'is_active' => true,
                    'sort_order' => ++$order,
                ],
            );
        }
    }
}
