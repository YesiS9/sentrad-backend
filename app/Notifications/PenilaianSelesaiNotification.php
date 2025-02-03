<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class PenilaianSelesaiNotification extends Notification
{
    use Queueable;

    protected $tipe; // tipe registrasi (individu/kelompok)
    protected $registrasi; // data registrasi

    /**
     * Constructor
     *
     * @param string $tipe
     * @param \App\Models\RegistrasiIndividu|\App\Models\RegistrasiKelompok $registrasi
     */
    public function __construct($tipe, $registrasi)
    {
        $this->tipe = $tipe;
        $this->registrasi = $registrasi;
    }

    /**
     * Define channels for notification delivery.
     *
     * @param mixed $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail']; // Menggunakan kanal email
    }

    /**
     * Build the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        if ($this->tipe === 'individu') {
            $nama = $this->registrasi->nama;
            $subject = 'Penilaian Registrasi Individu Selesai';
        } else {
            $nama = $this->registrasi->nama_kelompok;
            $subject = 'Penilaian Registrasi Kelompok Selesai';
        }

        return (new MailMessage)
            ->subject($subject)
            ->greeting("Halo, {$notifiable->name}")
            ->line("Penilaian untuk registrasi {$this->tipe} Anda telah selesai.")
            ->line("Nama: {$nama}")
            ->action('Lihat Detail', url('/login'))
            ->line('Terima kasih telah menggunakan website kami.');
    }

}
