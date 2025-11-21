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
            'kode' => 'required|string|unique:siswa,kode|max:100',
            'nama' => 'required|string|max:255',
        ]);

        // 2. Simpan data
        Siswa::create([
            'kode' => $request->kode,
            'nama' => $request->nama,
        ]);

        // 3. Redirect kembali ke halaman index dengan pesan sukses
        return redirect()->route('admin.siswa.index')
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
            'kode' => [
                'required',
                'string',
                Rule::unique('siswa')->ignore($siswa->id), // Ignore ID ini
                'max:100'
            ],
            'nama' => 'required|string|max:255',
        ]);

        // 2. Update data
        $siswa->update([
            'kode' => $request->kode,
            'nama' => $request->nama,
        ]);

        // 3. Redirect kembali ke halaman index
        return redirect()->route('admin.siswa.index')
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
        return redirect()->route('admin.siswa.index')
            ->with('success', 'Data siswa berhasil dihapus.');
    }
}
