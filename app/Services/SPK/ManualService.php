<?php

namespace App\Services\SPK;

use App\Services\TimerService;
use Illuminate\Support\Collection;

class ManualService
{
    /**
     * Menjalankan perhitungan "Manual" (SAW Sederhana).
     *
     * @param array $alternatives Data alternatif [id_siswa_kelas => [id_kriteria => nilai]]
     * @param Collection $criteria Koleksi model Kriteria
     * @return array
     */
    public function calculate(array $alternatives, Collection $criteria): array
    {
        $timer = new TimerService();
        $finalScores = [];

        // --- TAHAP 1: Menjumlahkan nilai setiap kriteria ---
        $timer->startStage();
        foreach ($alternatives as $altId => $values) {
            $totalNilai = 0;
            foreach ($criteria as $c) {
                // Ini menjumlahkan nilai asli, bukan bobot
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
            'values' => $finalScores, // [id_siswa_kelas => total_nilai]
            'timings' => $timings,
        ];
    }
}
