<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Forum extends Model
{
    use HasUuids, SoftDeletes, HasFactory;

    protected $fillable = [
        'seniman_id',
        'kategori_id',
        'judul_forum',
        'status_forum'
    ];

    public function anggotaForums()
{
    return $this->hasMany(AnggotaForum::class, 'forum_id');
}



}
