<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use MaatWebsite\Excel\Concerns\WithCalculatedFormulas; // <-- 1. PERBAIKAN: Diaktifkan
use MaatWebsite\Excel\Concerns\WithWorksheetName; // <-- 2. PERBAIKAN: Tambahan baru
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str; // <-- 3. PERBAIKAN: Namespace Str yang benar

// Model yang kita butuhkan
use App\Models\Siswa;
use App\Models\Kriteria;
use App\Models\DataSiswaKelas;
use App\Models\DataNilaiKriteria;
use Exception;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

// Tambahkan 'WithCalculatedFormulas' dan 'WithWorksheetName'
class NilaiKriteriaImport implements ToCollection, WithHeadingRow
{
    protected $id_semester;
    protected $id_kelas;
    protected $kriteriaMap;

    /**
     * Terima id_semester dan id_kelas dari Controller
     */
    public function __construct(int $id_semester, int $id_kelas)
    {
        $this->id_semester = $id_semester;
        $this->id_kelas    = $id_kelas;

        $this->kriteriaMap = Kriteria::pluck('id', 'nama_kriteria');

        if ($this->kriteriaMap->isEmpty()) {
            throw new Exception("Import Gagal: Data Kriteria di database masih kosong. Harap isi Master Kriteria (C1-C5) terlebih dahulu.");
        }
    }

    /**
     * Proses setiap baris dari file Excel
     */
    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Lewati baris jika kolom 'nama' kosong
            if (empty($row['nama'])) {
                continue;
            }

            // 2. Cari Siswa ATAU Buat Baru (Alur A Otomatis)
            $siswa = Siswa::firstOrCreate(
                [
                    'nama' => $row['nama']
                ],
                [
                    'kode' => 'S-' . Str::uuid()->toString() // Membuat kode unik
                ]
            );

            // 3. Cari Penempatan ATAU Buat Baru (Alur B-1 Otomatis)
            $dataSiswa = DataSiswaKelas::firstOrCreate(
                [
                    'id_siswa' => $siswa->id,
                    'id_semester' => $this->id_semester,
                    'id_kelas' => $this->id_kelas
                ]
            );

            // 4. Loop semua kriteria yang kita punya di DB (Input Nilai)
            foreach ($this->kriteriaMap as $namaKriteria => $idKriteria) {
                // 5. Cocokkan nama kriteria DB dengan header Excel
                // Header di Excel: 'nama', 'nilai', 'sikap', 'absensi', 'ekstrakurikuler', 'prestasi'
                $excelHeader = strtolower($namaKriteria);
                if ($namaKriteria == 'Sikap/Akhlak') $excelHeader = 'sikap';
                if ($namaKriteria == 'Ekstrakurikuler') $excelHeader = 'ekstrakurikuler';

                // 6. Cek apakah kolomnya ada di Excel dan tidak kosong
                if (isset($row[$excelHeader]) && $row[$excelHeader] !== '') {
                    // 7. Simpan nilai ke tabel baru kita!
                    DataNilaiKriteria::updateOrCreate(
                        [
                            'id_data_siswa_kelas' => $dataSiswa->id,
                            'id_kriteria'         => $idKriteria,
                        ],
                        [
                            'nilai' => $row[$excelHeader] // Ambil nilai dari Excel
                        ]
                    );
                } else {
                    Log::warning("Import Nilai: Siswa '{$row['nama']}' tidak memiliki nilai untuk '{$excelHeader}'.");
                }
            }
        }
    }
}
