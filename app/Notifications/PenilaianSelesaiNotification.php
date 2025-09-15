<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class PenilaianSelesaiNotification extends Notification
{
    use Queueable;

    protected $tipe;
    protected $registrasi;

    public function __construct($tipe, $registrasi)
    {
        $this->tipe = $tipe;
        $this->registrasi = $registrasi;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        if ($this->tipe === 'individu') {
            $nama = $this->registrasi->nama;
            $subject = 'Penilaian Registrasi Individu Selesai';
        } else {
            $nama = $this->registrasi->nama_kelompok;
            $subject = 'Penilaian Registrasi Kelompok Selesai';
        }

        $recipientName = $notifiable->name ?? 'User';

        return (new MailMessage)
            ->subject($subject)
            ->greeting("Halo, {$recipientName}") 
            ->line("Penilaian untuk registrasi {$this->tipe} Anda telah selesai.")
            ->line("Nama: {$nama}")
            ->action('Lihat Detail', url('/login'))
            ->line('Terima kasih telah menggunakan website kami.')
            ->salutation('Salam, Tim Sentrad');
    }
}
