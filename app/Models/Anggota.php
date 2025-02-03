<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Anggota extends Model
{
    use HasUuids, SoftDeletes, HasFactory;

    protected $fillable = [
        'kelompok_id',
        'nama_anggota',
        'tgl_lahir',
        'tgl_gabung',
        'alamat_anggota',
        'noTelp_anggota',
        'tingkat_skill',
        'peran_anggota',
        'status_anggota',
    ];

    public function kelompok()
    {
        return $this->belongsTo(RegistrasiKelompok::class, 'kelompok_id', 'id');
    }

    public function tingkatan()
    {
        return $this->belongsTo(Tingkatan::class, 'tingkatan_id', 'id');
    }
}
