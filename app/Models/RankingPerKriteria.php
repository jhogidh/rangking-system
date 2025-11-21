<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RankingPerKriteria extends Model
{
    use HasFactory;
    protected $table = 'ranking_per_kriteria';
    protected $fillable = ['id_siswa_kelas', 'id_kriteria', 'ranking'];

    public function dataSiswaKelas()
    {
        return $this->belongsTo(DataSiswaKelas::class, 'id_siswa_kelas');
    }

    public function kriteria()
    {
        return $this->belongsTo(Kriteria::class, 'id_kriteria');
    }
}
