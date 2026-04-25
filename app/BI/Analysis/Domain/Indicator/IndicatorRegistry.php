<?php

namespace App\BI\Analysis\Domain\Indicator;

final class IndicatorRegistry
{
    /** @var array<string, IndicatorInterface> */
    private array $indicators = [];

    public function register(IndicatorInterface $indicator): void
    {
        $this->indicators[$indicator->key()] = $indicator;
    }

    public function get(string $key): ?IndicatorInterface
    {
        return $this->indicators[$key] ?? null;
    }

    /** @return IndicatorInterface[] */
    public function all(): array
    {
        return array_values($this->indicators);
    }

    public function keys(): array
    {
        return array_keys($this->indicators);
    }
}
