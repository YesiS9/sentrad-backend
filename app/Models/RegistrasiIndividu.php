<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Notifications\PenilaianSelesaiNotification;

class RegistrasiIndividu extends Model
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
            // Cek apakah `status_individu` berubah menjadi 'Penilaian Selesai'
            if ($registrasi->isDirty('status_individu') && $registrasi->status_individu === 'Penilaian Selesai') {
                $seniman = $registrasi->seniman; // Relasi ke seniman
                if ($seniman && $seniman->user) { // Pastikan ada user terkait
                    $seniman->user->notify(new PenilaianSelesaiNotification('individu', $registrasi));
                }
            }
        });
    }

}

