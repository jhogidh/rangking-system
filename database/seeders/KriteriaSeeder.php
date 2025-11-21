<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Kriteria; // <-- Pastikan Model Kriteria di-import

class KriteriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Data dari gambar
        $dataKriteria = [
            [
                'nama_kriteria' => 'Absensi',
                'bobot' => 0.09
            ],
            [
                'nama_kriteria' => 'Sikap/Akhlak',
                'bobot' => 0.46
            ],
            [
                'nama_kriteria' => 'Nilai',
                'bobot' => 0.26
            ],
            [
                'nama_kriteria' => 'Ekstrakurikuler',
                'bobot' => 0.04
            ],
            [
                'nama_kriteria' => 'Prestasi',
                'bobot' => 0.16
            ],
        ];

        // 2. Masukkan data ke database
        // Kita pakai 'updateOrCreate' agar seeder ini aman
        // dijalankan berkali-kali tanpa membuat data duplikat.
        foreach ($dataKriteria as $data) {
            Kriteria::updateOrCreate(
                [
                    'nama_kriteria' => $data['nama_kriteria'] // Cari berdasarkan nama
                ],
                [
                    'bobot' => $data['bobot'] // Update atau Create dengan bobot ini
                ]
            );
        }
    }
}
