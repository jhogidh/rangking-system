<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Kriteria; // <-- Ganti ke model Kriteria
use Illuminate\Http\Request;
use Illuminate\Validation\Rule; // <-- Import Rule untuk validasi unique

class KriteriaController extends Controller
{
    /**
     * Menampilkan halaman utama (tabel data kriteria)
     */
    public function index()
    {
        $kriteria = Kriteria::latest()->paginate(10);

        // Kirim data ke view
        return view('layouts.admin.contents.kriteria.index', compact('kriteria'));
    }

    /**
     * Menampilkan halaman form tambah data
     */
    public function create()
    {
        return view('layouts.admin.contents.kriteria.create');
    }

    /**
     * Menyimpan data kriteria baru ke database
     */
    public function store(Request $request)
    {
        // 1. Validasi data
        $request->validate([
            'nama_kriteria' => 'required|string|unique:kriteria,nama_kriteria|max:255',
            'bobot' => 'required|numeric|min:0|max:1', // Bobot biasanya antara 0 dan 1
        ]);

        // 2. Simpan data
        Kriteria::create([
            'nama_kriteria' => $request->nama_kriteria,
            'bobot' => $request->bobot,
        ]);

        // 3. Redirect kembali ke halaman index
        return redirect()->route('admin.kriteria.index')
            ->with('success', 'Data kriteria berhasil ditambahkan.');
    }

    /**
     * Menampilkan halaman form edit data
     */
    public function edit(Kriteria $kriteria)
    {
        // kirim data kriteria yang ditemukan ke view
        return view('layouts.admin.contents.kriteria.edit', compact('kriteria'));
    }

    /**
     * Mengupdate data kriteria di database
     */
    public function update(Request $request, Kriteria $kriteria)
    {
        // 1. Validasi data
        $request->validate([
            'nama_kriteria' => [
                'required',
                'string',
                Rule::unique('kriteria')->ignore($kriteria->id), // Ignore ID ini
                'max:255'
            ],
            'bobot' => 'required|numeric|min:0|max:1',
        ]);

        // 2. Update data
        $kriteria->update([
            'nama_kriteria' => $request->nama_kriteria,
            'bobot' => $request->bobot,
        ]);

        // 3. Redirect kembali ke halaman index
        return redirect()->route('admin.kriteria.index')
            ->with('success', 'Data kriteria berhasil diperbarui.');
    }

    /**
     * Menghapus data kriteria dari database
     */
    public function destroy(Kriteria $kriteria)
    {
        // Hapus data
        $kriteria->delete();

        // Redirect kembali
        return redirect()->route('admin.kriteria.index')
            ->with('success', 'Data kriteria berhasil dihapus.');
    }
}
