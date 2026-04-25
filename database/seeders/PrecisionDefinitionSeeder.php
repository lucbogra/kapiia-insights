<?php

namespace Database\Seeders;

use App\BI\Analysis\Domain\Precision\PrecisionRegistry;
use App\BI\Analysis\Infrastructure\Persistence\PrecisionDefinitionModel;
use Illuminate\Database\Seeder;

class PrecisionDefinitionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(PrecisionRegistry $registry): void
    {
        $order = 0;

        foreach ($registry->all() as $precision) {
            PrecisionDefinitionModel::updateOrCreate(
                ['key' => $precision->key()],
                [
                    'label' => $precision->label(),
                    'type' => $precision->type()->value,
                    'target' => $precision->target()->value,
                    'parameters_schema' => $precision->parametersSchema(),
                    'is_active' => true,
                    'sort_order' => ++$order,
                ],
            );
        }
    }
}
