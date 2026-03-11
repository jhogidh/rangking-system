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
use App\Services\SPK\BordaService;
use App\Support\RankingHelper;
use Illuminate\Support\Collection;

class BordaController extends Controller
{
    private const ACADEMIC_CRITERIA_NAMES = ['Nilai', 'Sikap', 'Sikap/Akhlak', 'Absensi'];
    private const NON_ACADEMIC_CRITERIA_NAMES = ['Prestasi', 'Ekstrakurikuler'];

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
            $alternatives[$siswa->id] = $nilaiMap->toArray();
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
        $this->saveRankings($bordaResult, $data['alternatives'], Ranking::CATEGORY_ALL);

        AnalisisPerbandingan::updateOrCreate(
            ['id_semester' => $data['id_semester'], 'id_kelas' => $data['id_kelas'], 'metode' => 'Borda'],
            [
                'waktu_tahap_1' => $bordaResult['timings']['tahap_1'] ?? 0,
                'waktu_tahap_2' => $bordaResult['timings']['tahap_2'] ?? 0,
                'waktu_tahap_3' => $bordaResult['timings']['tahap_3'] ?? 0,
                'waktu_tahap_4' => $bordaResult['timings']['tahap_4'] ?? 0,
                'waktu_tahap_5' => $bordaResult['timings']['tahap_5'] ?? 0,
                'waktu_total' => $bordaResult['timings']['total'] ?? 0
            ]
        );

        return view('layouts.admin.contents.borda.show-steps', [
            'steps' => $bordaResult['steps'],
            'timings' => $bordaResult['timings'],
            'criteria' => $data['criteria'],
            'siswaMap' => $data['siswaMap'],
            'ranks' => RankingHelper::denseRanks($bordaResult['steps']['final_scores']),
            'kategoriLabel' => $this->categoryLabel(Ranking::CATEGORY_ALL),
        ]);
    }


    public function calculateAkademik(Request $request)
    {
        $data = $this->prepareData($request);

        if ($data instanceof \Illuminate\Http\RedirectResponse) {
            return $data;
        }

        $criteria = $this->filterCriteriaByCategory($data['criteria'], Ranking::CATEGORY_AKADEMIK);
        if ($criteria->isEmpty()) return redirect()->back()->with('error', 'Kriteria akademik tidak ditemukan.');

        $criteriaIds = $criteria->pluck('id')->toArray();
        $alternatives = $this->filterAlternativesByCriteria($data['alternatives'], $criteriaIds);
        if (empty($alternatives)) return redirect()->back()->with('error', 'Data nilai akademik tidak ditemukan.');

        $bordaResult = $this->bordaService->calculate($alternatives, $criteria);

        return $this->saveAndShow($bordaResult, $data, $criteria, $alternatives, Ranking::CATEGORY_AKADEMIK);
    }

    public function calculateNonAkademik(Request $request)
    {
        $data = $this->prepareData($request);

        if ($data instanceof \Illuminate\Http\RedirectResponse) {
            return $data;
        }

        $criteria = $this->filterCriteriaByCategory($data['criteria'], Ranking::CATEGORY_NON_AKADEMIK);
        if ($criteria->isEmpty()) return redirect()->back()->with('error', 'Kriteria non-akademik tidak ditemukan.');

        $criteriaIds = $criteria->pluck('id')->toArray();
        $alternatives = $this->filterAlternativesByCriteria($data['alternatives'], $criteriaIds);
        if (empty($alternatives)) return redirect()->back()->with('error', 'Data nilai non-akademik tidak ditemukan.');

        $bordaResult = $this->bordaService->calculate($alternatives, $criteria);

        return $this->saveAndShow($bordaResult, $data, $criteria, $alternatives, Ranking::CATEGORY_NON_AKADEMIK);
    }

    private function saveAndShow($bordaResult, $data, $criteria, array $alternatives, string $category)
    {
        $this->saveRankings($bordaResult, $alternatives, $category);
        $ranks = RankingHelper::denseRanks($bordaResult['steps']['final_scores']);

        return view('layouts.admin.contents.borda.show-steps', [
            'steps' => $bordaResult['steps'],
            'timings' => $bordaResult['timings'],
            'criteria' => $criteria,
            'siswaMap' => $data['siswaMap'],
            'ranks' => $ranks,
            'kategoriLabel' => $this->categoryLabel($category),
        ]);
    }

    private function saveRankings(array $bordaResult, array $alternatives, string $category): void
    {
        $processedSiswaIds = array_keys($alternatives);

        Ranking::whereIn('id_data_siswa_kelas', $processedSiswaIds)
            ->where('metode', 'Borda')
            ->where('kategori', $category)
            ->delete();

        $scores = $bordaResult['steps']['final_scores'];
        $ranks = RankingHelper::denseRanks($scores);

        foreach ($scores as $id_data_siswa_kelas => $nilai_alternatif) {
            Ranking::create([
                'id_data_siswa_kelas' => $id_data_siswa_kelas,
                'metode' => 'Borda',
                'kategori' => $category,
                'hasil_alternatif' => $nilai_alternatif,
                'ranking' => $ranks[$id_data_siswa_kelas],
            ]);
        }
    }

    private function filterAlternativesByCriteria(array $alternatives, array $criteriaIds): array
    {
        $filteredAlternatives = [];
        foreach ($alternatives as $altId => $values) {
            $filtered = collect($values)->only($criteriaIds)->toArray();
            if (!empty($filtered)) {
                $filteredAlternatives[$altId] = $filtered;
            }
        }
        return $filteredAlternatives;
    }

    private function filterCriteriaByCategory(Collection $criteria, string $category): Collection
    {
        if ($category === Ranking::CATEGORY_AKADEMIK) {
            return $criteria->whereIn('nama', self::ACADEMIC_CRITERIA_NAMES);
        }

        if ($category === Ranking::CATEGORY_NON_AKADEMIK) {
            return $criteria->whereIn('nama', self::NON_ACADEMIC_CRITERIA_NAMES);
        }

        return $criteria;
    }

    private function categoryLabel(string $category): string
    {
        return match ($category) {
            Ranking::CATEGORY_AKADEMIK => 'Akademik',
            Ranking::CATEGORY_NON_AKADEMIK => 'Non Akademik',
            default => 'Semua Kriteria',
        };
    }
}
