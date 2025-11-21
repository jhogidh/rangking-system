<?php

namespace App\Services\SPK;

use App\Services\TimerService;
use Illuminate\Support\Collection;

class WeightedProductService
{
    /**
     * Menjalankan perhitungan metode Weighted Product (WP).
     *
     * @param array $alternatives Data alternatif [id_siswa_kelas => [id_kriteria => nilai]]
     * @param Collection $criteria Koleksi model Kriteria
     * @return array
     */
    public function calculate(array $alternatives, Collection $criteria): array
    {
        $timer = new TimerService();

        // Inisialisasi array steps lengkap dengan key baru
        $steps = [
            'raw_values' => $alternatives,
            'normalized_weights' => [],
            'vector_s_details' => [], // <--- INI YANG KEMARIN HILANG/BELUM ADA
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
            $details = []; // Array sementara untuk menyimpan hasil pangkat

            foreach ($normalizedWeights as $kriteriaId => $bobot) {
                $nilai = $values[$kriteriaId] ?? 0;

                // Logika hitung pangkat
                if ($nilai > 0) {
                    $hasilPangkat = pow($nilai, $bobot);
                } else {
                    $hasilPangkat = 0; // Jika nilai 0, hasil pangkat dianggap 0 (mengikuti logika WP ketat)
                }

                // Simpan detail (Nilai ^ Bobot) ke array sementara
                $details[$kriteriaId] = $hasilPangkat;

                // Kalikan ke total S
                $s *= $hasilPangkat;
            }

            // Masukkan ke array steps utama
            $steps['vector_s_details'][$altId] = $details; // <--- PENTING: Mengisi key vector_s_details
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
        arsort($steps['vector_v']); // Sortir nilai V (DESC)
        $timer->stopStage('tahap_4');

        $timings = $timer->timings;
        $timings['total'] = $timer->getTotalTime();

        return [
            'steps' => $steps,
            'timings' => $timings,
        ];
    }
}
