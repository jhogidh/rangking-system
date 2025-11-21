<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnalisisPerbandingan extends Model
{
    use HasFactory;

    protected $table = 'analisis_perbandingan';

    /**
     * Atribut yang dapat diisi secara massal.
     * Ini adalah perbaikan untuk error "Tidak ada data."
     */
    protected $fillable = [
        'id_semester',
        'id_kelas',
        'metode',
        'waktu_tahap_1',
        'waktu_tahap_2',
        'waktu_tahap_3',
        'waktu_tahap_4',
        'waktu_tahap_5', // Untuk Borda
        'waktu_total',
        'spearman_rho',
    ];

    /**
     * Relasi ke semester
     */
    public function semester()
    {
        return $this->belongsTo(Semester::class, 'id_semester');
    }

    /**
     * Relasi ke kelas (opsional)
     */
    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'id_kelas');
    }
}
