<?php

namespace App\Services\SPK;

use App\Services\TimerService;
use Illuminate\Support\Collection;

class BordaService
{

    public function calculate(array $alternatives, Collection $criteria): array
    {
        $timer = new TimerService();
        $n = count($alternatives); // Jumlah seluruh alternatif
        $altIds = array_keys($alternatives);

        $steps = [
            'raw_values' => $alternatives,
            'ranks_per_criteria' => [],
            'borda_scores' => [],
            'weighted_scores' => [],
            'final_scores' => [],
        ];

        // --- TAHAP 1: Menentukan Ranking per Kriteria ---
        // --- TAHAP 1: Menentukan Ranking per Kriteria (MODIFIED) ---
        $timer->startStage();
        foreach ($criteria as $c) {
            $tempScores = [];
            foreach ($alternatives as $altId => $values) {
                $tempScores[$altId] = $values[$c->id] ?? 0;
            }
            arsort($tempScores); // Sortir nilai (DESC)

            $ranks = [];
            $rank = 1;          // Rangking yang akan ditempel
            $counter = 1;       // Penghitung urutan baris (1, 2, 3, ...)
            $prevScore = null;  // Menyimpan nilai skor sebelumnya

            foreach ($tempScores as $altId => $score) {
                // Jika skor beda dengan sebelumnya, update rangking ke posisi counter saat ini
                // Jika skor sama, rangking jangan berubah (tetap pakai $rank yang lama)
                if ($score != $prevScore) {
                    $rank = $counter;
                }

                $ranks[$altId] = $rank;

                $prevScore = $score; // Simpan skor untuk pengecekan iterasi berikutnya
                $counter++;          // Counter baris selalu nambah
            }

            $steps['ranks_per_criteria'][$c->id] = $ranks;
        }
        $timer->stopStage('tahap_1');

        // --- TAHAP 2: Menentukan Skor Borda (n - rank) ---
        $timer->startStage();
        foreach ($steps['ranks_per_criteria'] as $kriteriaId => $ranks) {
            foreach ($ranks as $altId => $rank) {
                $steps['borda_scores'][$altId][$kriteriaId] = $n - $rank;
            }
        }
        $timer->stopStage('tahap_2');

        // --- TAHAP 3: Bobot ROC (Kriteria) x Skor ---
        $timer->startStage();
        $criteriaWeights = $criteria->pluck('bobot', 'id');

        foreach ($steps['borda_scores'] as $altId => $scores) {
            foreach ($scores as $kriteriaId => $score) {
                $steps['weighted_scores'][$altId][$kriteriaId] = $score * ($criteriaWeights[$kriteriaId] ?? 0);
            }
        }
        $timer->stopStage('tahap_3');

        // --- TAHAP 4: Penjumlahan (Bobot x Skor) ---
        $timer->startStage();
        foreach ($steps['weighted_scores'] as $altId => $weightedScores) {
            $steps['final_scores'][$altId] = array_sum($weightedScores);
        }
        $timer->stopStage('tahap_4');

        // --- TAHAP 5: Perangkingan ---
        $timer->startStage();
        arsort($steps['final_scores']); // Sortir skor akhir (DESC)
        $timer->stopStage('tahap_5');

        $timings = $timer->timings;
        $timings['total'] = $timer->getTotalTime();

        return [
            'steps' => $steps,
            'timings' => $timings,
        ];
    }
}
