<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Rubrik extends Model
{
    use HasUuids, SoftDeletes, HasFactory;


    protected $fillable = [
        'penilai_id',
        'nama_rubrik',
        'deskripsi_rubrik',
        'bobot'
    ];

    public function penilai()
    {
        return $this->belongsTo(Penilai::class);
    }

    public function rubrikPenilaians()
    {
        return $this->hasMany(RubrikPenilaian::class, 'rubrik_id', 'id');
    }

}
