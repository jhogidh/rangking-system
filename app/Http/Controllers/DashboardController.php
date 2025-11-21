<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard admin.
     */
    public function index()
    {
        // Kamu bisa menambahkan logika di sini nanti,
        // seperti mengambil jumlah siswa, dll.
        // $jumlahSiswa = \App\Models\Siswa::count();

        // Arahkan ke view dashboard
        // Ganti 'admin.dashboard' jika nama file view-mu berbeda
        return view('layouts.admin.contents.dashboard');
    }
}
