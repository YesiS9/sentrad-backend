<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RiwayatRegisIndividu extends Model
{
    use HasUuids, SoftDeletes, HasFactory;
    protected $fillable = [
        'seniman_id',
        'kategori_id',
        'nama',
        'tgl_lahir',
        'tgl_mulai',
        'alamat',
        'noTelp',
        'email',
        'status_individu',
    ];

}
