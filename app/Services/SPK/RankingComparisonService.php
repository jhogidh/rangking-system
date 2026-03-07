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
                $wpStats = $this->calculateMatchStats($manualForWp, $wpCompared);
                $bordaStats = $this->calculateMatchStats($manualForBorda, $bordaCompared);

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

    private function calculateMatchStats(array $manualRanks, array $targetRanks): array
    {
        $sesuai = 0;
        $tidakSesuai = 0;

        foreach ($manualRanks as $studentClassId => $manualRank) {
            $targetRank = $targetRanks[$studentClassId] ?? null;
            if ($targetRank === null) {
                continue;
            }

            if ((int) $manualRank === (int) $targetRank) {
                $sesuai++;
            } else {
                $tidakSesuai++;
            }
        }

        $total = $sesuai + $tidakSesuai;
        $akurasi = $total > 0 ? round(($sesuai / $total) * 100, 2) : null;

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
