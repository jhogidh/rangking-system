<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    use HasFactory;
    protected $table = 'semester';
    protected $fillable = ['nama', 'id_tahun_ajaran'];

    public function tahunAjaran()
    {
        return $this->belongsTo(TahunAjaran::class, 'id_tahun_ajaran');
    }

    public function dataSiswaKelas()
    {
        return $this->hasMany(DataSiswaKelas::class, 'id_semester');
    }
}
