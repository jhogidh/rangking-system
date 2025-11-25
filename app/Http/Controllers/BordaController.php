<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Semester;
use App\Models\Kelas;
use App\Models\Kriteria;
use App\Models\DataSiswaKelas;
use App\Models\Ranking;
use App\Models\AnalisisPerbandingan;
use App\Models\Siswa; // Kita butuh ini
use App\Services\SPK\BordaService;

class BordaController extends Controller
{
    protected $bordaService;

    public function __construct(BordaService $borda)
    {
        $this->bordaService = $borda;
    }

    /**
     * Helper privat untuk mengambil data mentah
     */
    private function prepareData(Request $request)
    {
        $request->validate([
            'id_semester' => 'required|exists:semester,id',
            'id_kelas' => 'nullable|exists:kelas,id',
        ]);

        $id_semester = $request->id_semester;
        $id_kelas = $request->id_kelas;

        $allCriteria = Kriteria::all();
        $query = DataSiswaKelas::where('id_semester', $id_semester)->with('nilaiKriteria');

        if ($id_kelas) $query->where('id_kelas', $id_kelas);

        $dataSiswaSemester = $query->get();

        if ($dataSiswaSemester->isEmpty()) return redirect()->back()->with('error', 'Tidak ada data siswa/nilai di semester/kelas yang dipilih.');

        $alternatives = [];
        $usedCriteriaIds = [];
        $siswaMap = []; // Untuk mapping ID ke Nama

        foreach ($dataSiswaSemester as $siswa) {
            $nilaiMap = $siswa->nilaiKriteria->pluck('nilai', 'id_kriteria');
            if ($nilaiMap->isEmpty()) continue;

            $alternatives[$siswa->id] = $nilaiMap;
            $siswaMap[$siswa->id] = $siswa->siswa->nama; // Simpan nama siswa
            $usedCriteriaIds = array_merge($usedCriteriaIds, $nilaiMap->keys()->all());
        }

        if (empty($alternatives)) return redirect()->back()->with('error', 'Siswa ditemukan, tapi data nilai kriteria (hasil import) masih kosong.');

        // Ambil semua kriteria tanpa filter, urutkan sesuai prioritas yang baru kita buat
        $criteria = $allCriteria->sortBy('prioritas');

        return [
            'alternatives' => $alternatives,
            'criteria' => $criteria,
            'id_semester' => $id_semester,
            'id_kelas' => $id_kelas,
            'siswaMap' => $siswaMap, // Kirim map nama siswa
        ];
    }

    /**
     * Menampilkan halaman form pemicu Borda (Menu 2 - Index)
     */
    public function index()
    {
        $semesters = Semester::with('tahunAjaran')->orderBy('id', 'desc')->get();
        $kelasList = Kelas::orderBy('nama', 'asc')->get();
        return view('layouts.admin.contents.borda.index', compact('semesters', 'kelasList'));
    }

    /**
     * Menjalankan perhitungan Borda dan menampilkan semua langkah
     */
    public function calculate(Request $request)
    {
        $data = $this->prepareData($request);
        if ($data instanceof \Illuminate\Http\RedirectResponse) return $data; // Return error jika gagal

        // 1. Jalankan Service Borda
        $bordaResult = $this->bordaService->calculate($data['alternatives'], $data['criteria']);

        // 2. Simpan hasil ranking (untuk Laporan Menu 4)
        $processedSiswaIds = array_keys($data['alternatives']);
        Ranking::whereIn('id_data_siswa_kelas', $processedSiswaIds)->where('metode', 'Borda')->delete();

        $rank = 1;
        foreach ($bordaResult['steps']['final_scores'] as $id_data_siswa_kelas => $nilai_alternatif) {
            Ranking::create([
                'id_data_siswa_kelas' => $id_data_siswa_kelas,
                'metode' => 'Borda',
                'hasil_alternatif' => $nilai_alternatif,
                'ranking' => $rank++,
            ]);
        }

        // 3. Simpan hasil statistik (untuk Laporan Menu 5)
        AnalisisPerbandingan::updateOrCreate(
            [
                'id_semester' => $data['id_semester'],
                'id_kelas' => $data['id_kelas'],
                'metode' => 'Borda',
            ],
            [
                'waktu_tahap_1' => $bordaResult['timings']['tahap_1'] ?? 0,
                'waktu_tahap_2' => $bordaResult['timings']['tahap_2'] ?? 0,
                'waktu_tahap_3' => $bordaResult['timings']['tahap_3'] ?? 0,
                'waktu_tahap_4' => $bordaResult['timings']['tahap_4'] ?? 0,
                'waktu_tahap_5' => $bordaResult['timings']['tahap_5'] ?? 0,
                'waktu_total' => $bordaResult['timings']['total'] ?? 0,
                'spearman_rho' => null, // Spearman dihitung di AnalisisController
            ]
        );

        // 4. Kirim semua data langkah ke view
        return view('layouts.admin.contents.borda.show-steps', [
            'steps' => $bordaResult['steps'],
            'timings' => $bordaResult['timings'],
            'criteria' => $data['criteria'],
            'siswaMap' => $data['siswaMap'],
        ]);
    }
}
