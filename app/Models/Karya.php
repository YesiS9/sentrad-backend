<?php

namespace App\Models;

use App\Events\KaryaUpdated;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Karya extends Model
{
    use HasUuids, SoftDeletes, HasFactory;

    protected $fillable = [
        'portofolio_id',
        'judul_karya',
        'tgl_pembuatan',
        'deskripsi_karya',
        'bentuk_karya',
        'media_karya',
        'status_karya',
    ];




    public function portofolio()
    {
        return $this->belongsTo(Portofolio::class, 'portofolio_id');
    }




    protected static function booted()
    {
        static::created(function ($karya) {
            event(new KaryaUpdated($karya->portofolio_id));
        });

        static::deleted(function ($karya) {
            event(new KaryaUpdated($karya->portofolio_id));
        });
    }

}
