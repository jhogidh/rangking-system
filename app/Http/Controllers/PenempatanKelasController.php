<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Semester;
use App\Models\Kelas;
use App\Models\Siswa;
use App\Models\DataSiswaKelas;
use Illuminate\Validation\Rule;

class PenempatanKelasController extends Controller
{
    public function index(Request $request)
    {
        $semesters = Semester::orderBy('id', 'desc')->get();

        $kelasList = Kelas::orderBy('nama', 'asc')->get();

        $siswaDiLuarKelas = null;
        $siswaDiDalamKelas = null;
        $selectedSemester = null;
        $selectedKelas = null;

        if ($request->filled('id_semester') && $request->filled('id_kelas')) {
            $selectedSemester = Semester::find($request->id_semester);
            $selectedKelas = Kelas::find($request->id_kelas);

            $siswaSudahDitempatkanIds = DataSiswaKelas::where('id_semester', $request->id_semester)
                ->where('id_kelas', $request->id_kelas)
                ->pluck('id_siswa');

            $siswaDiDalamKelas = DataSiswaKelas::where('id_semester', $request->id_semester)
                ->where('id_kelas', $request->id_kelas)
                ->with('siswa')
                ->get();

            $siswaDiKelasLainIds = DataSiswaKelas::where('id_semester', $request->id_semester)
                ->where('id_kelas', '!=', $request->id_kelas)
                ->pluck('id_siswa');

            $excludeIds = $siswaSudahDitempatkanIds->merge($siswaDiKelasLainIds);

            $siswaDiLuarKelas = Siswa::whereNotIn('id', $excludeIds)
                ->orderBy('nama', 'asc')
                ->get();
        }

        return view('layouts.admin.contents.penempatan.index', compact(
            'semesters',
            'kelasList',
            'siswaDiLuarKelas',
            'siswaDiDalamKelas',
            'selectedSemester',
            'selectedKelas'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'id_siswa' => [
                'required',
                'exists:siswa,id',
                Rule::unique('data_siswa_kelas')->where(function ($query) use ($request) {
                    return $query->where('id_siswa', $request->id_siswa)
                        ->where('id_semester', $request->id_semester);
                }),
            ],
            'id_kelas' => 'required|exists:kelas,id',
            'id_semester' => 'required|exists:semester,id',
        ], [
            'id_siswa.unique' => 'Siswa ini sudah ditempatkan di kelas lain pada semester ini.'
        ]);

        DataSiswaKelas::create($validated);

        return redirect()->back()->with('success', 'Siswa berhasil ditempatkan di kelas.');
    }

    public function destroy($id)
    {
        $dataSiswaKelas = DataSiswaKelas::find($id);

        if ($dataSiswaKelas) {
            $dataSiswaKelas->delete();
            return redirect()->back()->with('success', 'Siswa berhasil dikeluarkan dari kelas.');
        }

        return redirect()->back()->with('error', 'Data penempatan tidak ditemukan.');
    }
}
