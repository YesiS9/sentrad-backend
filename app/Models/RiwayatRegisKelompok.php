<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RiwayatRegisKelompok extends Model
{
    use HasUuids, SoftDeletes, HasFactory;


    protected $fillable = [
        'seniman_id',
        'kategori_id',
        'nama_kelompok',
        'tgl_terbentuk',
        'alamat_kelompok',
        'deskripsi_kelompok',
        'noTelp_kelompok',
        'email_kelompok',
        'jumlah_anggota',
        'status_kelompok',
    ];
}
