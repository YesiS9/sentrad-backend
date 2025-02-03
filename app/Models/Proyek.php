<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Proyek extends Model
{
    use HasUuids, SoftDeletes, HasFactory;

    protected $fillable = [
        'seniman_id',
        'kategori_id',
        'judul_proyek',
        'deskripsi_proyek',
        'waktu_mulai',
        'waktu_selesai',
        'lokasi_proyek',
        'tautan_proyek',
        'status_proyek',
        'jumlah_like'
    ];


    public function seniman()
    {
        return $this->belongsTo(Seniman::class, 'seniman_id');
    }
}
