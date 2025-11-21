<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TahunAjaran extends Model
{
    use HasFactory;
    protected $table = 'tahun_ajaran';
    protected $fillable = ['nama', 'tahun_mulai', 'tahun_selesai', 'status']; // 'nama' ditambahkan dari file migrasi SQL sebelumnya

    public function semester()
    {
        return $this->hasMany(Semester::class, 'id_tahun_ajaran');
    }
}
