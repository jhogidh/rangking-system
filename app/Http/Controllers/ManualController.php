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
use App\Services\SPK\ManualService;

class ManualController extends Controller
{
    protected $manualService;

    public function __construct(ManualService $manual)
    {
        $this->manualService = $manual;
    }

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

        if ($id_kelas) $query->where('id_kelas', $id_kelas);

        $dataSiswaSemester = $query->get();

        if ($dataSiswaSemester->isEmpty()) return redirect()->back()->with('error', 'Tidak ada data siswa/nilai di semester/kelas yang dipilih.');

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

        if (empty($alternatives)) return redirect()->back()->with('error', 'Siswa ditemukan, tapi data nilai kriteria (hasil import) masih kosong.');

        $criteria = $allCriteria->whereIn('id', array_unique($usedCriteriaIds));

        return [
            'alternatives' => $alternatives,
            'criteria' => $criteria,
            'id_semester' => $id_semester,
            'id_kelas' => $id_kelas,
            'siswaMap' => $siswaMap,
        ];
    }

    public function index()
    {
        $semesters = Semester::orderBy('id', 'desc')->get();
        $kelasList = Kelas::orderBy('nama', 'asc')->get();
        return view('layouts.admin.contents.manual.index', compact('semesters', 'kelasList'));
    }

    public function calculate(Request $request)
    {
        $data = $this->prepareData($request);
        if ($data instanceof \Illuminate\Http\RedirectResponse) return $data;

        $manualResult = $this->manualService->calculate($data['alternatives'], $data['criteria']);

        $processedSiswaIds = array_keys($data['alternatives']);
        Ranking::whereIn('id_data_siswa_kelas', $processedSiswaIds)->where('metode', 'Manual')->delete();

        $rank = 1;
        $scores = $manualResult['steps']['final_scores'];

        foreach ($scores as $id_data_siswa_kelas => $nilai_alternatif) {
            Ranking::create([
                'id_data_siswa_kelas' => $id_data_siswa_kelas,
                'metode' => 'Manual',
                'hasil_alternatif' => $nilai_alternatif,
                'ranking' => $rank++,
            ]);
        }

        $queryStat = AnalisisPerbandingan::where('id_semester', $data['id_semester'])->where('metode', 'Manual');
        if ($data['id_kelas']) $queryStat->where('id_kelas', $data['id_kelas']);
        else $queryStat->whereNull('id_kelas');
        $queryStat->delete();

        AnalisisPerbandingan::create([
            'id_semester' => $data['id_semester'],
            'id_kelas' => $data['id_kelas'],
            'metode' => 'Manual',
            'waktu_tahap_1' => $manualResult['timings']['tahap_1'] ?? 0,
            'waktu_tahap_2' => $manualResult['timings']['tahap_2'] ?? 0,
            'waktu_total' => $manualResult['timings']['total'] ?? 0,
            'spearman_rho' => 1.00
        ]);

        return view('layouts.admin.contents.manual.show-steps', [
            'steps' => $manualResult['steps'],
            'timings' => $manualResult['timings'],
            'criteria' => $data['criteria'],
            'siswaMap' => $data['siswaMap'],
        ]);
    }
}
