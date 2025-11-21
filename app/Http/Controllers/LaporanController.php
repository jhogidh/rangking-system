<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ranking;
use App\Models\Semester;

class LaporanController extends Controller
{
    public function index()
    {
        // Tampilkan form untuk pilih semester
        $semester = Semester::all();
        // return view('laporan.index', compact('semester'));
    }

    public function show(Request $request)
    {
        // Tampilkan hasil ranking berdasarkan semester yang dipilih
        $id_semester = $request->id_semester;

        $hasilRanking = Ranking::whereHas('dataSiswaKelas', function ($query) use ($id_semester) {
            $query->where('id_semester', $id_semester);
        })
            ->with('dataSiswaKelas.siswa', 'dataSiswaKelas.kelas')
            ->orderBy('ranking', 'asc')
            ->get();

        // return view('laporan.show', compact('hasilRanking'));
    }

    public function detail($id_ranking)
    {
        // Tampilkan detail skor per kriteria untuk satu siswa
        $ranking = Ranking::with('dataSiswaKelas.skorKriteria.kriteria')->find($id_ranking);
        // return view('laporan.detail', compact('ranking'));
    }
}
