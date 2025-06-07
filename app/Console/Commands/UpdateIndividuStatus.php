<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\RegistrasiIndividu;
use Carbon\Carbon;

class UpdateIndividuStatus extends Command
{
    protected $signature = 'individu:update-status';
    protected $description = 'Update status registrasi individu dari Pengajuan Pendaftaran ke Proses setelah 24 jam';

    public function handle()
    {
        $now = Carbon::now();

        $individus = RegistrasiIndividu::where('status_individu', 'Pengajuan Pendaftaran')
            ->where('created_at', '<=', $now->subHours(24))
            ->get();

        foreach ($individus as $individu) {
            $individu->status_individu = 'Dalam proses penilaian';
            $individu->save();
        }

        $this->info(count($individus) . ' Registrasi individu diperbarui statusnya.');
    }
}
