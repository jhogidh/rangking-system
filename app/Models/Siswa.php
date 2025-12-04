<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Siswa extends Model
{
    use HasFactory;
    protected $table = 'siswa';
    protected $fillable = ['nisn', 'nama', 'tahun_masuk'];

    public function dataKelas()
    {
        // Histori siswa di semua kelas/semester
        return $this->hasMany(DataSiswaKelas::class, 'id_siswa');
    }
}
