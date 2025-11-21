<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TotalNilaiAkademik extends Model
{
    use HasFactory;
    protected $table = 'total_nilai_akademik';
    protected $fillable = ['id_siswa_kelas', 'total_nilai'];

    public function dataSiswaKelas()
    {
        return $this->belongsTo(DataSiswaKelas::class, 'id_siswa_kelas');
    }
}
