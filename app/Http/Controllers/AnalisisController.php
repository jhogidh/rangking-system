<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\AnalisisPerbandingan;
use App\Models\Ranking;
use App\Models\Semester;
use App\Models\Kelas;
use App\Services\SPK\AnalysisService;

class AnalisisController extends Controller
{
    protected $analysisService;

    public function __construct(AnalysisService $analysis)
    {
        $this->analysisService = $analysis;
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
        $rankings = null;

        if ($filters['id_semester']) {
            $rankingQuery = Ranking::query()
                ->with('dataSiswaKelas.siswa')
                ->whereHas('dataSiswaKelas', function ($q_siswa) use ($filters) {
                    $q_siswa->where('id_semester', $filters['id_semester']);
                    if ($filters['id_kelas']) $q_siswa->where('id_kelas', $filters['id_kelas']);
                });

            $rankings = $rankingQuery
                ->orderBy('metode', 'asc')
                ->orderBy('ranking', 'asc')
                ->get()
                ->groupBy('dataSiswaKelas.siswa.nama');
        }

        return view('layouts.admin.contents.analisis.show_pemeringkatan', compact('rankings', 'dropdowns', 'filters'));
    }

    public function showPengujian(Request $request)
    {
        $filters = $this->getFilters($request);
        $dropdowns = $this->getDropdownData();
        $statistik = null;

        if ($filters['id_semester']) {
            $statistikQuery = AnalisisPerbandingan::where('id_semester', $filters['id_semester']);

            if ($filters['id_kelas']) $statistikQuery->where('id_kelas', $filters['id_kelas']);
            else $statistikQuery->whereNull('id_kelas');

            $statistik = $statistikQuery->orderBy('metode', 'asc')->get();
        }

        return view('layouts.admin.contents.analisis.show_pengujian', compact('statistik', 'dropdowns', 'filters'));
    }

    public function hitungSpearman(Request $request)
    {
        $request->validate(['id_semester' => 'required']);
        $id_semester = $request->id_semester;
        $id_kelas = $request->id_kelas;

        $rankingsDB = Ranking::query()
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
}
