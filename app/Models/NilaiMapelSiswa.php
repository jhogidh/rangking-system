<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NilaiMapelSiswa extends Model
{
    use HasFactory;
    protected $table = 'nilai_mapel_siswa';
    protected $fillable = ['id_siswa_kelas', 'id_akademik', 'id_non_akademik', 'nilai'];

    public function dataSiswaKelas()
    {
        return $this->belongsTo(DataSiswaKelas::class, 'id_siswa_kelas');
    }

    public function akademik()
    {
        return $this->belongsTo(Akademik::class, 'id_akademik');
    }

    public function nonakademik()
    {
        return $this->belongsTo(Nonakademik::class, 'id_non_akademik');
    }
}
