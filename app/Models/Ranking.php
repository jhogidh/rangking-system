<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ranking extends Model
{
    use HasFactory;
    protected $table = 'ranking';

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_data_siswa_kelas', // <-- TAMBAHKAN BARIS INI
        'metode',
        'hasil_alternatif',
        'ranking',
    ];

    /**
     * Relasi ke data siswa di kelas
     */
    public function dataSiswaKelas()
    {
        return $this->belongsTo(\App\Models\DataSiswaKelas::class, 'id_data_siswa_kelas');
    }
}
