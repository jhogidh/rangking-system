<?php

namespace App\Support;

class RankingHelper
{
    /**
     * Build competition ranks from an already-sorted (desc) score list.
     * Example: [100, 100, 90] => [1, 1, 3]
     */
    public static function competitionRanks(array $sortedScores, float $epsilon = 1.0E-12): array
    {
        $ranks = [];
        $position = 0;
        $currentRank = 0;
        $previousScore = null;

        foreach ($sortedScores as $altId => $score) {
            $position++;

            if ($previousScore === null || abs((float) $score - (float) $previousScore) > $epsilon) {
                $currentRank = $position;
                $previousScore = $score;
            }

            $ranks[$altId] = $currentRank;
        }

        return $ranks;
    }

    /**
     * Build dense ranks (no skipping numbers).
     * Example: [100, 100, 90] => [1, 1, 2]
     */
    public static function denseRanks(array $sortedScores, float $epsilon = 1.0E-12): array
    {
        $ranks = [];
        $currentRank = 0;
        $previousScore = null;

        foreach ($sortedScores as $altId => $score) {

            if ($previousScore === null || abs((float) $score - (float) $previousScore) > $epsilon) {
                $currentRank++;
                $previousScore = $score;
            }

            $ranks[$altId] = $currentRank;
        }

        return $ranks;
    }
}
