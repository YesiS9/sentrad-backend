<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RegistrasiKelompok extends Model
{
    use HasUuids, SoftDeletes, HasFactory;


    protected $fillable = [
        'seniman_id',
        'kategori_id',
        'nama_kelompok',
        'tgl_terbentuk',
        'alamat_kelompok',
        'deskripsi_kelompok',
        'noTelp_kelompok',
        'email_kelompok',
        'jumlah_anggota',
        'status_kelompok',
    ];

    public function seniman()
    {
        return $this->belongsTo(Seniman::class);
    }

    public function kategoriSeni()
    {
        return $this->belongsTo(KategoriSeni::class, 'kategori_id');
    }

    protected static function boot()
    {
        parent::boot();

        static::updated(function ($registrasi) {
            if ($registrasi->isDirty('status_kelompok') && $registrasi->status_kelompok === 'Penilaian Selesai') {
                if (!empty($registrasi->email_kelompok)) {
                    \Notification::route('mail', $registrasi->email_kelompok)
                        ->notify(new PenilaianSelesaiNotification('kelompok', $registrasi));
                }
            }
        });
    }

}
