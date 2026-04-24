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

        $accuracySummaryByCategory = [];
        $accuracyTablesByCategory = [];

        $dataset = [];
        $avgWaktuWP = 0;
        $avgWaktuBorda = 0;

        $chartLabels = [];
        $chartWaktuWP = [];
        $chartWaktuBorda = [];

        $labels = $labelsNon = $labelsSemua = [];

        $wpAkademikKeseluruhan = $wpAkademikTop3 = [];
        $bordaAkademikKeseluruhan = $bordaAkademikTop3 = [];

        $wpNonKeseluruhan = $wpNonTop3 = [];
        $bordaNonKeseluruhan = $bordaNonTop3 = [];

        $wpSemuaKeseluruhan = $wpSemuaTop3 = [];
        $bordaSemuaKeseluruhan = $bordaSemuaTop3 = [];

        if ($filters['id_semester']) {
            $statistikQuery = AnalisisPerbandingan::where('id_semester', $filters['id_semester']);

            if ($filters['id_kelas']) $statistikQuery->where('id_kelas', $filters['id_kelas']);
            else $statistikQuery->whereNull('id_kelas');

            $statistik = $statistikQuery->orderBy('metode', 'asc')->get();

            $rankings = $this->getFilteredRankings($filters);
            $accuracyByScope = $this->rankingComparisonService->calculateAccuracyByScope($rankings);

            $accuracySummaryByCategory = $this->rankingComparisonService->getAccuracySummaryByCategory($rankings);

            $accuracyTablesByCategory = $this->rankingComparisonService->getAccuracyTablesByCategory($rankings);

            // HAPUS DATA DUMMY LAMA INI
            /*
$labels = ['Kelas 1', 'Kelas 2', 'Kelas 3'];

$wpAkademikKeseluruhan = [80, 85, 90];
$wpAkademikTop3 = [85, 88, 92];
$bordaAkademikKeseluruhan = [78, 82, 87];
$bordaAkademikTop3 = [83, 86, 89];
*/


            // ===============================
            // GRAFIK AKURASI DATA ASLI
            // ===============================

            // AKADEMIK
            $rowsAkademik = $accuracyTablesByCategory['keseluruhan']['akademik']['rows'] ?? [];
            $rowsAkademikTop3 = $accuracyTablesByCategory['top_3']['akademik']['rows'] ?? [];

            $labels = collect($rowsAkademik)->pluck('dataset_label')->values()->toArray();

            $wpAkademikKeseluruhan = collect($rowsAkademik)->pluck('akurasi_wp')->map(fn($v) => round($v, 2))->values()->toArray();

            $bordaAkademikKeseluruhan = collect($rowsAkademik)->pluck('akurasi_borda')->map(fn($v) => round($v, 2))->values()->toArray();

            $wpAkademikTop3 = collect($rowsAkademikTop3)->pluck('akurasi_wp')->map(fn($v) => round($v, 2))->values()->toArray();

            $bordaAkademikTop3 = collect($rowsAkademikTop3)->pluck('akurasi_borda')->map(fn($v) => round($v, 2))->values()->toArray();


            // NON AKADEMIK
            $rowsNon = $accuracyTablesByCategory['keseluruhan']['non_akademik']['rows'] ?? [];
            $rowsNonTop3 = $accuracyTablesByCategory['top_3']['non_akademik']['rows'] ?? [];

            $labelsNon = collect($rowsNon)->pluck('dataset_label')->values()->toArray();

            $wpNonKeseluruhan = collect($rowsNon)->pluck('akurasi_wp')->map(fn($v) => round($v, 2))->values()->toArray();

            $bordaNonKeseluruhan = collect($rowsNon)->pluck('akurasi_borda')->map(fn($v) => round($v, 2))->values()->toArray();

            $wpNonTop3 = collect($rowsNonTop3)->pluck('akurasi_wp')->map(fn($v) => round($v, 2))->values()->toArray();

            $bordaNonTop3 = collect($rowsNonTop3)->pluck('akurasi_borda')->map(fn($v) => round($v, 2))->values()->toArray();


            // SEMUA
            $rowsSemua = $accuracyTablesByCategory['keseluruhan']['semua']['rows'] ?? [];
            $rowsSemuaTop3 = $accuracyTablesByCategory['top_3']['semua']['rows'] ?? [];

            $labelsSemua = collect($rowsSemua)->pluck('dataset_label')->values()->toArray();

            $wpSemuaKeseluruhan = collect($rowsSemua)->pluck('akurasi_wp')->map(fn($v) => round($v, 2))->values()->toArray();

            $bordaSemuaKeseluruhan = collect($rowsSemua)->pluck('akurasi_borda')->map(fn($v) => round($v, 2))->values()->toArray();

            $wpSemuaTop3 = collect($rowsSemuaTop3)->pluck('akurasi_wp')->map(fn($v) => round($v, 2))->values()->toArray();

            $bordaSemuaTop3 = collect($rowsSemuaTop3)->pluck('akurasi_borda')->map(fn($v) => round($v, 2))->values()->toArray();
        }

        return view('layouts.admin.contents.analisis.show_pengujian', compact(
            'statistik',
            'accuracyByScope',
            'dropdowns',
            'filters',

            // AKADEMIK
            'labels',
            'wpAkademikKeseluruhan',
            'wpAkademikTop3',
            'bordaAkademikKeseluruhan',
            'bordaAkademikTop3',

            // NON AKADEMIK
            'labelsNon',
            'wpNonKeseluruhan',
            'wpNonTop3',
            'bordaNonKeseluruhan',
            'bordaNonTop3',

            // SEMUA
            'labelsSemua',
            'wpSemuaKeseluruhan',
            'wpSemuaTop3',
            'bordaSemuaKeseluruhan',
            'bordaSemuaTop3',

            'accuracySummaryByCategory',
            'accuracyTablesByCategory',

            // biarkan lama
            'dataset',
            'avgWaktuWP',
            'avgWaktuBorda',
            'chartLabels',
            'chartWaktuWP',
            'chartWaktuBorda'
        ));
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
