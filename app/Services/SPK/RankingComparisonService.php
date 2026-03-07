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

    public function __construct(private AnalysisService $analysisService)
    {
    }

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

                $rows[] = [
                    'kategori_key' => $categoryKey,
                    'kategori_label' => $categoryLabel,
                    'jumlah_manual' => $topN ? min($topN, count($manualRanks)) : count($manualRanks),
                    'jumlah_wp_valid' => count($wpCompared),
                    'jumlah_borda_valid' => count($bordaCompared),
                    'spearman_wp' => empty($wpCompared) ? null : $this->analysisService->calculateSpearman($manualForWp, $wpCompared),
                    'spearman_borda' => empty($bordaCompared) ? null : $this->analysisService->calculateSpearman($manualForBorda, $bordaCompared),
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
