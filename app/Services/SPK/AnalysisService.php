<?php

namespace App\Services\SPK;

class AnalysisService
{
    /**
     * Menghitung Korelasi Rank Spearman.
     *
     * @param array $ranks1 Array asosiatif [id_alternatif => ranking]
     * @param array $ranks2 Array asosiatif [id_alternatif => ranking]
     * @return float
     */
    public function calculateSpearman(array $ranks1, array $ranks2): float
    {
        $n = count($ranks1);

        // Pastikan jumlah elemen sama
        if ($n == 0 || $n !== count($ranks2)) {
            return 0.0;
        }

        $sum_d_squared = 0;
        foreach ($ranks1 as $altId => $rank1) {
            // Jika ID alternatif tidak ada di kedua list, lewati
            if (!isset($ranks2[$altId])) {
                // Ini seharusnya tidak terjadi jika data $alternatives konsisten
                continue;
            }

            $rank2 = $ranks2[$altId];
            $d = $rank1 - $rank2;
            $sum_d_squared += ($d * $d);
        }

        // Rumus Spearman: 1 - ( (6 * sum(d^2)) / (n * (n^2 - 1)) )

        $denominator = $n * (($n * $n) - 1);
        if ($denominator == 0) {
            // Terjadi jika n=1, korelasi tidak terdefinisi, tapi kita anggap 1 (cocok sempurna)
            return 1.0;
        }

        $rho = 1 - ((6 * $sum_d_squared) / $denominator);

        return $rho;
    }
}
