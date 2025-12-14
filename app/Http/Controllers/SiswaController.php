<?php

namespace App\Http\Controllers; // <-- Ganti ke namespace Admin

use App\Http\Controllers\Controller;
use App\Models\Siswa; // <-- Import model Siswa
use Illuminate\Http\Request;
use Illuminate\Validation\Rule; // <-- Import Rule untuk validasi unique

class SiswaController extends Controller
{
    public function index()
    {
        $siswa = Siswa::latest()->paginate(10);

        return view('layouts.admin.contents.siswa.index', compact('siswa'));
    }

    public function create()
    {
        return view('layouts.admin.contents.siswa.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nisn' => 'required|string|unique:siswa,nisn|max:100',
            'nama' => 'required|string|max:255',
        ]);

        Siswa::create([
            'nisn' => $request->kode || null,
            'nama' => $request->nama,
            'tahun_masuk' => $request->tahun_masuk || null
        ]);

        return redirect()->route('proses.siswa.index')
            ->with('success', 'Data siswa berhasil ditambahkan.');
    }

    public function edit(Siswa $siswa)
    {
        return view('layouts.admin.contents.siswa.edit', compact('siswa'));
    }

    public function update(Request $request, Siswa $siswa)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'nisn' => [
                'required',
                'string',
                Rule::unique('siswa')->ignore($siswa->id),
                'max:100'
            ],
            'tahun_masuk' => [
                'required',
                'string',
                Rule::unique('siswa')->ignore($siswa->id),
                'max:100'
            ]
        ]);

        $siswa->update([
            'nisn' => $request->nisn,
            'tahun_masuk' => $request->tahun_masuk,
            'nama' => $request->nama,
        ]);

        return redirect()->route('proses.siswa.index')
            ->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function destroy(Siswa $siswa)
    {
        $siswa->delete();
        return redirect()->route('proses.siswa.index')
            ->with('success', 'Data siswa berhasil dihapus.');
    }
}
