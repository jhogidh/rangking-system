<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ranking;
use App\Models\Semester;

class LaporanController extends Controller
{
    public function index()
    {
        $semester = Semester::all();
    }

    public function show(Request $request)
    {
        $id_semester = $request->id_semester;

        $hasilRanking = Ranking::whereHas('dataSiswaKelas', function ($query) use ($id_semester) {
            $query->where('id_semester', $id_semester);
        })
            ->with('dataSiswaKelas.siswa', 'dataSiswaKelas.kelas')
            ->orderBy('ranking', 'asc')
            ->get();
    }

    public function detail($id_ranking)
    {
        $ranking = Ranking::with('dataSiswaKelas.skorKriteria.kriteria')->find($id_ranking);
    }
}
