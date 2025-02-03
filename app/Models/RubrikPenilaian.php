<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RubrikPenilaian extends Model
{
    use HasUuids, SoftDeletes, HasFactory;


    protected $fillable = [
        'rubrik_id',
        'penilaian_karya_id',
        'skor',

    ];

    public function rubrik()
    {
        return $this->belongsTo(Rubrik::class, 'rubrik_id', 'id');
    }

    public function penilaianKarya()
    {
        return $this->belongsTo(PenilaianKarya::class);
    }
}
