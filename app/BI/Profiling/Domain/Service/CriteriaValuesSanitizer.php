<?php

namespace App\BI\Profiling\Domain\Service;

final class CriteriaValuesSanitizer
{
    /**
     * Nettoie un tableau de critères en retirant ceux qui n'ont pas de valeur exploitable.
     *
     * Règles de filtrage :
     * - Valeur null ou string vide : retirée
     * - Plage avec from ET to à null : retirée
     * - Plage avec from OU to défini : conservée (plage partielle valide)
     *
     * @param  array  $values  Valeurs brutes du formulaire
     * @return array Valeurs nettoyées (peut être vide)
     */
    public function sanitize(array $values): array
    {
        $sanitized = [];

        foreach ($values as $key => $value) {
            if ($this->isEmpty($value)) {
                continue;
            }

            if (is_array($value) && $this->isRangeEmpty($value)) {
                continue;
            }

            $sanitized[$key] = $this->cleanValue($value);
        }

        return $sanitized;
    }

    private function isEmpty(mixed $value): bool
    {
        return $value === null || $value === '';
    }

    private function isRangeEmpty(array $range): bool
    {
        $from = $range['from'] ?? $range['min'] ?? null;
        $to = $range['to'] ?? $range['max'] ?? null;

        return $this->isEmpty($from) && $this->isEmpty($to);
    }

    /**
     * Pour les plages, on retire les bornes null individuelles
     * pour ne garder que celles définies.
     */
    private function cleanValue(mixed $value): mixed
    {
        if (! is_array($value)) {
            return $value;
        }

        return array_filter(
            $value,
            fn ($v) => ! $this->isEmpty($v),
        );
    }
}
