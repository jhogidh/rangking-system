<?php

namespace App\Http\Controllers; // <-- Ganti ke namespace Admin

use App\Http\Controllers\Controller;
use App\Models\Siswa; // <-- Import model Siswa
use Illuminate\Http\Request;
use Illuminate\Validation\Rule; // <-- Import Rule untuk validasi unique

class SiswaController extends Controller
{
    /**
     * Menampilkan halaman utama (tabel data siswa)
     */
    public function index()
    {
        // Pakai paginate(10)
        $siswa = Siswa::latest()->paginate(10);

        // Kirim data ke view
        return view('layouts.admin.contents.siswa.index', compact('siswa'));
    }

    /**
     * Menampilkan halaman form tambah data
     */
    public function create()
    {
        return view('layouts.admin.contents.siswa.create');
    }

    /**
     * Menyimpan data siswa baru ke database
     */
    public function store(Request $request)
    {
        // 1. Validasi data (seperti KelasController)
        $request->validate([
            'nisn' => 'required|string|unique:siswa,nisn|max:100',
            'nama' => 'required|string|max:255',
        ]);

        // 2. Simpan data
        Siswa::create([
            'nisn' => $request->kode || null,
            'nama' => $request->nama,
            'tahun_masuk' => $request->tahun_masuk || null
        ]);

        // 3. Redirect kembali ke halaman index dengan pesan sukses
        return redirect()->route('proses.siswa.index')
            ->with('success', 'Data siswa berhasil ditambahkan.');
    }

    /**
     * Menampilkan halaman form edit data
     */
    public function edit(Siswa $siswa) // <-- Pakai Route-Model Binding
    {
        // $siswa otomatis ditemukan berdasarkan ID di URL
        return view('layouts.admin.contents.siswa.edit', compact('siswa'));
    }

    /**
     * Mengupdate data siswa di database
     */
    public function update(Request $request, Siswa $siswa) // <-- Pakai Route-Model Binding
    {
        // 1. Validasi data (seperti KelasController)
        $request->validate([
            'nama' => 'required|string|max:255',
            'nisn' => [
                'required',
                'string',
                Rule::unique('siswa')->ignore($siswa->id), // Ignore ID ini
                'max:100'
            ],
            'tahun_masuk' => [
                'required',
                'string',
                Rule::unique('siswa')->ignore($siswa->id), // Ignore ID ini
                'max:100'
            ]
        ]);

        // 2. Update data
        $siswa->update([
            'nisn' => $request->nisn,
            'tahun_masuk' => $request->tahun_masuk,
            'nama' => $request->nama,
        ]);

        // 3. Redirect kembali ke halaman index
        return redirect()->route('proses.siswa.index')
            ->with('success', 'Data siswa berhasil diperbarui.');
    }

    /**
     * Menghapus data siswa dari database
     */
    public function destroy(Siswa $siswa) // <-- Pakai Route-Model Binding
    {
        // Hapus data
        $siswa->delete();

        // Redirect kembali
        return redirect()->route('proses.siswa.index')
            ->with('success', 'Data siswa berhasil dihapus.');
    }
}
