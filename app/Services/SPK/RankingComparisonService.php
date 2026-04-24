<?php

namespace App\Services\SPK;

use App\Models\Ranking;
use Illuminate\Support\Collection;

class RankingComparisonService
{
    private const CATEGORY_LABELS = [
        Ranking::CATEGORY_ALL => 'Semua Kriteria',
        Ranking::CATEGORY_AKADEMIK => 'Akademik',
        Ranking::CATEGORY_NON_AKADEMIK => 'Non Akademik',
    ];

    public function getCategoryLabels(): array
    {
        return self::CATEGORY_LABELS;
    }

    public function normalizeCategory(?string $category): string
    {
        if ($category === null || $category === '') {
            return Ranking::CATEGORY_ALL;
        }

        return $category;
    }

    public function calculateAccuracyByScope(Collection $rankings): array
    {
        $methodRanks = $this->collectMethodRanksByCategory($rankings);

        $scopes = [
            'keseluruhan' => null,
            'top_3' => 3,
        ];

        $result = [];
        foreach ($scopes as $scopeKey => $topN) {
            $rows = [];
            foreach (self::CATEGORY_LABELS as $categoryKey => $categoryLabel) {
                $manualRanks = $methodRanks[$categoryKey]['Manual'] ?? [];
                $wpRanks = $methodRanks[$categoryKey]['WP'] ?? [];
                $bordaRanks = $methodRanks[$categoryKey]['Borda'] ?? [];

                [$manualForWp, $wpCompared] = $this->buildComparableRanks($manualRanks, $wpRanks, $topN);
                [$manualForBorda, $bordaCompared] = $this->buildComparableRanks($manualRanks, $bordaRanks, $topN);
                $wpStats = $this->calculateMatchStats($manualForWp, $wpCompared, $topN);
                $bordaStats = $this->calculateMatchStats($manualForBorda, $bordaCompared, $topN);

                $rows[] = [
                    'kategori_key' => $categoryKey,
                    'kategori_label' => $categoryLabel,
                    'jumlah_manual' => $topN ? min($topN, count($manualRanks)) : count($manualRanks),
                    'jumlah_wp_valid' => $wpStats['total'],
                    'wp_sesuai' => $wpStats['sesuai'],
                    'wp_tidak_sesuai' => $wpStats['tidak_sesuai'],
                    'akurasi_wp' => $wpStats['akurasi'],
                    'jumlah_borda_valid' => $bordaStats['total'],
                    'borda_sesuai' => $bordaStats['sesuai'],
                    'borda_tidak_sesuai' => $bordaStats['tidak_sesuai'],
                    'akurasi_borda' => $bordaStats['akurasi'],
                ];
            }

            $result[$scopeKey] = $rows;
        }

        return $result;
    }

    public function getAccuracySummaryByCategory(Collection $rankings): array
{
    $data = $this->calculateAccuracyByScope($rankings);
    $result = [];

    foreach (self::CATEGORY_LABELS as $key => $label) {
        $kes = collect($data['keseluruhan'])->firstWhere('kategori_key', $key);
        $top = collect($data['top_3'])->firstWhere('kategori_key', $key);

        $result[$key] = [
            'label' => $label,
            'jumlah_siswa' => $kes['jumlah_manual'] ?? 0,

            'avg_wp_keseluruhan' => $kes['akurasi_wp'] ?? 0,
            'avg_wp_top3' => $top['akurasi_wp'] ?? 0,

            'avg_borda_keseluruhan' => $kes['akurasi_borda'] ?? 0,
            'avg_borda_top3' => $top['akurasi_borda'] ?? 0,

            'wp_sesuai' => $kes['wp_sesuai'] ?? 0,
            'wp_tidak_sesuai' => $kes['wp_tidak_sesuai'] ?? 0,

            'borda_sesuai' => $kes['borda_sesuai'] ?? 0,
            'borda_tidak_sesuai' => $kes['borda_tidak_sesuai'] ?? 0,
        ];
    }

    return $result;
}

public function getAccuracyTablesByCategory(Collection $rankings): array
{
    $grouped = $rankings->groupBy(function ($item) {
        return $item->dataSiswaKelas->id_kelas;
    });

    $result = [
        'keseluruhan' => [],
        'top_3' => []
    ];

    foreach (self::CATEGORY_LABELS as $categoryKey => $label) {

        $rowsKes = [];
        $rowsTop = [];

        foreach ($grouped as $kelasId => $items) {

            $kelas = optional($items->first()->dataSiswaKelas->kelas);
            $namaKelas = trim(($kelas->nama ?? '') . ' ' . ($kelas->sub ?? ''));

            $filtered = $items->filter(function ($row) use ($categoryKey) {
                return $this->normalizeCategory($row->kategori) == $categoryKey;
            });

            $acc = $this->calculateAccuracyByScope($filtered);

            $kes = collect($acc['keseluruhan'])->firstWhere('kategori_key', $categoryKey);
            $top = collect($acc['top_3'])->firstWhere('kategori_key', $categoryKey);

            if ($kes) {
                $rowsKes[] = [
                    'dataset_label' => $namaKelas,
                    'wp_sesuai' => $kes['wp_sesuai'],
                    'wp_tidak_sesuai' => $kes['wp_tidak_sesuai'],
                    'akurasi_wp' => $kes['akurasi_wp'],
                    'borda_sesuai' => $kes['borda_sesuai'],
                    'borda_tidak_sesuai' => $kes['borda_tidak_sesuai'],
                    'akurasi_borda' => $kes['akurasi_borda'],
                    'jumlah_manual' => $kes['jumlah_manual'],
                ];
            }

            if ($top) {
                $rowsTop[] = [
                    'dataset_label' => $namaKelas,
                    'wp_sesuai' => $top['wp_sesuai'],
                    'wp_tidak_sesuai' => $top['wp_tidak_sesuai'],
                    'akurasi_wp' => $top['akurasi_wp'],
                    'borda_sesuai' => $top['borda_sesuai'],
                    'borda_tidak_sesuai' => $top['borda_tidak_sesuai'],
                    'akurasi_borda' => $top['akurasi_borda'],
                    'jumlah_manual' => $top['jumlah_manual'],
                ];
            }
        }

        $result['keseluruhan'][$categoryKey] = [
            'label' => $label,
            'rows' => $rowsKes
        ];

        $result['top_3'][$categoryKey] = [
            'label' => $label,
            'rows' => $rowsTop
        ];
    }

    return $result;
}

    private function collectMethodRanksByCategory(Collection $rankings): array
    {
        $result = [];
        foreach (array_keys(self::CATEGORY_LABELS) as $categoryKey) {
            $result[$categoryKey] = [
                'Manual' => [],
                'WP' => [],
                'Borda' => [],
            ];
        }

        foreach ($rankings as $ranking) {
            $category = $this->normalizeCategory($ranking->kategori ?? null);
            $method = $ranking->metode ?? null;
            $studentClassId = $ranking->id_data_siswa_kelas ?? null;

            if (!isset($result[$category]) || !isset($result[$category][$method]) || !$studentClassId) {
                continue;
            }

            $result[$category][$method][$studentClassId] = (int) $ranking->ranking;
        }

        return $result;
    }

    private function buildComparableRanks(array $manualRanks, array $targetRanks, ?int $topN): array
    {
        $sortedManual = $this->sortRanksByValue($manualRanks);

        if ($topN !== null) {
            $sortedManual = array_slice($sortedManual, 0, $topN, true);
        }

        $manualCompared = [];
        $targetCompared = [];

        foreach ($sortedManual as $studentClassId => $manualRank) {
            if (!isset($targetRanks[$studentClassId])) {
                continue;
            }

            $manualCompared[$studentClassId] = $manualRank;
            $targetCompared[$studentClassId] = $targetRanks[$studentClassId];
        }

        return [$manualCompared, $targetCompared];
    }

    private function calculateMatchStats(array $manualRanks, array $targetRanks, ?int $topN = null): array
    {
        $sesuai = 0;
        $tidakSesuai = 0;

        if ($topN !== null) {
            // Top N → cocok jika siswa ada di Top N target, urutan tidak masalah
            foreach ($manualRanks as $studentId => $manualRank) {
                if (isset($targetRanks[$studentId]) && $targetRanks[$studentId] <= $topN) {
                    $sesuai++;
                } else {
                    $tidakSesuai++;
                }
            }
            $total = count($manualRanks);
            $akurasi = $total > 0 ? round(($sesuai / $total) * 100, 2) : null;
        } else {
            // Keseluruhan → harus persis sama
            foreach ($manualRanks as $studentId => $manualRank) {
                $targetRank = $targetRanks[$studentId] ?? null;
                if ($targetRank === null) continue;

                if ((int) $manualRank === (int) $targetRank) {
                    $sesuai++;
                } else {
                    $tidakSesuai++;
                }
            }
            $total = $sesuai + $tidakSesuai;
            $akurasi = $total > 0 ? round(($sesuai / $total) * 100, 2) : null;
        }

        return [
            'total' => $total,
            'sesuai' => $sesuai,
            'tidak_sesuai' => $tidakSesuai,
            'akurasi' => $akurasi,
        ];
    }


    private function sortRanksByValue(array $ranks): array
    {
        $rows = [];
        foreach ($ranks as $studentClassId => $rank) {
            $rows[] = [
                'id' => (int) $studentClassId,
                'rank' => (int) $rank,
            ];
        }

        usort($rows, function ($a, $b) {
            if ($a['rank'] !== $b['rank']) {
                return $a['rank'] <=> $b['rank'];
            }

            return $a['id'] <=> $b['id'];
        });

        $sorted = [];
        foreach ($rows as $row) {
            $sorted[$row['id']] = $row['rank'];
        }

        return $sorted;
    }
}