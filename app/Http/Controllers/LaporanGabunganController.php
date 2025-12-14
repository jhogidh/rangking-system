<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\AnalisisPerbandingan;

class LaporanGabunganController extends Controller
{
    public function index()
    {
        $laporan = AnalisisPerbandingan::with(['semester', 'kelas'])
            ->orderBy('id_semester', 'asc')
            ->orderBy('id_kelas', 'asc')
            ->get();

        $dataset = [];
        // Data untuk Grafik
        $chartLabels = [];
        $chartDataWP = [];
        $chartDataBorda = [];

        foreach ($laporan as $row) {
            $semesterLabel = $row->semester->tahun_mulai . '/' . $row->semester->tahun_selesai . ' ' . $row->semester->nama;
            $kelasLabel = $row->kelas ? $row->kelas->nama : 'All';
            $key = $row->id_semester . '-' . ($row->id_kelas ?? 'all');

            // Label gabungan untuk Tabel & Chart (misal: "2024 Ganjil - 10A")
            $fullLabel = $semesterLabel . ' - ' . $kelasLabel;

            $dataset[$key]['semester'] = $semesterLabel;
            $dataset[$key]['kelas'] = $kelasLabel;
            $dataset[$key][$row->metode] = [
                'spearman' => $row->spearman_rho,
                'waktu' => $row->waktu_total
            ];

            // Menyusun Data Chart (Hanya perlu dilakukan sekali per kombinasi semester-kelas)
            // Kita cek apakah label ini sudah ada di chartLabels agar tidak duplikat (karena loop jalan per metode)
            if (!in_array($fullLabel, $chartLabels)) {
                $chartLabels[] = $fullLabel;
            }

            // Isi data sesuai metode
            if ($row->metode == 'WP') {
                // Kita gunakan index dari chartLabels terakhir untuk memastikan urutan sinkron
                $index = array_search($fullLabel, $chartLabels);
                $chartDataWP[$index] = $row->spearman_rho;
            } elseif ($row->metode == 'Borda') {
                $index = array_search($fullLabel, $chartLabels);
                $chartDataBorda[$index] = $row->spearman_rho;
            }
        }

        // Re-index array agar urutannya rapi (0, 1, 2...) untuk JSON
        $chartDataWP = array_values($chartDataWP);
        $chartDataBorda = array_values($chartDataBorda);

        $avgWP = $laporan->where('metode', 'WP')->avg('spearman_rho');
        $avgBorda = $laporan->where('metode', 'Borda')->avg('spearman_rho');

        return view('layouts.admin.contents.laporan.gabungan', compact(
            'dataset',
            'avgWP',
            'avgBorda',
            'chartLabels',
            'chartDataWP',
            'chartDataBorda'
        ));
    }
}
