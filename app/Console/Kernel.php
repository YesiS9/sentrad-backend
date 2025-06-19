<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

use App\Models\RegistrasiKelompok;
use App\Models\RegistrasiIndividu;
use App\Models\Penilai;
use App\Models\KuotaPenilaian;
use Carbon\Carbon;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            try {
                $penilai = Penilai::all();
                foreach ($penilai as $p) {
                    $periode = now()->format('Y-m');
                    $existingQuota = KuotaPenilaian::where('penilai_id', $p->id)
                        ->where('periode_penilaian', $periode)
                        ->first();

                    if (!$existingQuota) {
                        KuotaPenilaian::create([
                            'penilai_id' => $p->id,
                            'periode_penilaian' => $periode,
                            'kuota_terpakai' => 0,
                        ]);
                        \Log::info("Kuota penilaian dibuat untuk penilai {$p->id} di periode {$periode}");
                    }
                }
            } catch (\Throwable $e) {
                \Log::error('Gagal membuat kuota penilaian: ' . $e->getMessage());
            }
        })->monthlyOn(1, '00:00');

        $schedule->call(function () {
            try {
                $kelompoks = RegistrasiKelompok::where('status_kelompok', 'Pengajuan Pendaftaran')
                    ->where('created_at', '<=', now()->subHours(24))
                    ->get();

                foreach ($kelompoks as $kelompok) {
                    if ($kelompok->status_kelompok !== 'Dalam proses penilaian') {
                        $kelompok->status_kelompok = 'Dalam proses penilaian';
                        $kelompok->save();
                        \Log::info("Status kelompok {$kelompok->id} diubah menjadi 'Dalam proses penilaian'");
                    }
                }
            } catch (\Throwable $e) {
                \Log::error('Gagal memperbarui status kelompok: ' . $e->getMessage());
            }
        })->hourly();

        $schedule->call(function () {
            try {
                $individus = RegistrasiIndividu::where('status_individu', 'Pengajuan Pendaftaran')
                    ->where('created_at', '<=', now()->subHours(24))
                    ->get();

                foreach ($individus as $individu) {
                    if ($individu->status_individu !== 'Dalam proses penilaian') {
                        $individu->status_individu = 'Dalam proses penilaian';
                        $individu->save();
                        \Log::info("Status individu {$individu->id} diubah menjadi 'Dalam proses penilaian'");
                    }
                }
            } catch (\Throwable $e) {
                \Log::error('Gagal memperbarui status individu: ' . $e->getMessage());
            }
        })->hourly();
    }


    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
