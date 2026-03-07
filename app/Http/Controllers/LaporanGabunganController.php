<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AnalisisPerbandingan;
use App\Models\Ranking;
use App\Services\SPK\RankingComparisonService;
use Illuminate\Support\Collection;

class LaporanGabunganController extends Controller
{
    public function __construct(private RankingComparisonService $rankingComparisonService)
    {
    }

    public function index()
    {
        // Ambil hanya WP & Borda untuk rekap waktu.
        $laporan = AnalisisPerbandingan::with(['semester', 'kelas'])
            ->whereIn('metode', ['WP', 'Borda'])
            ->whereNotNull('id_kelas')
            ->orderBy('id_semester', 'asc')
            ->orderBy('id_kelas', 'asc')
            ->get();

        $dataset = [];

        foreach ($laporan as $row) {
            $semesterLabel = $row->semester->tahun_mulai . '/' . $row->semester->tahun_selesai . ' ' . $row->semester->nama;
            $kelasLabel = $row->kelas ? $row->kelas->nama : 'All';
            $key = $row->id_semester . '-' . ($row->id_kelas ?? 'all');

            $dataset[$key]['semester'] = $semesterLabel;
            $dataset[$key]['kelas'] = $kelasLabel;
            $dataset[$key][$row->metode] = [
                'waktu' => $row->waktu_total,
            ];
        }

        // Rata-rata waktu
        $avgWaktuWP = $laporan->where('metode', 'WP')->avg('waktu_total');
        $avgWaktuBorda = $laporan->where('metode', 'Borda')->avg('waktu_total');

        $chartLabels = [];
        $chartWaktuWP = [];
        $chartWaktuBorda = [];
        foreach ($dataset as $row) {
            $chartLabels[] = ($row['semester'] ?? '-') . ' - ' . ($row['kelas'] ?? '-');
            $chartWaktuWP[] = isset($row['WP']['waktu']) ? (float) $row['WP']['waktu'] : 0.0;
            $chartWaktuBorda[] = isset($row['Borda']['waktu']) ? (float) $row['Borda']['waktu'] : 0.0;
        }

        $accuracyByScope = $this->buildAccuracyRekap();
        $accuracyTablesByCategory = [
            'keseluruhan' => $this->groupAccuracyRowsByCategory($accuracyByScope['keseluruhan'] ?? []),
            'top_3' => $this->groupAccuracyRowsByCategory($accuracyByScope['top_3'] ?? []),
        ];
        $accuracySummaryByCategory = $this->buildAccuracySummaryByCategory($accuracyTablesByCategory);

        return view('layouts.admin.contents.laporan.gabungan', compact(
            'dataset',
            'avgWaktuWP',
            'avgWaktuBorda',
            'chartLabels',
            'chartWaktuWP',
            'chartWaktuBorda',
            'accuracyByScope',
            'accuracyTablesByCategory',
            'accuracySummaryByCategory'
        ));
    }

    private function buildAccuracyRekap(): array
    {
        $rankings = Ranking::with(['dataSiswaKelas.semester', 'dataSiswaKelas.kelas'])->get();
        $rankingsByDataset = $rankings
            ->filter(fn($r) => $r->dataSiswaKelas !== null && $r->dataSiswaKelas->id_kelas !== null)
            ->groupBy(function ($ranking) {
                $idSemester = $ranking->dataSiswaKelas->id_semester;
                $idKelas = $ranking->dataSiswaKelas->id_kelas;
                return $idSemester . '-' . $idKelas;
            });

        $result = [
            'keseluruhan' => [],
            'top_3' => [],
        ];

        foreach ($rankingsByDataset as $datasetKey => $datasetRankings) {
            $first = $datasetRankings->first();
            $semester = $first->dataSiswaKelas->semester;
            $kelas = $first->dataSiswaKelas->kelas;

            if (!$semester) {
                continue;
            }

            $semesterLabel = $semester->tahun_mulai . '/' . $semester->tahun_selesai . ' ' . $semester->nama;
            $kelasLabel = $kelas ? trim(($kelas->nama ?? '') . ' ' . ($kelas->sub ?? '')) : 'Semua Kelas';
            $datasetLabel = $semesterLabel . ' - ' . $kelasLabel;

            $accuracyPerScope = $this->rankingComparisonService->calculateAccuracyByScope($datasetRankings);
            foreach (['keseluruhan', 'top_3'] as $scopeKey) {
                foreach ($accuracyPerScope[$scopeKey] as $row) {
                    $result[$scopeKey][] = [
                        'dataset_key' => $datasetKey,
                        'dataset_label' => $datasetLabel,
                        'kategori_key' => $row['kategori_key'],
                        'kategori_label' => $row['kategori_label'],
                        'wp_sesuai' => $row['wp_sesuai'],
                        'wp_tidak_sesuai' => $row['wp_tidak_sesuai'],
                        'akurasi_wp' => $row['akurasi_wp'],
                        'borda_sesuai' => $row['borda_sesuai'],
                        'borda_tidak_sesuai' => $row['borda_tidak_sesuai'],
                        'akurasi_borda' => $row['akurasi_borda'],
                        'jumlah_manual' => $row['jumlah_manual'],
                    ];
                }
            }
        }

        usort($result['keseluruhan'], fn($a, $b) => strcmp($a['dataset_key'], $b['dataset_key']));
        usort($result['top_3'], fn($a, $b) => strcmp($a['dataset_key'], $b['dataset_key']));

        return $result;
    }

    private function groupAccuracyRowsByCategory(array $rows): array
    {
        $categories = $this->rankingComparisonService->getCategoryLabels();
        $grouped = [];
        foreach ($categories as $categoryKey => $label) {
            $grouped[$categoryKey] = [
                'label' => $label,
                'rows' => [],
            ];
        }

        foreach ($rows as $row) {
            $categoryKey = $row['kategori_key'] ?? null;
            if (!$categoryKey || !isset($grouped[$categoryKey])) {
                continue;
            }
            $grouped[$categoryKey]['rows'][] = $row;
        }

        return $grouped;
    }

    private function buildAccuracySummaryByCategory(array $accuracyTablesByCategory): array
    {
        $categories = $this->rankingComparisonService->getCategoryLabels();
        $summary = [];
        foreach ($categories as $categoryKey => $label) {
            $rowsKeseluruhan = $accuracyTablesByCategory['keseluruhan'][$categoryKey]['rows'] ?? [];
            $rowsTop3 = $accuracyTablesByCategory['top_3'][$categoryKey]['rows'] ?? [];
            $countKeseluruhan = $this->aggregateCounts($rowsKeseluruhan);

            $summary[$categoryKey] = [
                'label' => $label,
                'avg_wp_keseluruhan' => $this->averageAccuracy($rowsKeseluruhan, 'akurasi_wp'),
                'avg_borda_keseluruhan' => $this->averageAccuracy($rowsKeseluruhan, 'akurasi_borda'),
                'avg_wp_top3' => $this->averageAccuracy($rowsTop3, 'akurasi_wp'),
                'avg_borda_top3' => $this->averageAccuracy($rowsTop3, 'akurasi_borda'),
                'jumlah_siswa' => $countKeseluruhan['jumlah_siswa'],
                'wp_sesuai' => $countKeseluruhan['wp_sesuai'],
                'wp_tidak_sesuai' => $countKeseluruhan['wp_tidak_sesuai'],
                'borda_sesuai' => $countKeseluruhan['borda_sesuai'],
                'borda_tidak_sesuai' => $countKeseluruhan['borda_tidak_sesuai'],
            ];
        }

        return $summary;
    }

    private function aggregateCounts(array $rows): array
    {
        $collection = collect($rows);

        return [
            'jumlah_siswa' => (int) $collection->sum('jumlah_manual'),
            'wp_sesuai' => (int) $collection->sum('wp_sesuai'),
            'wp_tidak_sesuai' => (int) $collection->sum('wp_tidak_sesuai'),
            'borda_sesuai' => (int) $collection->sum('borda_sesuai'),
            'borda_tidak_sesuai' => (int) $collection->sum('borda_tidak_sesuai'),
        ];
    }

    private function averageAccuracy(array $rows, string $key): ?float
    {
        $values = collect($rows)->pluck($key)->filter(fn($v) => $v !== null)->values();
        if ($values->isEmpty()) {
            return null;
        }

        return round((float) $values->avg(), 2);
    }
}
