<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Semester extends Model
{
    use HasFactory;
    protected $table = 'semester';


    protected $fillable = [
        'nama',
        'tahun_mulai',
        'tahun_selesai',
        'status'
    ];



    public function dataSiswaKelas()
    {
        return $this->hasMany(DataSiswaKelas::class, 'id_semester');
    }
}
