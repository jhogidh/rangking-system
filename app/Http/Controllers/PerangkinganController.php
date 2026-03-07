<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

// --- Model yang Dibutuhkan ---
use App\Models\Semester;
use App\Models\Kelas;
use App\Models\DataSiswaKelas;
use App\Models\Kriteria;
use App\Models\Ranking;
use App\Models\AnalisisPerbandingan;
use App\Models\TotalNilaiAkademik; // (Jika kamu masih menggunakannya di 'Persiapan Data')

// --- Service SPK ---
use App\Services\SPK\WeightedProductService;
use App\Services\SPK\BordaService;
use App\Services\SPK\ManualService;
use App\Services\SPK\AnalysisService;
use App\Support\RankingHelper;

class PerangkinganController extends Controller
{
    protected $wpService;
    protected $bordaService;
    protected $manualService;
    protected $analysisService;

    public function __construct(
        WeightedProductService $wp,
        BordaService $borda,
        ManualService $manual,
        AnalysisService $analysis
    ) {
        $this->wpService = $wp;
        $this->bordaService = $borda;
        $this->manualService = $manual;
        $this->analysisService = $analysis;
    }

    public function index()
    {
        $semesters = Semester::with('tahunAjaran')->orderBy('id', 'desc')->get();
        $kelasList = Kelas::orderBy('nama', 'asc')->get();

        return view('layouts.admin.contents.perankingan.index', compact(
            'semesters',
            'kelasList'
        ));
    }


    public function hitungDanAnalisis(Request $request)
    {
        $id_semester = $request->id_semester;
        $id_kelas = $request->id_kelas;
        $allCriteria = Kriteria::all();

        $query = DataSiswaKelas::where('id_semester', $id_semester)
            ->with('nilaiKriteria');

        if ($id_kelas) {
            $query->where('id_kelas', $id_kelas);
        }

        $dataSiswaSemester = $query->get();

        if ($dataSiswaSemester->isEmpty()) {
            return redirect()->back()
                ->with('error', 'Tidak ada data siswa di semester/kelas yang dipilih. (Pastikan Alur B "Import Nilai" sudah dijalankan).');
        }

        $alternatives = [];
        $usedCriteriaIds = [];

        foreach ($dataSiswaSemester as $siswa) {

            $nilaiMap = $siswa->nilaiKriteria->pluck('nilai', 'id_kriteria');

            if ($nilaiMap->isEmpty()) {
                continue;
            }

            $alternatives[$siswa->id] = $nilaiMap;
            $usedCriteriaIds = array_merge($usedCriteriaIds, $nilaiMap->keys()->all());
        }
        if (empty($alternatives)) {
            return redirect()->back()
                ->with('error', 'Siswa ditemukan, tapi data nilai kriteria (hasil import) masih kosong. (Pastikan Alur B-2 "Import Nilai" sudah dijalankan).');
        }

        $criteria = $allCriteria->whereIn('id', array_unique($usedCriteriaIds));

        $wpResult = $this->wpService->calculate($alternatives, $criteria);
        $bordaResult = $this->bordaService->calculate($alternatives, $criteria);
        $manualResult = $this->manualService->calculate($alternatives, $criteria);

        $processedSiswaIds = array_keys($alternatives);
        Ranking::whereIn('id_data_siswa_kelas', $processedSiswaIds)
            ->where('kategori', Ranking::CATEGORY_ALL)
            ->delete();

        $saveRanking = function ($result, $metode) {
            $ranks = RankingHelper::denseRanks($result['values']);
            foreach ($result['values'] as $id_data_siswa_kelas => $nilai_alternatif) {
                Ranking::create([
                    'id_data_siswa_kelas' => $id_data_siswa_kelas,
                    'metode' => $metode,
                    'kategori' => Ranking::CATEGORY_ALL,
                    'hasil_alternatif' => $nilai_alternatif,
                    'ranking' => $ranks[$id_data_siswa_kelas],
                ]);
            }
        };

        $saveRanking($wpResult, 'WP');
        $saveRanking($bordaResult, 'Borda');
        $saveRanking($manualResult, 'Manual');


        $manualRanks = RankingHelper::denseRanks($manualResult['values']);
        $wpRanks = RankingHelper::denseRanks($wpResult['values']);
        $bordaRanks = RankingHelper::denseRanks($bordaResult['values']);

        $spearman_wp = $this->analysisService->calculateSpearman($manualRanks, $wpRanks);
        $spearman_borda = $this->analysisService->calculateSpearman($manualRanks, $bordaRanks);

        $queryStatistik = AnalisisPerbandingan::where('id_semester', $id_semester);
        if ($id_kelas) {
            $queryStatistik->where('id_kelas', $id_kelas);
        } else {
            $queryStatistik->whereNull('id_kelas');
        }
        $queryStatistik->delete();

        AnalisisPerbandingan::create([
            'id_semester' => $id_semester,
            'id_kelas' => $id_kelas,
            'metode' => 'WP',
            'waktu_tahap_1' => $wpResult['timings']['tahap_1'] ?? 0,
            'waktu_tahap_2' => $wpResult['timings']['tahap_2'] ?? 0,
            'waktu_tahap_3' => $wpResult['timings']['tahap_3'] ?? 0,
            'waktu_tahap_4' => $wpResult['timings']['tahap_4'] ?? 0,
            'waktu_total' => $wpResult['timings']['total'] ?? 0,
            'spearman_rho' => $spearman_wp
        ]);

        AnalisisPerbandingan::create([
            'id_semester' => $id_semester,
            'id_kelas' => $id_kelas,
            'metode' => 'Borda',
            'waktu_tahap_1' => $bordaResult['timings']['tahap_1'] ?? 0,
            'waktu_tahap_2' => $bordaResult['timings']['tahap_2'] ?? 0,
            'waktu_tahap_3' => $bordaResult['timings']['tahap_3'] ?? 0,
            'waktu_tahap_4' => $bordaResult['timings']['tahap_4'] ?? 0,
            'waktu_tahap_5' => $bordaResult['timings']['tahap_5'] ?? 0,
            'waktu_total' => $bordaResult['timings']['total'] ?? 0,
            'spearman_rho' => $spearman_borda
        ]);

        AnalisisPerbandingan::create([
            'id_semester' => $id_semester,
            'id_kelas' => $id_kelas,
            'metode' => 'Manual',
            'waktu_tahap_1' => $manualResult['timings']['tahap_1'] ?? 0,
            'waktu_tahap_2' => $manualResult['timings']['tahap_2'] ?? 0,
            'waktu_total' => $manualResult['timings']['total'] ?? 0,
            'spearman_rho' => 1.00
        ]);

        return redirect()->route('admin.analisis.show', [
            'id_semester' => $id_semester,
            'id_kelas' => $id_kelas
        ])->with('success', 'Perhitungan dan Analisis Selesai!');
    }
}
