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

    // (Gunakan logika prepareData yang SAMA dengan ManualController)
    private function prepareData(Request $request)
    { /* ... Copy logic prepareData ... */
        $request->validate(['id_semester' => 'required|exists:semester,id', 'id_kelas' => 'nullable|exists:kelas,id']);
        $id_semester = $request->id_semester;
        $id_kelas = $request->id_kelas;
        $allCriteria = Kriteria::all();
        $query = DataSiswaKelas::where('id_semester', $id_semester)->with(['nilaiKriteria', 'siswa']);
        if ($id_kelas) $query->where('id_kelas', $id_kelas);
        $dataSiswaSemester = $query->get();
        if ($dataSiswaSemester->isEmpty()) return redirect()->back()->with('error', 'Tidak ada data siswa.');
        $alternatives = [];
        $usedCriteriaIds = [];
        $siswaMap = [];
        foreach ($dataSiswaSemester as $siswa) {
            $nilaiMap = $siswa->nilaiKriteria->pluck('nilai', 'id_kriteria');
            if ($nilaiMap->isEmpty()) continue;
            $alternatives[$siswa->id] = $nilaiMap;
            $siswaMap[$siswa->id] = $siswa->siswa->nama;
            $usedCriteriaIds = array_merge($usedCriteriaIds, $nilaiMap->keys()->all());
        }
        if (empty($alternatives)) return redirect()->back()->with('error', 'Siswa ditemukan, tapi data nilai kosong.');
        $criteria = $allCriteria->whereIn('id', array_unique($usedCriteriaIds));
        return ['alternatives' => $alternatives, 'criteria' => $criteria, 'id_semester' => $id_semester, 'id_kelas' => $id_kelas, 'siswaMap' => $siswaMap];
    }

    public function index()
    {
        $semesters = Semester::orderBy('id', 'desc')->get();
        $kelasList = Kelas::orderBy('nama', 'asc')->get();
        return view('layouts.admin.contents.wp.index', compact('semesters', 'kelasList'));
    }

    public function calculate(Request $request)
    {
        $data = $this->prepareData($request);
        if ($data instanceof \Illuminate\Http\RedirectResponse) return $data;

        $wpResult = $this->wpService->calculate($data['alternatives'], $data['criteria']);

        $processedSiswaIds = array_keys($data['alternatives']);
        Ranking::whereIn('id_data_siswa_kelas', $processedSiswaIds)->where('metode', 'WP')->delete();

        $rank = 1;
        foreach ($wpResult['steps']['vector_v'] as $id_data_siswa_kelas => $nilai_alternatif) {
            Ranking::create([
                'id_data_siswa_kelas' => $id_data_siswa_kelas,
                'metode' => 'WP',
                'hasil_alternatif' => $nilai_alternatif,
                'ranking' => $rank++,
            ]);
        }

        AnalisisPerbandingan::updateOrCreate(
            ['id_semester' => $data['id_semester'], 'id_kelas' => $data['id_kelas'], 'metode' => 'WP'],
            [
                'waktu_tahap_1' => $wpResult['timings']['tahap_1'] ?? 0,
                'waktu_tahap_2' => $wpResult['timings']['tahap_2'] ?? 0,
                'waktu_tahap_3' => $wpResult['timings']['tahap_3'] ?? 0,
                'waktu_tahap_4' => $wpResult['timings']['tahap_4'] ?? 0,
                'waktu_total' => $wpResult['timings']['total'] ?? 0,
                'spearman_rho' => null,
            ]
        );

        return view('layouts.admin.contents.wp.show-steps', [
            'steps' => $wpResult['steps'],
            'timings' => $wpResult['timings'],
            'criteria' => $data['criteria'],
            'siswaMap' => $data['siswaMap'],
        ]);
    }
}
