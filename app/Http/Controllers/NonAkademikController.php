<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\NonAkademik;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;


class NonAkademikController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $nonakademik = Nonakademik::latest()->paginate(10);

        return view('layouts.admin.contents.nonakademik.index', compact('nonakademik'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('layouts.admin.contents.nonakademik.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|unique:nonakademik,nama|max:255',
            'kode' => 'required|string|unique:nonakademik,kode|max:50',
        ]);

        Nonakademik::create($request->all());

        return redirect()->route('admin.nonakademik.index')
            ->with('success', 'Data non-akademik berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Nonakademik $nonakademik) // Route Model Binding
    {
        return view('layouts.admin.contents.nonakademik.edit', compact('nonakademik'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Nonakademik $nonakademik)
    {
        $request->validate([
            'nama' => [
                'required',
                'string',
                Rule::unique('nonakademik')->ignore($nonakademik->id),
                'max:255'
            ],
            'kode' => [
                'required',
                'string',
                Rule::unique('nonakademik')->ignore($nonakademik->id),
                'max:50'
            ],
        ]);

        $nonakademik->update($request->all());

        return redirect()->route('admin.nonakademik.index')
            ->with('success', 'Data non-akademik berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Nonakademik $nonakademik)
    {
        // Proteksi: Jangan hapus jika sudah dipakai di nilai_mapel_siswa
        if ($nonakademik->nilaiMapel()->count() > 0) {
            return redirect()->route('admin.nonakademik.index')
                ->with('error', 'Gagal! Data ini masih digunakan di tabel nilai siswa.');
        }

        $nonakademik->delete();

        return redirect()->route('admin.nonakademik.index')
            ->with('success', 'Data non-akademik berhasil dihapus.');
    }
}
