<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Semester;
use App\Models\TahunAjaran; // <-- Import TahunAjaran
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SemesterController extends Controller
{
    public function index()
    {
        // Tidak perlu with('tahunAjaran') lagi
        $semester = Semester::latest()->paginate(10);
        return view('layouts.admin.contents.semester.index', compact('semester'));
    }

    public function create()
    {
        // Tidak perlu ambil data tahun ajaran
        return view('layouts.admin.contents.semester.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100', // Ganjil/Genap
            'tahun_mulai' => 'required|digits:4|integer|min:2000',
            'tahun_selesai' => 'required|digits:4|integer|min:2000|gte:tahun_mulai',
        ]);

        Semester::create($request->all());

        return redirect()->route('proses.semester.index')
            ->with('success', 'Semester berhasil ditambahkan.');
    }

    public function edit(Semester $semester)
    {
        return view('layouts.admin.contents.semester.edit', compact('semester'));
    }

    public function update(Request $request, Semester $semester)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'tahun_mulai' => 'required|digits:4|integer|min:2000',
            'tahun_selesai' => 'required|digits:4|integer|min:2000|gte:tahun_mulai',
        ]);

        $semester->update($request->all());

        return redirect()->route('proses.semester.index')
            ->with('success', 'Semester berhasil diperbarui.');
    }

    public function destroy(Semester $semester)
    {
        if ($semester->dataSiswaKelas()->count() > 0) {
            return redirect()->route('proses.semester.index')
                ->with('error', 'Gagal! Semester ini memiliki data siswa.');
        }

        $semester->delete();

        return redirect()->route('proses.semester.index')
            ->with('success', 'Semester berhasil dihapus.');
    }
}
