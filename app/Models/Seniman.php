<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class Seniman extends Model
{
    use HasUuids, SoftDeletes, HasFactory, Notifiable;
    protected $table = 'seniman';

    protected $fillable = [
        'user_id',
        'tingkatan_id',
        'nama_seniman',
        'tgl_lahir',
        'deskripsi_seniman',
        'alamat_seniman',
        'noTelp_seniman',
        'lama_pengalaman',
        'status_seniman'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function tingkatan()
    {
        return $this->belongsTo(Tingkatan::class, 'tingkatan_id', 'id');
    }

    public function registrasiIndividu()
    {
        return $this->hasOne(RegistrasiIndividu::class, 'seniman_id');
    }

    public function registrasiKelompok()
    {
        return $this->hasMany(RegistrasiKelompok::class);
    }

    public function portofolios()
    {
        return $this->hasMany(Portofolio::class, 'seniman_id');
    }
}

