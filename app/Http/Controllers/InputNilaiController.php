<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Imports\NilaiKriteriaImport;
use Illuminate\Http\Request;


use App\Models\Semester;
use App\Models\Kelas;
use MaatWebsite\Excel\Validators\ValidationException;
use Exception;
use Maatwebsite\Excel\Facades\Excel;

class InputNilaiController extends Controller
{
    /**
     * Menampilkan halaman form import (Alur B - Wajib 2)
     */
    public function index()
    {
        // PERBAIKAN: Hapus with('tahunAjaran') karena tabelnya sudah tidak ada
        // Data tahun sekarang ada langsung di tabel semester
        $semesters = Semester::orderBy('id', 'desc')->get();
        $kelasList = Kelas::orderBy('nama', 'asc')->get();

        // 2. Kembalikan view
        return view('layouts.admin.contents.input-nilai.index', compact(
            'semesters',
            'kelasList'
        ));
    }

    /**
     * Memproses file Excel yang di-upload
     */
    public function store(Request $request)
    {
        // 1. Validasi input dengan PESAN KUSTOM
        $request->validate([
            'id_semester' => 'required|exists:semester,id',
            'id_kelas' => 'required|exists:kelas,id',
            // Paksa hanya CSV atau TXT (beberapa CSV terdeteksi sebagai text/plain)
            'file_import' => 'required|mimes:csv,txt', 
        ], [
            'file_import.required' => 'Anda belum memilih file.',
            'file_import.mimes' => 'Format file SALAH! Harap upload file CSV (.csv).',
        ]);

        try {
            $id_semester = (int) $request->id_semester;
            $id_kelas = (int) $request->id_kelas;

            // 3. Jalankan "Mesin Import" dengan mengirimkan parameter ke Constructor
            // PERBAIKAN DI SINI: Kirim $id_semester dan $id_kelas
            Excel::import(new NilaiKriteriaImport($id_semester, $id_kelas), $request->file('file_import'));

            // 4. Redirect kembali dengan sukses
            return redirect()->back()
                             ->with('success', 'Data nilai kriteria berhasil di-import!');

        } catch (ValidationException $e) {
             $failures = $e->failures();
             // Pesan error jika validasi baris Excel gagal
             return redirect()->back()->with('error', 'Gagal import! Error: ' . $failures[0]->errors()[0]);
        } catch (Exception $e) {
            // Tangkap error umum (seperti format header salah)
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
