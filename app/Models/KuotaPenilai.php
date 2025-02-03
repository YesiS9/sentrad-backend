<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\SoftDeletes;

class KuotaPenilai extends Model
{
    use HasUuids, SoftDeletes, HasFactory;

    protected $fillable = [
        'penilai_id',
        'periode_bulan',
        'kuota_terpakai'
    ];

    public function penilai()
    {
        return $this->belongsTo(Penilai::class, 'penilai_id');
    }

    public function penilaian()
    {
        return $this->hasMany(Penilaian::class, 'kuota_id');
    }
}
