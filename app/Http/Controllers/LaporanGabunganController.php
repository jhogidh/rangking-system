<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AnalisisPerbandingan;
use App\Models\Ranking;
use App\Services\SPK\RankingComparisonService;

class LaporanGabunganController extends Controller
{
    public function __construct(private RankingComparisonService $rankingComparisonService)
    {
    }

    public function index()
    {
        // Ambil hanya WP & Borda supaya label/chart konsisten
        $laporan = AnalisisPerbandingan::with(['semester', 'kelas'])
            ->whereIn('metode', ['WP', 'Borda'])
            ->orderBy('id_semester', 'asc')
            ->orderBy('id_kelas', 'asc')
            ->get();

        $dataset = [];

        // Data untuk Chart
        $chartLabels = [];
        $chartDataWP = [];
        $chartDataBorda = [];
        $chartWaktuWP = [];
        $chartWaktuBorda = [];

        foreach ($laporan as $row) {
            $semesterLabel = $row->semester->tahun_mulai . '/' . $row->semester->tahun_selesai . ' ' . $row->semester->nama;
            $kelasLabel = $row->kelas ? $row->kelas->nama : 'All';
            $key = $row->id_semester . '-' . ($row->id_kelas ?? 'all');

            $fullLabel = $semesterLabel . ' - ' . $kelasLabel;

            $dataset[$key]['semester'] = $semesterLabel;
            $dataset[$key]['kelas'] = $kelasLabel;
            $dataset[$key][$row->metode] = [
                'spearman' => $row->spearman_rho,
                'waktu' => $row->waktu_total,
            ];

            if (!in_array($fullLabel, $chartLabels, true)) {
                $chartLabels[] = $fullLabel;
            }

            $index = array_search($fullLabel, $chartLabels, true);

            if ($row->metode === 'WP') {
                $chartDataWP[$index] = $row->spearman_rho;
                $chartWaktuWP[$index] = $row->waktu_total;
            } elseif ($row->metode === 'Borda') {
                $chartDataBorda[$index] = $row->spearman_rho;
                $chartWaktuBorda[$index] = $row->waktu_total;
            }
        }

        // Rapikan index supaya urut 0..n
        $chartDataWP = array_values($chartDataWP);
        $chartDataBorda = array_values($chartDataBorda);
        $chartWaktuWP = array_values($chartWaktuWP);
        $chartWaktuBorda = array_values($chartWaktuBorda);

        // Rata-rata akurasi
        $avgWP = $laporan->where('metode', 'WP')->avg('spearman_rho');
        $avgBorda = $laporan->where('metode', 'Borda')->avg('spearman_rho');

        // Rata-rata waktu
        $avgWaktuWP = $laporan->where('metode', 'WP')->avg('waktu_total');
        $avgWaktuBorda = $laporan->where('metode', 'Borda')->avg('waktu_total');

        $accuracyByScope = $this->buildAccuracyRekap();

        return view('layouts.admin.contents.laporan.gabungan', compact(
            'dataset',
            'avgWP',
            'avgBorda',
            'avgWaktuWP',
            'avgWaktuBorda',
            'chartLabels',
            'chartDataWP',
            'chartDataBorda',
            'chartWaktuWP',
            'chartWaktuBorda',
            'accuracyByScope'
        ));
    }

    private function buildAccuracyRekap(): array
    {
        $rankings = Ranking::with(['dataSiswaKelas.semester', 'dataSiswaKelas.kelas'])->get();
        $rankingsByDataset = $rankings
            ->filter(fn($r) => $r->dataSiswaKelas !== null)
            ->groupBy(function ($ranking) {
                $idSemester = $ranking->dataSiswaKelas->id_semester;
                $idKelas = $ranking->dataSiswaKelas->id_kelas ?? 'all';
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
                        'kategori_label' => $row['kategori_label'],
                        'spearman_wp' => $row['spearman_wp'],
                        'spearman_borda' => $row['spearman_borda'],
                        'jumlah_manual' => $row['jumlah_manual'],
                    ];
                }
            }
        }

        usort($result['keseluruhan'], fn($a, $b) => strcmp($a['dataset_key'], $b['dataset_key']));
        usort($result['top_3'], fn($a, $b) => strcmp($a['dataset_key'], $b['dataset_key']));

        return $result;
    }
}
