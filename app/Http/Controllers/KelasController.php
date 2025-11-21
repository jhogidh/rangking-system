<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Kelas; // <-- Ganti ke model Kelas
use Illuminate\Http\Request;
use Illuminate\Validation\Rule; // <-- Import Rule untuk validasi unique

class KelasController extends Controller
{
    /**
     * Menampilkan halaman utama (tabel data kelas)
     */
    public function index()
    {
        // Saya pakai paginate(10) agar konsisten dengan perbaikan 'firstItem()'
        $kelasList = Kelas::latest()->paginate(10);

        // Kirim data ke view
        return view('layouts.admin.contents.kelas.index', ['kelas' => $kelasList]);
    }

    /**
     * Menampilkan halaman form tambah data
     */
    public function create()
    {
        return view('layouts.admin.contents.kelas.create');
    }

    /**
     * Menyimpan data kelas baru ke database
     */
    public function store(Request $request)
    {
        // 1. Validasi data
        $request->validate([
            'nama' => 'required|string|unique:kelas,nama|max:100',
            'sub' => 'nullable|string|max:50', // 'sub' boleh kosong
        ]);

        // 2. Simpan data
        Kelas::create([
            'nama' => $request->nama,
            'sub' => $request->sub,
        ]);

        // 3. Redirect kembali ke halaman index
        return redirect()->route('admin.kelas.index')
            ->with('success', 'Data kelas berhasil ditambahkan.');
    }

    /**
     * Menampilkan halaman form edit data
     */
    public function edit(Kelas $kela) // <-- Ganti $siswa jadi $kela (singular)
    {
        // kirim data kelas yang ditemukan ke view
        return view('layouts.admin.contents.kelas.edit', ['kelas' => $kela]);
    }

    /**
     * Mengupdate data kelas di database
     */
    public function update(Request $request, Kelas $kela)
    {
        // 1. Validasi data
        $request->validate([
            'nama' => [
                'required',
                'string',
                Rule::unique('kelas')->ignore($kela->id), // Ignore ID ini
                'max:100'
            ],
            'sub' => 'nullable|string|max:50',
        ]);

        // 2. Update data
        $kela->update([
            'nama' => $request->nama,
            'sub' => $request->sub,
        ]);

        // 3. Redirect kembali ke halaman index
        return redirect()->route('admin.kelas.index')
            ->with('success', 'Data kelas berhasil diperbarui.');
    }

    /**
     * Menghapus data kelas dari database
     */
    public function destroy(Kelas $kela)
    {
        // Hapus data
        $kela->delete();

        // Redirect kembali
        return redirect()->route('admin.kelas.index')
            ->with('success', 'Data kelas berhasil dihapus.');
    }
}
