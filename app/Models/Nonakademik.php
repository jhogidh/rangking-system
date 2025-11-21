<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nonakademik extends Model
{
    use HasFactory;
    protected $table = 'nonakademik';
    protected $fillable = ['nama', 'kode'];

    public function nilaiMapel()
    {
        return $this->hasMany(NilaiMapelSiswa::class, 'id_non_akademik');
    }
}
