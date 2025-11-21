<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// --- Model yang Dibutuhkan ---
use App\Models\AnalisisPerbandingan;
use App\Models\DataSiswaKelas;
use App\Models\Ranking;
use App\Models\Semester;
use App\Models\Kelas;
use App\Models\Kriteria;
use App\Services\SPK\AnalysisService;
use App\Services\SPK\ManualService;

class AnalisisController extends Controller
{
    // Inject service
    protected $manualService;
    protected $analysisService;

    public function __construct(ManualService $manual, AnalysisService $analysis)
    {
        $this->manualService = $manual;
        $this->analysisService = $analysis;
    }

    /**
     * Helper privat untuk mengambil filter dari Request (GET)
     */
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

    /**
     * Helper privat untuk mengambil dropdown
     */
    private function getDropdownData()
    {
        return [
            'semesters' => Semester::with('tahunAjaran')->orderBy('id', 'desc')->get(),
            'kelasList' => Kelas::orderBy('nama', 'asc')->get(),
        ];
    }


    /**
     * Menampilkan halaman "Pemeringkatan" (Menu 4)
     */
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
                    if ($filters['id_kelas']) {
                        $q_siswa->where('id_kelas', $filters['id_kelas']);
                    }
                });

            $rankings = $rankingQuery
                ->orderBy('metode', 'asc')
                ->orderBy('ranking', 'asc')
                ->get()
                ->groupBy('dataSiswaKelas.siswa.nama');
        }

        return view('layouts.admin.contents.analisis.show_pemeringkatan', compact(
            'rankings',
            'dropdowns',
            'filters'
        ));
    }

    /**
     * Menampilkan halaman "Pengujian" (Menu 5)
     */
    public function showPengujian(Request $request)
    {
        $filters = $this->getFilters($request);
        $dropdowns = $this->getDropdownData();
        $statistik = null;

        if ($filters['id_semester']) {
            $statistikQuery = AnalisisPerbandingan::where('id_semester', $filters['id_semester']);

            if ($filters['id_kelas']) {
                $statistikQuery->where('id_kelas', $filters['id_kelas']);
            } else {
                $statistikQuery->whereNull('id_kelas');
            }

            $statistik = $statistikQuery->orderBy('metode', 'asc')->get();
        }

        return view('layouts.admin.contents.analisis.show_pengujian', compact(
            'statistik',
            'dropdowns',
            'filters'
        ));
    }

    /**
     * Helper privat untuk mengambil data (dipakai Manual & Spearman)
     */
    private function prepareData($id_semester, $id_kelas)
    {
        $allCriteria = Kriteria::all();
        $query = DataSiswaKelas::where('id_semester', $id_semester)->with('nilaiKriteria');
        if ($id_kelas) $query->where('id_kelas', $id_kelas);
        $dataSiswaSemester = $query->get();

        if ($dataSiswaSemester->isEmpty()) return false;

        $alternatives = [];
        $usedCriteriaIds = [];
        foreach ($dataSiswaSemester as $siswa) {
            $nilaiMap = $siswa->nilaiKriteria->pluck('nilai', 'id_kriteria');
            if ($nilaiMap->isEmpty()) continue;
            $alternatives[$siswa->id] = $nilaiMap;
            $usedCriteriaIds = array_merge($usedCriteriaIds, $nilaiMap->keys()->all());
        }
        if (empty($alternatives)) return false;

        $criteria = $allCriteria->whereIn('id', array_unique($usedCriteriaIds));

        return [
            'alternatives' => $alternatives,
            'criteria' => $criteria,
        ];
    }


    /**
     * Menjalankan perhitungan HANYA UNTUK Manual (dari Menu 4)
     */
    public function hitungManual(Request $request)
    {
        $request->validate(['id_semester' => 'required']);
        $id_semester = $request->id_semester;
        $id_kelas = $request->id_kelas;

        $data = $this->prepareData($id_semester, $id_kelas);
        if ($data === false) return redirect()->back()->with('error', 'Tidak ada data nilai untuk dihitung.');

        $manualResult = $this->manualService->calculate($data['alternatives'], $data['criteria']);

        $processedSiswaIds = array_keys($data['alternatives']);
        Ranking::whereIn('id_data_siswa_kelas', $processedSiswaIds)->where('metode', 'Manual')->delete();

        $rank = 1;
        foreach ($manualResult['steps']['final_scores'] as $id_data_siswa_kelas => $nilai_alternatif) {
            Ranking::create([
                'id_data_siswa_kelas' => $id_data_siswa_kelas,
                'metode' => 'Manual',
                'hasil_alternatif' => $nilai_alternatif,
                'ranking' => $rank++,
            ]);
        }

        AnalisisPerbandingan::updateOrCreate(
            ['id_semester' => $id_semester, 'id_kelas' => $id_kelas, 'metode' => 'Manual'],
            [
                'waktu_tahap_1' => $manualResult['timings']['tahap_1'] ?? 0,
                'waktu_tahap_2' => $manualResult['timings']['tahap_2'] ?? 0,
                'waktu_total' => $manualResult['timings']['total'] ?? 0,
                'spearman_rho' => 1.00 // Manual vs Manual = 1
            ]
        );

        return redirect()->back()->with('success', 'Perhitungan Manual Selesai!');
    }

    /**
     * Menjalankan perhitungan Korelasi Spearman (dari Menu 5)
     */
    public function hitungSpearman(Request $request)
    {
        $request->validate(['id_semester' => 'required']);
        $id_semester = $request->id_semester;
        $id_kelas = $request->id_kelas;

        // Ambil data ranking yang sudah tersimpan
        $rankingsDB = Ranking::query()
            ->whereHas('dataSiswaKelas', function ($q_siswa) use ($id_semester, $id_kelas) {
                $q_siswa->where('id_semester', $id_semester);
                if ($id_kelas) $q_siswa->where('id_kelas', $id_kelas);
            })->get();

        $manualRanks = $rankingsDB->where('metode', 'Manual')->pluck('ranking', 'id_data_siswa_kelas');
        $wpRanks = $rankingsDB->where('metode', 'WP')->pluck('ranking', 'id_data_siswa_kelas');
        $bordaRanks = $rankingsDB->where('metode', 'Borda')->pluck('ranking', 'id_data_siswa_kelas');

        if ($manualRanks->isEmpty() || $wpRanks->isEmpty() || $bordaRanks->isEmpty()) {
            return redirect()->back()->with('error', 'Gagal hitung Spearman. Pastikan data Borda, WP, dan Manual sudah dihitung (Menu 2, 3, 4).');
        }

        // Hitung Spearman
        $spearman_wp = $this->analysisService->calculateSpearman($manualRanks->toArray(), $wpRanks->toArray());
        $spearman_borda = $this->analysisService->calculateSpearman($manualRanks->toArray(), $bordaRanks->toArray());

        // Update data statistik
        AnalisisPerbandingan::updateOrCreate(
            ['id_semester' => $id_semester, 'id_kelas' => $id_kelas, 'metode' => 'WP'],
            ['spearman_rho' => $spearman_wp]
        );
        AnalisisPerbandingan::updateOrCreate(
            ['id_semester' => $id_semester, 'id_kelas' => $id_kelas, 'metode' => 'Borda'],
            ['spearman_rho' => $spearman_borda]
        );

        return redirect()->back()->with('success', 'Perhitungan Akurasi (Spearman) Selesai!');
    }
}
