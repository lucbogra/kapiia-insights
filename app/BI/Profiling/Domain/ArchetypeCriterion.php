<?php

// app/BI/Profiling/Domain/ArchetypeCriterion.php

namespace App\BI\Profiling\Domain;

final class ArchetypeCriterion
{
    public function __construct(
        public readonly string  $id,
        public readonly string  $key,
        public readonly string  $label,
        public readonly string  $type,           // 'discrete' | 'range'
        public readonly array   $options,
        public readonly ?string $nomenclaturePrefix,
        public readonly string  $sourceColumn,
        public readonly int     $sortOrder,
    ) {}

    public function isRange(): bool
    {
        return $this->type === 'range';
    }

    public function isDiscrete(): bool
    {
        return $this->type === 'discrete';
    }

    public function discreteValues(): array
    {
        return $this->options['values'] ?? [];
    }

    public function rangeBounds(): array
    {
        return [
            'min'  => $this->options['min']  ?? 0,
            'max'  => $this->options['max']  ?? 100,
            'step' => $this->options['step'] ?? 1,
        ];
    }
}
