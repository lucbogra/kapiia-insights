<?php

// app/BI/Profiling/Domain/Service/NomenclatureGenerator.php

namespace App\BI\Profiling\Domain\Service;

use App\BI\Profiling\Domain\ArchetypeCriterion;

final class NomenclatureGenerator
{
    /**
     * @param ArchetypeCriterion[] $criteria   Critères triés par sort_order
     * @param array                $values      Ex: {"sex":"M","birth_year":{"from":2010,"to":2013}}
     */
    public function generate(array $criteria, array $values): string
    {
        $parts = [];

        foreach ($criteria as $criterion) {
            $value = $values[$criterion->key] ?? null;

            if ($value === null) {
                continue;
            }

            $prefix = $criterion->nomenclaturePrefix ?? strtoupper($criterion->key);

            if ($criterion->isRange()) {
                $from = $value['from'] ?? $value['min'] ?? '';
                $to   = $value['to']   ?? $value['max'] ?? '';
                // Ex: "2010-2013" ou avec prefix "BY-2010-2013"
                $parts[] = $prefix . $from . '-' . $to;
            } else {
                // Ex: "M", "SEP", "ORP-P"
                $parts[] = $prefix . $value;
            }
        }

        return implode('_', $parts);
    }
}
