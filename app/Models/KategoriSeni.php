<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KategoriSeni extends Model
{
    use HasUuids, SoftDeletes, HasFactory;

    protected $fillable = [
        'user_id',
        'nama_kategori',
        'deskripsi_kategori'
    ];

    public function kategoriRubrik()
    {
        return $this->hasMany(KategoriRubrik::class, 'kategori_id', 'id');
    }


    public function rubrik()
    {
        return $this->hasManyThrough(
            Rubrik::class,
            KategoriRubrik::class,
            'kategori_id',
            'id', 
            'id',
            'rubrik_id'
        );
    }

    public function senis()
    {
        return $this->hasMany(Seni::class);
    }

    public function penilai()
    {
        return $this->hasMany(Penilai::class, 'kategori_id');
    }
}
