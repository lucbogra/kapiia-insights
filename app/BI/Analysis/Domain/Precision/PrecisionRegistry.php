<?php

namespace App\BI\Analysis\Domain\Precision;

final class PrecisionRegistry
{
    /** @var array<string, PrecisionInterface> */
    private array $precisions = [];

    public function register(PrecisionInterface $precision): void
    {
        $this->precisions[$precision->key()] = $precision;
    }

    public function get(string $key): ?PrecisionInterface
    {
        return $this->precisions[$key] ?? null;
    }

    /** @return PrecisionInterface[] */
    public function all(): array
    {
        return array_values($this->precisions);
    }
}
