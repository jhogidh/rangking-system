<?php

namespace App\Services\SPK;

use App\Services\TimerService;
use Illuminate\Support\Collection;

class WeightedProductService
{
    public function calculate(array $alternatives, Collection $criteria): array
    {
        $timer = new TimerService();
        $steps = [
            'raw_values' => $alternatives,
            'normalized_weights' => [],
            'vector_s' => [],
            'vector_v' => [],
        ];

        // --- TAHAP 1: Normalisasi Bobot ---
        $timer->startStage();
        $totalBobot = $criteria->sum('bobot');
        $normalizedWeights = $criteria->mapWithKeys(
            fn($c) =>
            [$c->id => $c->bobot / $totalBobot]
        );
        $steps['normalized_weights'] = $normalizedWeights;
        $timer->stopStage('tahap_1');

        // --- TAHAP 2: Menghitung Vektor S ---
        $timer->startStage();
        foreach ($alternatives as $altId => $values) {
            $s = 1;
            foreach ($normalizedWeights as $kriteriaId => $bobot) {
                $nilai = $values[$kriteriaId] ?? 0;
                if ($nilai > 0) {
                    $s *= pow($nilai, $bobot);
                }
            }
            $steps['vector_s'][$altId] = $s;
        }
        $timer->stopStage('tahap_2');

        // --- TAHAP 3: Menghitung Vektor V ---
        $timer->startStage();
        $totalVectorS = array_sum($steps['vector_s']);
        if ($totalVectorS > 0) {
            foreach ($steps['vector_s'] as $altId => $s) {
                $steps['vector_v'][$altId] = $s / $totalVectorS;
            }
        } else {
            $steps['vector_v'] = array_fill_keys(array_keys($steps['vector_s']), 0);
        }
        $timer->stopStage('tahap_3');

        // --- TAHAP 4: Perangkingan ---
        $timer->startStage();
        arsort($steps['vector_v']);
        $timer->stopStage('tahap_4');

        $timings = $timer->timings;
        $timings['total'] = $timer->getTotalTime();

        return [
            'steps' => $steps,
            'timings' => $timings,
            'values' => $steps['vector_v']
        ];
    }
}
