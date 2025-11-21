<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataNilaiKriteria extends Model
{
    use HasFactory;

    protected $table = 'data_nilai_kriteria';

    protected $fillable = [
        'id_data_siswa_kelas',
        'id_kriteria',
        'nilai',
    ];

    /**
     * Relasi ke data siswa di kelas
     */
    public function dataSiswaKelas()
    {
        return $this->belongsTo(DataSiswaKelas::class, 'id_data_siswa_kelas');
    }

    /**
     * Relasi ke kriteria
     */
    public function kriteria()
    {
        return $this->belongsTo(Kriteria::class, 'id_kriteria');
    }
}
