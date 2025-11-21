<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataSiswaKelas extends Model
{
    use HasFactory;
    protected $table = 'data_siswa_kelas';
    protected $fillable = ['id_siswa', 'id_kelas', 'id_semester'];

    // --- Relasi "belongsTo" (Ke Atas) ---
    public function siswa()
    {
        return $this->belongsTo(Siswa::class, 'id_siswa');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas');
    }

    public function semester()
    {
        return $this->belongsTo(Semester::class, 'id_semester');
    }

    // --- RELASI BARU (PENGGANTI SEMUA YANG LAMA) ---
    /**
     * Relasi ke 5 nilai kriteria (dari hasil import)
     * Ini adalah satu-satunya relasi nilai yang kita butuhkan.
     */
    public function nilaiKriteria()
    {
        return $this->hasMany(DataNilaiKriteria::class, 'id_data_siswa_kelas');
    }


    // --- HAPUS / BERI KOMENTAR RELASI LAMA DI BAWAH INI ---

    // Relasi ini sudah tidak dipakai (diganti data_nilai_kriteria)
    // public function nilaiMapel()
    // {
    //     return $this->hasMany(NilaiMapelSiswa::class, 'id_siswa_kelas');
    // }

    // Relasi ini sudah tidak dipakai (diganti data_nilai_kriteria)
    // public function totalNilai()
    // {
    //     return $this->hasOne(TotalNilaiAkademik::class, 'id_siswa_kelas');
    // }

    /**
     * Relasi ke hasil ranking akhir (ini tetap dipakai)
     */
    public function ranking()
    {
        return $this->hasOne(Ranking::class, 'id_data_siswa_kelas');
    }

    // Relasi ini sudah tidak dipakai
    // public function skorKriteria()
    // {
    //     return $this->hasMany(SkorPerKriteria::class, 'id_siswa_kelas');
    // }

    // Relasi ini sudah tidak dipakai
    // public function rankingKriteria()
    // {
    //     return $this->hasMany(RankingPerKriteria::class, 'id_siswa_kelas');
    // }
}
