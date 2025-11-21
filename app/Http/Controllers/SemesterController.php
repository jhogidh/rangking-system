<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Semester;
use App\Models\TahunAjaran; // <-- Import TahunAjaran
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SemesterController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Tampilkan relasi 'tahunAjaran'
        $semester = Semester::with('tahunAjaran')->latest()->paginate(10);
        return view('layouts.admin.contents.semester.index', compact('semester'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Ambil data tahun ajaran untuk dropdown
        $tahunAjaran = TahunAjaran::orderBy('tahun_mulai', 'desc')->get();
        return view('layouts.admin.contents.semester.create', compact('tahunAjaran'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'id_tahun_ajaran' => 'required|exists:tahun_ajaran,id',
            // Pastikan 'nama' dan 'id_tahun_ajaran' unik bersamaan
            Rule::unique('semester')->where(function ($query) use ($request) {
                return $query->where('id_tahun_ajaran', $request->id_tahun_ajaran);
            }),
        ]);

        Semester::create($request->all());

        return redirect()->route('admin.semester.index')
            ->with('success', 'Semester berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Semester $semester) // Route Model Binding
    {
        // Ambil data tahun ajaran untuk dropdown
        $tahunAjaran = TahunAjaran::orderBy('tahun_mulai', 'desc')->get();
        return view('layouts.admin.contents.semester.edit', compact('semester', 'tahunAjaran'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Semester $semester)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'id_tahun_ajaran' => 'required|exists:tahun_ajaran,id',
            Rule::unique('semester')->where(function ($query) use ($request) {
                return $query->where('id_tahun_ajaran', $request->id_tahun_ajaran);
            })->ignore($semester->id),
        ]);

        $semester->update($request->all());

        return redirect()->route('admin.semester.index')
            ->with('success', 'Semester berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Semester $semester)
    {
        // Tambahkan proteksi
        if ($semester->dataSiswaKelas()->count() > 0) {
            return redirect()->route('admin.semester.index')
                ->with('error', 'Gagal! Semester ini masih memiliki data siswa terkait.');
        }

        $semester->delete();

        return redirect()->route('admin.semester.index')
            ->with('success', 'Semester berhasil dihapus.');
    }
}
