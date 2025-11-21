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
    /**
     * Menampilkan halaman penempatan kelas (Alur B - Wajib 1)
     */
    public function index(Request $request)
    {
        // 1. Ambil data untuk dropdown filter
        $semesters = Semester::with('tahunAjaran')->orderBy('id', 'desc')->get();
        $kelasList = Kelas::orderBy('nama', 'asc')->get();

        // 2. Siapkan variabel kosong
        $siswaDiLuarKelas = null;
        $siswaDiDalamKelas = null;
        $kelas = null;
        $selectedSemester = null; // Untuk menyimpan info semester
        $selectedKelas = null; // Untuk menyimpan info kelas

        // 3. Cek apakah user sudah memilih filter
        if ($request->filled('id_semester') && $request->filled('id_kelas')) {
            // Simpan info yang dipilih untuk dikirim kembali ke view
            $selectedSemester = Semester::find($request->id_semester);
            $selectedKelas = Kelas::find($request->id_kelas);

            // 4. Ambil ID siswa yang SUDAH ADA di kelas ini PADA semester ini
            $siswaSudahDitempatkanIds = DataSiswaKelas::where('id_semester', $request->id_semester)
                ->where('id_kelas', $request->id_kelas)
                ->pluck('id_siswa'); // Ganti ke id_siswa

            // 5. Ambil data lengkap siswa yang SUDAH ADA (berdasarkan relasi DataSiswaKelas)
            $siswaDiDalamKelas = DataSiswaKelas::where('id_semester', $request->id_semester)
                ->where('id_kelas', $request->id_kelas)
                ->with('siswa') // Ambil data siswanya
                ->get();

            // 6. Ambil ID siswa yang sudah ada di kelas LAIN PADA semester ini
            $siswaDiKelasLainIds = DataSiswaKelas::where('id_semester', $request->id_semester)
                ->where('id_kelas', '!=', $request->id_kelas)
                ->pluck('id_siswa'); // Ganti ke id_siswa

            // 7. Ambil siswa yang "tersedia" (belum di kelas manapun PADA semester ini)
            //    Kita gabungkan kedua daftar ID yang tidak boleh tampil
            $excludeIds = $siswaSudahDitempatkanIds->merge($siswaDiKelasLainIds);

            $siswaDiLuarKelas = Siswa::whereNotIn('id', $excludeIds)
                ->orderBy('nama', 'asc')
                ->get();
        }

        // 8. Kirim semua data ke view
        return view('layouts.admin.contents.penempatan.index', compact(
            'semesters',
            'kelasList',
            'siswaDiLuarKelas',
            'siswaDiDalamKelas',
            'selectedSemester', // Kirim info semester yang dipilih
            'selectedKelas'     // Kirim info kelas yang dipilih
        ));
    }

    /**
     * Menyimpan (menambahkan) siswa ke dalam kelas
     */
    public function store(Request $request)
    {
        // 1. Validasi
        $validated = $request->validate([
            'id_siswa' => [
                'required',
                'exists:siswa,id',
                // Validasi unik: pastikan siswa ini belum ada di semester ini
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

        // 2. Buat data
        DataSiswaKelas::create($validated);

        // 3. Redirect kembali
        return redirect()->back()
            ->with('success', 'Siswa berhasil ditempatkan di kelas.');
    }

    /**
     * Menghapus (mengeluarkan) siswa dari kelas
     * Parameter $id adalah ID dari tabel 'data_siswa_kelas'
     */
    public function destroy($id)
    {
        // 1. Cari data
        $dataSiswaKelas = DataSiswaKelas::find($id);

        // 2. Hapus
        if ($dataSiswaKelas) {
            $dataSiswaKelas->delete();
            return redirect()->back()
                ->with('success', 'Siswa berhasil dikeluarkan dari kelas.');
        }

        return redirect()->back()
            ->with('error', 'Data penempatan tidak ditemukan.');
    }
}
