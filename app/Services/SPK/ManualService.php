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
                // Penjumlahan sederhana (SAW tanpa bobot, atau dengan bobot 1)
                // Sesuai request awal: "menjumlahkan nilai setiap kriteria"
                $totalNilai += $values[$c->id] ?? 0;
            }
            $finalScores[$altId] = $totalNilai;
        }
        $timer->stopStage('tahap_1');

        // --- TAHAP 2: Perangkingan ---
        $timer->startStage();
        arsort($finalScores); // Sortir total nilai dari tertinggi
        $timer->stopStage('tahap_2');

        $timings = $timer->timings;
        $timings['total'] = $timer->getTotalTime();

        return [
            'steps' => [
                'final_scores' => $finalScores // Masukkan ke dalam 'steps' agar seragam
            ],
            'values' => $finalScores, // Tetap kirim ini untuk backward compatibility jika perlu
            'timings' => $timings,
        ];
    }
}
