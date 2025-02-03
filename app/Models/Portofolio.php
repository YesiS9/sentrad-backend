<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Portofolio extends Model
{
    use HasUuids, SoftDeletes, HasFactory;


    protected $fillable = [
        'kelompok_id',
        'seniman_id',
        'kategori_id',
        'judul_portofolio',
        'tgl_dibuat',
        'deskripsi_portofolio',
        'jumlah_karya',
    ];


    public function kelompok()
    {
        return $this->belongsTo(RegistrasiKelompok::class, 'kelompok_id', 'id');
    }

    public function seniman()
    {
        return $this->belongsTo(Seniman::class, 'seniman_id', 'id');
    }

    public function karyas()
    {
        return $this->hasMany(Karya::class);
    }
}
