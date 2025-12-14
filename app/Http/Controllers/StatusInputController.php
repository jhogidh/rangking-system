<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Semester;
use App\Models\Kelas;
use App\Models\DataSiswaKelas;
use Illuminate\Support\Facades\DB;

class StatusInputController extends Controller
{
    public function index()
    {
        // 1. Ambil semua Semester (urutkan dari yang terlama ke terbaru agar enak dilihat)
        // Kita asumsikan data semester 2022-2025 sudah diinput di Master Data
        $semesters = Semester::orderBy('tahun_mulai', 'asc')
            ->orderBy('nama', 'desc') // Ganjil dulu biasanya
            ->get();

        // 2. Ambil semua Kelas (1-6)
        $kelasList = Kelas::orderBy('nama', 'asc')->get();

        // 3. Cek Status Input untuk setiap kombinasi
        // Struktur data: $statusMatrix[id_semester][id_kelas] = true/false
        $statusMatrix = [];

        foreach ($semesters as $semester) {
            foreach ($kelasList as $kelas) {
                // Cek apakah ada data siswa di kelas & semester ini yang sudah punya nilai
                // Kita cek di tabel 'data_nilai_kriteria' via relasi
                // Jika ada minimal 1 siswa yang punya nilai, kita anggap SUDAH DIINPUT (Green)

                $hasData = DB::table('data_siswa_kelas')
                    ->join('data_nilai_kriteria', 'data_siswa_kelas.id', '=', 'data_nilai_kriteria.id_data_siswa_kelas')
                    ->where('data_siswa_kelas.id_semester', $semester->id)
                    ->where('data_siswa_kelas.id_kelas', $kelas->id)
                    ->exists();

                $statusMatrix[$semester->id][$kelas->id] = $hasData;
            }
        }

        return view('layouts.admin.contents.status-input.index', compact('semesters', 'kelasList', 'statusMatrix'));
    }
}
