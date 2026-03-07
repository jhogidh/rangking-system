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
use App\Support\RankingHelper;
use Illuminate\Support\Collection;

class ManualController extends Controller
{
    private const ACADEMIC_CRITERIA_NAMES = ['Nilai', 'Sikap', 'Sikap/Akhlak', 'Absensi'];
    private const NON_ACADEMIC_CRITERIA_NAMES = ['Prestasi', 'Ekstrakurikuler'];

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

            $alternatives[$siswa->id] = $nilaiMap->toArray();
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
        return $this->calculateByCategory($request, Ranking::CATEGORY_ALL);
    }

    public function calculateAkademik(Request $request)
    {
        return $this->calculateByCategory($request, Ranking::CATEGORY_AKADEMIK);
    }

    public function calculateNonAkademik(Request $request)
    {
        return $this->calculateByCategory($request, Ranking::CATEGORY_NON_AKADEMIK);
    }

    private function calculateByCategory(Request $request, string $category)
    {
        $data = $this->prepareData($request);
        if ($data instanceof \Illuminate\Http\RedirectResponse) return $data;

        $categoryCriteria = $this->filterCriteriaByCategory($data['criteria'], $category);
        if ($categoryCriteria->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada kriteria untuk kategori yang dipilih.');
        }

        $criteriaIds = $categoryCriteria->pluck('id')->all();
        $categoryAlternatives = $this->filterAlternativesByCriteria($data['alternatives'], $criteriaIds);
        if (empty($categoryAlternatives)) {
            return redirect()->back()->with('error', 'Tidak ada data nilai untuk kategori kriteria yang dipilih.');
        }

        $manualResult = $this->manualService->calculate($categoryAlternatives, $categoryCriteria);

        $processedSiswaIds = array_keys($categoryAlternatives);
        Ranking::whereIn('id_data_siswa_kelas', $processedSiswaIds)
            ->where('metode', 'Manual')
            ->where('kategori', $category)
            ->delete();

        $scores = $manualResult['steps']['final_scores'];
        $ranks = RankingHelper::denseRanks($scores);

        foreach ($scores as $id_data_siswa_kelas => $nilai_alternatif) {
            Ranking::create([
                'id_data_siswa_kelas' => $id_data_siswa_kelas,
                'metode' => 'Manual',
                'kategori' => $category,
                'hasil_alternatif' => $nilai_alternatif,
                'ranking' => $ranks[$id_data_siswa_kelas],
            ]);
        }

        if ($category === Ranking::CATEGORY_ALL) {
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
        }

        return view('layouts.admin.contents.manual.show-steps', [
            'steps' => $manualResult['steps'],
            'timings' => $manualResult['timings'],
            'criteria' => $categoryCriteria,
            'siswaMap' => $data['siswaMap'],
            'ranks' => $ranks,
            'kategoriLabel' => $this->categoryLabel($category),
        ]);
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
