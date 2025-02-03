<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tingkatan extends Model
{
    use HasUuids, SoftDeletes, HasFactory;


    protected $fillable = [
        'nama_tingkatan',
        'deskripsi_tingkatan',
        'nilai_min',
        'nilai_max',
    ];
}
