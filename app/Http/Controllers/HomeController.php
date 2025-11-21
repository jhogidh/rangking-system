<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function home()
    {

        $siswa = [
            ['nama' => 'Silvi Aulia', 'nilai' => 95],
            ['nama' => 'Budi Santoso', 'nilai' => 88],
            ['nama' => 'Ani Wijaya', 'nilai' => 92],
            ['nama' => 'Dedi Kurniawan', 'nilai' => 85],
        ];

        return view('home', compact('siswa'));
    }
}
