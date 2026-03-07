<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ranking extends Model
{
    use HasFactory;
    protected $table = 'ranking';

    public const CATEGORY_ALL = 'semua';
    public const CATEGORY_AKADEMIK = 'akademik';
    public const CATEGORY_NON_AKADEMIK = 'nonakademik';

    /**
     * Atribut yang dapat diisi secara massal.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id_data_siswa_kelas', // <-- TAMBAHKAN BARIS INI
        'metode',
        'kategori',
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
