<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\AnalisisPerbandingan;
use App\Models\Ranking;
use App\Models\Semester;
use App\Models\Kelas;
use App\Services\SPK\AnalysisService;
use App\Services\SPK\RankingComparisonService;
use Illuminate\Support\Collection;

class AnalisisController extends Controller
{
    protected $analysisService;
    protected $rankingComparisonService;

    public function __construct(AnalysisService $analysis, RankingComparisonService $rankingComparisonService)
    {
        $this->analysisService = $analysis;
        $this->rankingComparisonService = $rankingComparisonService;
    }

    private function getFilters(Request $request)
    {
        $request->validate([
            'id_semester' => 'nullable|exists:semester,id',
            'id_kelas' => 'nullable|exists:kelas,id',
        ]);
        return [
            'id_semester' => $request->query('id_semester'),
            'id_kelas' => $request->query('id_kelas'),
        ];
    }

    private function getDropdownData()
    {
        return [
            'semesters' => Semester::orderBy('id', 'desc')->get(),
            'kelasList' => Kelas::orderBy('nama', 'asc')->get(),
        ];
    }

    public function showPemeringkatan(Request $request)
    {
        $filters = $this->getFilters($request);
        $dropdowns = $this->getDropdownData();
        $rankingsByCategory = null;
        $categoryLabels = $this->rankingComparisonService->getCategoryLabels();

        if ($filters['id_semester']) {
            $rankings = $this->getFilteredRankings($filters);
            $rankingsByCategory = $this->buildRankingsByCategory($rankings, array_keys($categoryLabels));
        }

        return view('layouts.admin.contents.analisis.show_pemeringkatan', compact(
            'rankingsByCategory',
            'categoryLabels',
            'dropdowns',
            'filters'
        ));
    }

    public function showPengujian(Request $request)
    {
        $filters = $this->getFilters($request);
        $dropdowns = $this->getDropdownData();
        $statistik = null;
        $accuracyByScope = null;

        if ($filters['id_semester']) {
            $statistikQuery = AnalisisPerbandingan::where('id_semester', $filters['id_semester']);

            if ($filters['id_kelas']) $statistikQuery->where('id_kelas', $filters['id_kelas']);
            else $statistikQuery->whereNull('id_kelas');

            $statistik = $statistikQuery->orderBy('metode', 'asc')->get();

            $rankings = $this->getFilteredRankings($filters);
            $accuracyByScope = $this->rankingComparisonService->calculateAccuracyByScope($rankings);
        }

        return view('layouts.admin.contents.analisis.show_pengujian', compact(
            'statistik',
            'accuracyByScope',
            'dropdowns',
            'filters'
        ));
    }

    public function hitungSpearman(Request $request)
    {
        $request->validate(['id_semester' => 'required']);
        $id_semester = $request->id_semester;
        $id_kelas = $request->id_kelas;

        $rankingsDB = Ranking::query()
            ->where(function ($q) {
                $q->where('kategori', Ranking::CATEGORY_ALL)->orWhereNull('kategori');
            })
            ->whereHas('dataSiswaKelas', function ($q_siswa) use ($id_semester, $id_kelas) {
                $q_siswa->where('id_semester', $id_semester);
                if ($id_kelas) $q_siswa->where('id_kelas', $id_kelas);
            })->get();

        $manualRanks = $rankingsDB->where('metode', 'Manual')->pluck('ranking', 'id_data_siswa_kelas');
        $wpRanks = $rankingsDB->where('metode', 'WP')->pluck('ranking', 'id_data_siswa_kelas');
        $bordaRanks = $rankingsDB->where('metode', 'Borda')->pluck('ranking', 'id_data_siswa_kelas');

        if ($manualRanks->isEmpty() || $wpRanks->isEmpty() || $bordaRanks->isEmpty()) {
            return redirect()->back()->with('error', 'Gagal hitung Spearman. Pastikan data Borda, WP, dan Manual sudah dihitung (Menu 1, 3, 4).');
        }

        $spearman_wp = $this->analysisService->calculateSpearman($manualRanks->toArray(), $wpRanks->toArray());
        $spearman_borda = $this->analysisService->calculateSpearman($manualRanks->toArray(), $bordaRanks->toArray());

        AnalisisPerbandingan::updateOrCreate(['id_semester' => $id_semester, 'id_kelas' => $id_kelas, 'metode' => 'WP'], ['spearman_rho' => $spearman_wp]);
        AnalisisPerbandingan::updateOrCreate(['id_semester' => $id_semester, 'id_kelas' => $id_kelas, 'metode' => 'Borda'], ['spearman_rho' => $spearman_borda]);

        return redirect()->back()->with('success', 'Perhitungan Akurasi (Spearman) Selesai!');
    }

    private function getFilteredRankings(array $filters): Collection
    {
        return Ranking::query()
            ->with(['dataSiswaKelas.siswa', 'dataSiswaKelas.kelas'])
            ->whereHas('dataSiswaKelas', function ($q_siswa) use ($filters) {
                $q_siswa->where('id_semester', $filters['id_semester']);
                if ($filters['id_kelas']) {
                    $q_siswa->where('id_kelas', $filters['id_kelas']);
                }
            })
            ->orderBy('metode', 'asc')
            ->orderBy('ranking', 'asc')
            ->get();
    }

    private function buildRankingsByCategory(Collection $rankings, array $categories): array
    {
        $result = [];
        foreach ($categories as $categoryKey) {
            $rows = $rankings
                ->filter(fn($row) => $this->rankingComparisonService->normalizeCategory($row->kategori ?? null) === $categoryKey)
                ->groupBy('id_data_siswa_kelas')
                ->map(function ($items) {
                    $first = $items->first();
                    $kelas = $first->dataSiswaKelas->kelas ?? null;
                    $manualRank = optional($items->firstWhere('metode', 'Manual'))->ranking;
                    $wpRank = optional($items->firstWhere('metode', 'WP'))->ranking;
                    $bordaRank = optional($items->firstWhere('metode', 'Borda'))->ranking;

                    return [
                        'id_data_siswa_kelas' => $first->id_data_siswa_kelas,
                        'nama_siswa' => $first->dataSiswaKelas->siswa->nama ?? 'N/A',
                        'kelas' => $kelas ? trim(($kelas->nama ?? '') . ' ' . ($kelas->sub ?? '')) : '-',
                        'manual' => $manualRank,
                        'wp' => $wpRank,
                        'borda' => $bordaRank,
                        'label_wp' => $this->buildComparisonLabel($manualRank, $wpRank),
                        'label_borda' => $this->buildComparisonLabel($manualRank, $bordaRank),
                    ];
                })
                ->values()
                ->all();

            usort($rows, function ($a, $b) {
                $rankA = $a['manual'] ?? PHP_INT_MAX;
                $rankB = $b['manual'] ?? PHP_INT_MAX;
                if ($rankA !== $rankB) {
                    return $rankA <=> $rankB;
                }
                return strcasecmp($a['nama_siswa'], $b['nama_siswa']);
            });

            $result[$categoryKey] = collect($rows);
        }

        return $result;
    }

    private function buildComparisonLabel($manualRank, $methodRank): string
    {
        if ($manualRank === null || $methodRank === null) {
            return '-';
        }

        $manualTop3 = (int) $manualRank <= 3;
        $methodTop3 = (int) $methodRank <= 3;

        return ($manualTop3 && $methodTop3) ? 'Sesuai' : 'Tidak Sesuai';
    }
}
