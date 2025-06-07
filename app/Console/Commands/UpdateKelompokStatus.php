<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\RegistrasiKelompok;
use Carbon\Carbon;

class UpdateKelompokStatus extends Command
{
    protected $signature = 'kelompok:update-status';
    protected $description = 'Update status registrasi kelompok dari Pengajuan Pendaftaran ke Proses setelah 24 jam';

    public function handle()
    {
        $now = Carbon::now();

        $kelompoks = RegistrasiKelompok::where('status_kelompok', 'Pengajuan Pendaftaran')
            ->where('created_at', '<=', $now->subHours(24))
            ->get();

        foreach ($kelompoks as $kelompok) {
            $kelompok->status_kelompok = 'Dalam proses penilaian';
            $kelompok->save();
        }

        $this->info(count($kelompoks) . 'Registrasi kelompok diperbarui statusnya.');
    }
}
