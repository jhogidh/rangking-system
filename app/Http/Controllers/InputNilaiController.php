<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Imports\NilaiKriteriaImport;

use Illuminate\Http\Request;


use App\Models\Semester;
use App\Models\Kelas;
use MaatWebsite\Excel\Validators\ValidationException; // <-- Import untuk catch error
use Exception; // <-- Import untuk catch error
use Maatwebsite\Excel\Facades\Excel;

class InputNilaiController extends Controller
{
    /**
     * Menampilkan halaman form import (Alur B - Wajib 2)
     */
    public function index()
    {
        // REVISI: Hapus with('tahunAjaran'), karena kolom tahun sudah ada di tabel semester
        $semesters = Semester::orderBy('id', 'desc')->get();

        $kelasList = Kelas::orderBy('nama', 'asc')->get();

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
        // 1. Validasi input
        $request->validate([
            'id_semester' => 'required|exists:semester,id',
            'id_kelas' => 'required|exists:kelas,id',
            'file_import' => 'required|mimes:xlsx,xls,csv',
        ]);

        try {
            $id_semester = $request->id_semester;
            $id_kelas = $request->id_kelas;

            // 3. Jalankan "Mesin Import"
            Excel::import(new NilaiKriteriaImport($id_semester, $id_kelas), $request->file('file_import'));

            return redirect()->back()
                ->with('success', 'Data nilai kriteria berhasil di-import!');
        } catch (ValidationException $e) {
            $failures = $e->failures();
            return redirect()->back()->with('error', 'Gagal import! Error: ' . $failures[0]->errors()[0]);
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan. Pesan: ' . $e->getMessage());
        }
    }
}
