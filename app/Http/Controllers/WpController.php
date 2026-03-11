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
use App\Support\RankingHelper;
use Illuminate\Support\Collection;

class WpController extends Controller
{
    private const ACADEMIC_CRITERIA_NAMES = ['Nilai', 'Sikap', 'Sikap/Akhlak', 'Absensi'];
    private const NON_ACADEMIC_CRITERIA_NAMES = ['Prestasi', 'Ekstrakurikuler'];

    protected $wpService;

    public function __construct(WeightedProductService $wp)
    {
        $this->wpService = $wp;
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
        return view('layouts.admin.contents.wp.index', compact('semesters', 'kelasList'));
    }

    public function calculate(Request $request)
    {
        $data = $this->prepareData($request);
        if ($data instanceof \Illuminate\Http\RedirectResponse) return $data;

        $wpResult = $this->wpService->calculate($data['alternatives'], $data['criteria']);
        $this->saveRankings($wpResult, $data['alternatives'], Ranking::CATEGORY_ALL);

        AnalisisPerbandingan::updateOrCreate(
            ['id_semester' => $data['id_semester'], 'id_kelas' => $data['id_kelas'], 'metode' => 'WP'],
            [
                'waktu_tahap_1' => $wpResult['timings']['tahap_1'] ?? 0,
                'waktu_tahap_2' => $wpResult['timings']['tahap_2'] ?? 0,
                'waktu_tahap_3' => $wpResult['timings']['tahap_3'] ?? 0,
                'waktu_tahap_4' => $wpResult['timings']['tahap_4'] ?? 0,
                'waktu_total' => $wpResult['timings']['total'] ?? 0
            ]
        );

        return view('layouts.admin.contents.wp.show-steps', [
            'steps' => $wpResult['steps'],
            'timings' => $wpResult['timings'],
            'criteria' => $data['criteria'],
            'siswaMap' => $data['siswaMap'],
            'ranks' => RankingHelper::denseRanks($wpResult['steps']['vector_v']),
            'kategoriLabel' => $this->categoryLabel(Ranking::CATEGORY_ALL),
        ]);
    }

    public function calculateAkademik(Request $request)
    {
        $data = $this->prepareData($request);
        if ($data instanceof \Illuminate\Http\RedirectResponse) return $data;

        $criteria = $this->filterCriteriaByCategory($data['criteria'], Ranking::CATEGORY_AKADEMIK);
        if ($criteria->isEmpty()) return redirect()->back()->with('error', 'Kriteria akademik tidak ditemukan.');

        $criteriaIds = $criteria->pluck('id')->toArray();
        $alternatives = $this->filterAlternativesByCriteria($data['alternatives'], $criteriaIds);
        if (empty($alternatives)) return redirect()->back()->with('error', 'Data nilai akademik tidak ditemukan.');

        $wpResult = $this->wpService->calculate($alternatives, $criteria);
        $this->saveRankings($wpResult, $alternatives, Ranking::CATEGORY_AKADEMIK);

        $scores = $wpResult['steps']['vector_v'];
        $ranks = RankingHelper::denseRanks($scores);

        return view('layouts.admin.contents.wp.show-steps', [
            'steps' => $wpResult['steps'],
            'timings' => $wpResult['timings'],
            'criteria' => $criteria,
            'siswaMap' => $data['siswaMap'],
            'ranks' => $ranks,
            'kategoriLabel' => $this->categoryLabel(Ranking::CATEGORY_AKADEMIK),
        ]);
    }

    public function calculateNonAkademik(Request $request)
    {
        $data = $this->prepareData($request);
        if ($data instanceof \Illuminate\Http\RedirectResponse) return $data;

        $criteria = $this->filterCriteriaByCategory($data['criteria'], Ranking::CATEGORY_NON_AKADEMIK);
        if ($criteria->isEmpty()) return redirect()->back()->with('error', 'Kriteria non-akademik tidak ditemukan.');

        $criteriaIds = $criteria->pluck('id')->toArray();
        $alternatives = $this->filterAlternativesByCriteria($data['alternatives'], $criteriaIds);
        if (empty($alternatives)) return redirect()->back()->with('error', 'Data nilai non-akademik tidak ditemukan.');

        $wpResult = $this->wpService->calculate($alternatives, $criteria);
        $this->saveRankings($wpResult, $alternatives, Ranking::CATEGORY_NON_AKADEMIK);

        $scores = $wpResult['steps']['vector_v'];
        $ranks = RankingHelper::denseRanks($scores);

        return view('layouts.admin.contents.wp.show-steps', [
            'steps' => $wpResult['steps'],
            'timings' => $wpResult['timings'],
            'criteria' => $criteria,
            'siswaMap' => $data['siswaMap'],
            'ranks' => $ranks,
            'kategoriLabel' => $this->categoryLabel(Ranking::CATEGORY_NON_AKADEMIK),
        ]);
    }

    private function saveRankings(array $wpResult, array $alternatives, string $category): void
    {
        $processedSiswaIds = array_keys($alternatives);
        Ranking::whereIn('id_data_siswa_kelas', $processedSiswaIds)
            ->where('metode', 'WP')
            ->where('kategori', $category)
            ->delete();

        $scores = $wpResult['steps']['vector_v'];
        $ranks = RankingHelper::denseRanks($scores);

        foreach ($scores as $id_data_siswa_kelas => $nilai_alternatif) {
            Ranking::create([
                'id_data_siswa_kelas' => $id_data_siswa_kelas,
                'metode' => 'WP',
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
