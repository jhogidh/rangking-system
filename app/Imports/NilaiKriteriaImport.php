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
        
        try {
            // Coba ambil data kriteria
            $this->kriteriaMap = Kriteria::pluck('id', 'nama');
        } catch (QueryException $e) {
            // Jika terjadi error database (misal tabel/kolom tidak ada), tangkap di sini
            // Error 1054 adalah "Unknown column"
            if ($e->errorInfo[1] == 1054) {
                throw new Exception("Gagal Membaca Database: Kolom 'nama_kriteria' tidak ditemukan di tabel kriteria. Mohon hubungi admin untuk memeriksa struktur database (migrasi).");
            }
            // Error 1146 adalah "Table doesn't exist"
            if ($e->errorInfo[1] == 1146) {
                 throw new Exception("Gagal Membaca Database: Tabel 'kriteria' belum dibuat. Mohon jalankan migrasi database terlebih dahulu.");
            }

            // Error database lainnya
            throw new Exception("Terjadi kesalahan database saat membaca Kriteria: " . $e->getMessage());
        }

        if ($this->kriteriaMap->isEmpty()) {
            throw new Exception("Gagal: Data Kriteria di database masih kosong. Mohon Admin mengisi Master Data Kriteria terlebih dahulu.");
        }
    }

    public function collection(Collection $rows)
    {
        // Cek apakah file kosong
        $firstRow = $rows->first();
        if (!$firstRow) {
             throw new Exception("Gagal: File yang Anda upload kosong atau tidak terbaca. Pastikan file berisi data.");
        }

        // Cek Header Wajib 'Nama'
        // Kita cek keys dari baris pertama
        $headerKeys = array_keys($firstRow->toArray());
        
        // Cari apakah ada key yang mengandung 'nama' (case-insensitive)
        $hasNama = false;
        foreach ($headerKeys as $key) {
            if (strtolower($key) === 'nama') {
                $hasNama = true;
                break;
            }
        }

        if (!$hasNama) {
             throw new Exception("Format Dokumen Tidak Sesuai: Kolom 'Nama' tidak ditemukan di baris pertama. Pastikan Anda menggunakan file template yang benar.");
        }

        // Cek Header Wajib 'Nilai' (salah satu kriteria)
        $hasNilai = false;
        foreach ($headerKeys as $key) {
            if (strtolower($key) === 'nilai') {
                $hasNilai = true;
                break;
            }
        }
        
        if (!$hasNilai) {
             // Opsional: Cek kriteria lain jika 'Nilai' tidak ada, tapi biasanya 'Nilai' wajib ada
             // throw new Exception("Format Dokumen Tidak Sesuai: Kolom 'Nilai' tidak ditemukan.");
        }

        $countSuccess = 0;

        foreach ($rows as $row) 
        {
            // Ambil nama (case-insensitive key lookup)
            $namaSiswa = null;
            foreach ($row as $key => $val) {
                if (strtolower($key) === 'nama') {
                    $namaSiswa = $val;
                    break;
                }
            }

            if (empty($namaSiswa)) continue; 

            // 1. Buat Siswa jika belum ada
            $siswa = Siswa::firstOrCreate(
                ['nama' => $namaSiswa],
                ['kode' => 'S-' . Str::uuid()->toString()]
            );

            // 2. Masukkan ke Kelas (Penempatan)
            $dataSiswa = DataSiswaKelas::firstOrCreate(
                [
                    'id_siswa' => $siswa->id,
                    'id_semester' => $this->id_semester,
                    'id_kelas' => $this->id_kelas
                ]
            );
            
            // 3. Simpan Nilai
            foreach ($this->kriteriaMap as $namaKriteria => $idKriteria) 
            {
                $excelHeader = strtolower($namaKriteria);
                if ($namaKriteria == 'Sikap/Akhlak') $excelHeader = 'sikap';
                if ($namaKriteria == 'Ekstrakurikuler') $excelHeader = 'ekstrakurikuler';

                // Cari value di row dengan case-insensitive key
                $val = null;
                foreach ($row as $key => $value) {
                    if (strtolower($key) === $excelHeader) {
                        $val = $value;
                        break;
                    }
                }

                if ($val !== null && $val !== '') {
                    // Bersihkan nilai jika ada karakter aneh
                    if (is_string($val) && str_starts_with($val, '=')) {
                        $val = 0; 
                    }

                    DataNilaiKriteria::updateOrCreate(
                        [
                            'id_data_siswa_kelas' => $dataSiswa->id,
                            'id_kriteria'         => $idKriteria,
                        ],
                        ['nilai' => $val]
                    );
                }
            }
            $countSuccess++;
        }
        
        if ($countSuccess === 0) {
            throw new Exception("Gagal: Tidak ada data siswa yang berhasil dibaca. Mohon periksa kembali isi file Anda.");
        }
    }
}