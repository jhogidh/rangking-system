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
use App\Services\SPK\WeightedProductService;

class WpController extends Controller
{
    protected $wpService;

    public function __construct(WeightedProductService $wp)
    {
        $this->wpService = $wp;
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
        $query = DataSiswaKelas::where('id_semester', $id_semester)->with(['nilaiKriteria', 'siswa']);

        if ($id_kelas) {
            $query->where('id_kelas', $id_kelas);
        }

        $dataSiswaSemester = $query->get();

        if ($dataSiswaSemester->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada data siswa/nilai di semester/kelas yang dipilih.');
        }

        $alternatives = [];
        $usedCriteriaIds = [];
        $siswaMap = []; // Untuk mapping ID ke Nama

        foreach ($dataSiswaSemester as $siswa) {
            $nilaiMap = $siswa->nilaiKriteria->pluck('nilai', 'id_kriteria');
            if ($nilaiMap->isEmpty()) continue;

            $alternatives[$siswa->id] = $nilaiMap;
            // Pastikan relasi 'siswa' ada untuk mengambil nama
            $siswaMap[$siswa->id] = $siswa->siswa->nama ?? 'Siswa ID: ' . $siswa->id;
            $usedCriteriaIds = array_merge($usedCriteriaIds, $nilaiMap->keys()->all());
        }

        if (empty($alternatives)) {
            return redirect()->back()->with('error', 'Siswa ditemukan, tapi data nilai kriteria (hasil import) masih kosong.');
        }

        $criteria = $allCriteria->whereIn('id', array_unique($usedCriteriaIds));

        return [
            'alternatives' => $alternatives,
            'criteria' => $criteria,
            'id_semester' => $id_semester,
            'id_kelas' => $id_kelas,
            'siswaMap' => $siswaMap,
        ];
    }

    /**
     * Menampilkan halaman form pemicu WP (Menu 3 - Index)
     * FUNGSI INI YANG TADI HILANG/UNDEFINED
     */
    public function index()
    {
        $semesters = Semester::with('tahunAjaran')->orderBy('id', 'desc')->get();
        $kelasList = Kelas::orderBy('nama', 'asc')->get();

        // Pastikan view ini ada di resources/views/layouts/admin/contents/wp/index.blade.php
        return view('layouts.admin.contents.wp.index', compact('semesters', 'kelasList'));
    }

    /**
     * Menjalankan perhitungan WP dan menampilkan semua langkah
     */
    public function calculate(Request $request)
    {
        $data = $this->prepareData($request);

        // Jika prepareData me-return redirect (karena error), teruskan return-nya
        if ($data instanceof \Illuminate\Http\RedirectResponse) {
            return $data;
        }

        // 1. Jalankan Service WP
        $wpResult = $this->wpService->calculate($data['alternatives'], $data['criteria']);

        // 2. Simpan hasil ranking (untuk Laporan Menu 4)
        // Hapus ranking lama untuk siswa yang dihitung saat ini saja
        $processedSiswaIds = array_keys($data['alternatives']);
        Ranking::whereIn('id_data_siswa_kelas', $processedSiswaIds)
            ->where('metode', 'WP')
            ->delete();

        $rank = 1;
        foreach ($wpResult['steps']['vector_v'] as $id_data_siswa_kelas => $nilai_alternatif) {
            Ranking::create([
                'id_data_siswa_kelas' => $id_data_siswa_kelas,
                'metode' => 'WP',
                'hasil_alternatif' => $nilai_alternatif,
                'ranking' => $rank++,
            ]);
        }

        // 3. Simpan hasil statistik (untuk Laporan Menu 5)
        AnalisisPerbandingan::updateOrCreate(
            [
                'id_semester' => $data['id_semester'],
                'id_kelas' => $data['id_kelas'],
                'metode' => 'WP',
            ],
            [
                'waktu_tahap_1' => $wpResult['timings']['tahap_1'] ?? 0,
                'waktu_tahap_2' => $wpResult['timings']['tahap_2'] ?? 0,
                'waktu_tahap_3' => $wpResult['timings']['tahap_3'] ?? 0,
                'waktu_tahap_4' => $wpResult['timings']['tahap_4'] ?? 0,
                'waktu_total' => $wpResult['timings']['total'] ?? 0,
                // Spearman dihitung di AnalisisController, biarkan null/abaikan
            ]
        );

        // 4. Kirim semua data langkah ke view
        return view('layouts.admin.contents.wp.show-steps', [
            'steps' => $wpResult['steps'],
            'timings' => $wpResult['timings'],
            'criteria' => $data['criteria'],
            'siswaMap' => $data['siswaMap'],
        ]);
    }
}
