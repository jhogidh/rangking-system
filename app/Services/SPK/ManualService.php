<?php

namespace App\Services\SPK;

use App\Services\TimerService;
use Illuminate\Support\Collection;

class ManualService
{
    public function calculate(array $alternatives, Collection $criteria): array
    {
        $timer = new TimerService();
        $finalScores = [];

        // --- TAHAP 1: Menjumlahkan nilai setiap kriteria ---
        $timer->startStage();
        foreach ($alternatives as $altId => $values) {
            $totalNilai = 0;
            foreach ($criteria as $c) {
                $totalNilai += $values[$c->id] ?? 0;
            }
            $finalScores[$altId] = $totalNilai;
        }
        $timer->stopStage('tahap_1');

        // --- TAHAP 2: Perangkingan ---
$timer->startStage();

// Ubah jadi array detail dulu
$detailedScores = [];

foreach ($finalScores as $altId => $score) {
    $detailedScores[] = [
        'id' => $altId,
        'total' => $score,
        'values' => $alternatives[$altId]
    ];
}

usort($detailedScores, function ($a, $b) use ($criteria) {

    // 1️⃣ Bandingkan total dulu
    if ($a['total'] != $b['total']) {
        return $b['total'] <=> $a['total'];
    }

    // 2️⃣ Jika sama → bandingkan per kriteria
    foreach ($criteria as $c) {
        $aVal = $a['values'][$c->id] ?? 0;
        $bVal = $b['values'][$c->id] ?? 0;

        if ($aVal != $bVal) {
            return $bVal <=> $aVal;
        }
    }

    return 0;
});

// Kembalikan ke format awal (id => total)
$finalScores = [];
foreach ($detailedScores as $item) {
    $finalScores[$item['id']] = $item['total'];
}

$timer->stopStage('tahap_2');

$timings = $timer->timings;
$timings['total'] = $timer->getTotalTime();

return [
    'steps' => [
        'final_scores' => $finalScores
    ],
    'values' => $finalScores,
    'timings' => $timings,
];
    }
}
