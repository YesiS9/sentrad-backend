<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class KomenProyek extends Model
{
    use HasUuids, SoftDeletes, HasFactory;

    protected $fillable = [
        'seniman_id',
        'proyek_id',
        'isi_komenProyek',
        'waktu_komenProyek',
    ];


    public function proyek()
    {
        return $this->belongsTo(Proyek::class, 'proyek_id');
    }
}
