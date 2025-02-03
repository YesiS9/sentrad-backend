<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Map extends Model
{
    use HasUuids, HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'seniman_id',
        'name',
        'description',
        'latitude',
        'longitude'
    ];

    public function seniman()
    {
        return $this->belongsTo(Seniman::class, 'seniman_id', 'id');
    }
}
