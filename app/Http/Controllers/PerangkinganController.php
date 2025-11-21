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

class PerangkinganController extends Controller
{
    // ... (Inject semua service di __construct) ...
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


    /**
     * Menampilkan halaman pemicu perankingan
     */
    public function index()
    {
        $semesters = Semester::with('tahunAjaran')->orderBy('id', 'desc')->get();
        $kelasList = Kelas::orderBy('nama', 'asc')->get();

        // (Pastikan path view-mu 'perankingan' bukan 'perangkingan')
        return view('layouts.admin.contents.perankingan.index', compact(
            'semesters',
            'kelasList'
        ));
    }


    /**
     * "Otak" Perhitungan SPK
     */
    public function hitungDanAnalisis(Request $request)
    {
        $id_semester = $request->id_semester;
        $id_kelas = $request->id_kelas; // Ini bisa null (jika 'Semua Kelas')

        // =================================================================
        // 1. PERSIAPAN DATA (LOGIKA BARU YANG LEBIH SIMPEL)
        // =================================================================

        // Ambil SEMUA kriteria dari DB
        $allCriteria = Kriteria::all();

        // Mulai query ke DataSiswaKelas
        $query = DataSiswaKelas::where('id_semester', $id_semester)
            // Load relasi nilaiKriteria yang BARU kita buat
            ->with('nilaiKriteria');

        // Jika user memilih kelas spesifik, filter berdasarkan id_kelas
        if ($id_kelas) {
            $query->where('id_kelas', $id_kelas);
        }

        $dataSiswaSemester = $query->get();

        // Cek jika tidak ada siswa
        if ($dataSiswaSemester->isEmpty()) {
            return redirect()->back()
                ->with('error', 'Tidak ada data siswa di semester/kelas yang dipilih. (Pastikan Alur B "Import Nilai" sudah dijalankan).');
        }

        // Siapkan array $alternatives
        $alternatives = [];
        $usedCriteriaIds = []; // Catat kriteria apa saja yang punya nilai

        foreach ($dataSiswaSemester as $siswa) {
            // Ubah Collection [ {id_kriteria: 1, nilai: 80}, {id_kriteria: 2, nilai: 90} ]
            // Menjadi array [ 1 => 80, 2 => 90 ]
            $nilaiMap = $siswa->nilaiKriteria->pluck('nilai', 'id_kriteria');

            // Cek jika siswa tidak punya nilai sama sekali
            if ($nilaiMap->isEmpty()) {
                continue;
            }

            // Masukkan ke array $alternatives
            $alternatives[$siswa->id] = $nilaiMap;

            // Catat semua ID kriteria yang baru saja kita tambahkan
            $usedCriteriaIds = array_merge($usedCriteriaIds, $nilaiMap->keys()->all());
        }

        // Cek jika ada siswa, tapi tidak ada nilai kriteria satupun
        if (empty($alternatives)) {
            return redirect()->back()
                ->with('error', 'Siswa ditemukan, tapi data nilai kriteria (hasil import) masih kosong. (Pastikan Alur B-2 "Import Nilai" sudah dijalankan).');
        }

        // Filter $allCriteria, HANYA ambil kriteria yang nilainya ada di $alternatives
        $criteria = $allCriteria->whereIn('id', array_unique($usedCriteriaIds));

        // =================================================================
        // 2. Jalankan Perhitungan
        // =================================================================
        $wpResult = $this->wpService->calculate($alternatives, $criteria);
        $bordaResult = $this->bordaService->calculate($alternatives, $criteria);
        $manualResult = $this->manualService->calculate($alternatives, $criteria);

        // =================================================================
        // 3. Simpan Hasil Ranking ke tabel 'ranking'
        // =================================================================

        $processedSiswaIds = array_keys($alternatives);
        Ranking::whereIn('id_data_siswa_kelas', $processedSiswaIds)->delete();

        $saveRanking = function ($result, $metode) {
            $rank = 1;
            foreach ($result['values'] as $id_data_siswa_kelas => $nilai_alternatif) {
                Ranking::create([
                    'id_data_siswa_kelas' => $id_data_siswa_kelas,
                    'metode' => $metode,
                    'hasil_alternatif' => $nilai_alternatif,
                    'ranking' => $rank++,
                ]);
            }
        };

        $saveRanking($wpResult, 'WP');
        $saveRanking($bordaResult, 'Borda');
        $saveRanking($manualResult, 'Manual');

        // =================================================================
        // 4. Jalankan Analisis Korelasi (Akurasi)
        // =================================================================
        $manualRanks = array_map(fn($r) => $r + 1, array_flip(array_keys($manualResult['values'])));
        $wpRanks = array_map(fn($r) => $r + 1, array_flip(array_keys($wpResult['values'])));
        $bordaRanks = array_map(fn($r) => $r + 1, array_flip(array_keys($bordaResult['values'])));

        $spearman_wp = $this->analysisService->calculateSpearman($manualRanks, $wpRanks);
        $spearman_borda = $this->analysisService->calculateSpearman($manualRanks, $bordaRanks);

        // =================================================================
        // 5. Simpan Statistik Waktu & Akurasi (INI YANG HILANG)
        // =================================================================
        // Hapus data statistik lama
        $queryStatistik = AnalisisPerbandingan::where('id_semester', $id_semester);
        if ($id_kelas) {
            $queryStatistik->where('id_kelas', $id_kelas);
        } else {
            $queryStatistik->whereNull('id_kelas');
        }
        $queryStatistik->delete();

        // Simpan data WP
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

        // Simpan data Borda
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

        // Simpan data Manual
        AnalisisPerbandingan::create([
            'id_semester' => $id_semester,
            'id_kelas' => $id_kelas,
            'metode' => 'Manual',
            'waktu_tahap_1' => $manualResult['timings']['tahap_1'] ?? 0,
            'waktu_tahap_2' => $manualResult['timings']['tahap_2'] ?? 0,
            'waktu_total' => $manualResult['timings']['total'] ?? 0,
            'spearman_rho' => 1.00
        ]);

        // Arahkan ke halaman hasil
        return redirect()->route('admin.analisis.show', [
            'id_semester' => $id_semester,
            'id_kelas' => $id_kelas
        ])->with('success', 'Perhitungan dan Analisis Selesai!');
    }
}
