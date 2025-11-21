<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Akademik extends Model
{
    use HasFactory;
    protected $table = 'akademik';
    protected $fillable = ['nama', 'kode'];

    public function nilaiMapel()
    {
        return $this->hasMany(NilaiMapelSiswa::class, 'id_akademik');
    }
}
