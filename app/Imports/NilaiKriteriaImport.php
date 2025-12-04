<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas; // Agar rumus Excel terbaca nilainya
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

// Model
use App\Models\Siswa;
use App\Models\Kriteria;
use App\Models\DataSiswaKelas;
use App\Models\DataNilaiKriteria;
use Exception;

class NilaiKriteriaImport implements ToCollection, WithHeadingRow, WithCalculatedFormulas
{
    protected $id_semester;
    protected $id_kelas;
    protected $kriteriaMap;

    public function __construct(int $id_semester, int $id_kelas)
    {
        $this->id_semester = $id_semester;
        $this->id_kelas    = $id_kelas;

        // Ambil ID dan Nama Kriteria untuk dicocokkan nanti
        $this->kriteriaMap = Kriteria::pluck('id', 'nama');

        if ($this->kriteriaMap->isEmpty()) {
            throw new Exception("Import Gagal: Data Kriteria di database kosong.");
        }
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // 1. Validasi dasar: Lewati jika tidak ada nama
            if (empty($row['nama'])) {
                continue;
            }

            // 2. Cari atau Buat Siswa
            $siswa = Siswa::updateOrCreate(
                ['nama' => $row['nama']], // Argumen 1: Cari berdasarkan Nama
                [                         // Argumen 2: Data baru jika nama tidak ditemukan
                    'nisn'        => $row['nisn'],
                    'tahun_masuk' => $row['tahun_masuk'] // Perhatikan underscore '_' karena header aslinya "Tahun Masuk"
                ]
            );

            // 3. Masukkan Siswa ke Kelas & Semester ini
            $dataSiswa = DataSiswaKelas::firstOrCreate([
                'id_siswa'    => $siswa->id,
                'id_semester' => $this->id_semester,
                'id_kelas'    => $this->id_kelas
            ]);

            // 4. Loop setiap Kriteria yang ada di Database untuk mencari nilainya di Excel
            foreach ($this->kriteriaMap as $namaKriteriaDb => $idKriteria) {

                // === LOGIKA MAPPING (PENTING) ===
                // Kita harus menebak nama header di Excel berdasarkan nama di Database.
                // Library Excel mengubah header jadi lowercase (slug).

                $headerExcel = Str::slug($namaKriteriaDb, '_'); // Default: nama_kriteria jadi nama_kriteria (kecil)

                // Override Manual (Sesuaikan dengan CSV 'Data Olah.csv' kamu)
                $namaLower = strtolower($namaKriteriaDb);

                if (str_contains($namaLower, 'sikap')) {
                    $headerExcel = 'sikap';
                } elseif (str_contains($namaLower, 'nilai') || str_contains($namaLower, 'akademik')) {
                    $headerExcel = 'nilai'; // Karena di CSV namanya cuma 'Nilai'
                } elseif (str_contains($namaLower, 'ekstra')) {
                    $headerExcel = 'ekstrakurikuler'; // Menangani typo 'kuli' atau 'kuri'
                } elseif (str_contains($namaLower, 'absen')) {
                    $headerExcel = 'absensi';
                } elseif (str_contains($namaLower, 'prestasi')) {
                    $headerExcel = 'prestasi';
                }

                // 5. Ambil Nilai dari Row Excel
                // Cek apakah kolom tersebut ada di Excel?
                if (isset($row[$headerExcel])) {
                    $nilai = $row[$headerExcel];

                    // Pastikan nilai valid (bukan null/kosong string)
                    if ($nilai !== null && $nilai !== '') {

                        // 6. Simpan ke Database
                        DataNilaiKriteria::updateOrCreate(
                            [
                                'id_data_siswa_kelas' => $dataSiswa->id,
                                'id_kriteria'         => $idKriteria,
                            ],
                            [
                                'nilai' => floatval($nilai) // Pastikan jadi angka desimal
                            ]
                        );
                    }
                } else {
                    // Debugging: Jika kolom tidak ketemu, catat di Log Laravel
                    // Cek di storage/logs/laravel.log
                    Log::warning("Kolom Excel '{$headerExcel}' tidak ditemukan untuk kriteria DB '{$namaKriteriaDb}'");
                }
            }
        }
    }
}
