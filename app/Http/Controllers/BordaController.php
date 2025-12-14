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
use App\Models\Siswa;
use App\Services\SPK\BordaService;

class BordaController extends Controller
{
    protected $bordaService;

    public function __construct(BordaService $borda)
    {
        $this->bordaService = $borda;
    }

    private function prepareData(Request $request)
    {
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
        return view('layouts.admin.contents.borda.index', compact('semesters', 'kelasList'));
    }

    public function calculate(Request $request)
    {
        $data = $this->prepareData($request);
        if ($data instanceof \Illuminate\Http\RedirectResponse) return $data;

        $bordaResult = $this->bordaService->calculate($data['alternatives'], $data['criteria']);

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

        AnalisisPerbandingan::updateOrCreate(
            ['id_semester' => $data['id_semester'], 'id_kelas' => $data['id_kelas'], 'metode' => 'Borda'],
            [
                'waktu_tahap_1' => $bordaResult['timings']['tahap_1'] ?? 0,
                'waktu_tahap_2' => $bordaResult['timings']['tahap_2'] ?? 0,
                'waktu_tahap_3' => $bordaResult['timings']['tahap_3'] ?? 0,
                'waktu_tahap_4' => $bordaResult['timings']['tahap_4'] ?? 0,
                'waktu_tahap_5' => $bordaResult['timings']['tahap_5'] ?? 0,
                'waktu_total' => $bordaResult['timings']['total'] ?? 0,
                'spearman_rho' => null,
            ]
        );

        return view('layouts.admin.contents.borda.show-steps', [
            'steps' => $bordaResult['steps'],
            'timings' => $bordaResult['timings'],
            'criteria' => $data['criteria'],
            'siswaMap' => $data['siswaMap'],
        ]);
    }
}
