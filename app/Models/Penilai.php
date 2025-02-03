<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class Penilai extends Model
{
    use HasUuids, SoftDeletes, HasFactory;

    protected $fillable = [
        'user_id',
        'kategori_id',
        'kuota_id',
        'nama_penilai',
        'alamat_penilai',
        'noTelp_penilai',
        'bidang_ahli',
        'lembaga',
        'tgl_lahir',
        'status_penilai',
        'kuota'
    ];


    public function rubriks()
    {
        return $this->hasMany(Rubrik::class, 'penilai_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function kategoriSeni()
    {
        return $this->belongsTo(KategoriSeni::class, 'kategori_id');
    }

    public function kuotaPenilai() {
        return $this->hasOne(KuotaPenilai::class);
    }

}
