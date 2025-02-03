<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Seni extends Model
{
    use HasUuids, SoftDeletes, HasFactory;


    protected $fillable = [
        'kategori_id',
        'nama_seni',
        'deskripsi_seni',
        'status_seni',
    ];

    public function kategori()
    {
        return $this->belongsTo(KategoriSeni::class);
    }
}
