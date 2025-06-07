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
            $penilai = Penilai::all();

            foreach ($penilai as $p) {
                $existingQuota = KuotaPenilaian::where('penilai_id', $p->id)
                    ->where('periode_penilaian', now()->format('Y-m'))
                    ->first();

                if (!$existingQuota) {
                    KuotaPenilaian::create([
                        'penilai_id' => $p->id,
                        'periode_penilaian' => now()->format('Y-m'),
                        'kuota_terpakai' => 0,
                    ]);
                }
            }
        })->monthlyOn(1, '00:00');

        $schedule->call(function () {
            $kelompoks = RegistrasiKelompok::where('status_kelompok', 'Pengajuan Pendaftaran')
                ->where('created_at', '<=', Carbon::now()->subHours(24))
                ->get();

            foreach ($kelompoks as $kelompok) {
                $kelompok->status_kelompok = 'Dalam proses penilaian';
                $kelompok->save();
            }
        })->hourly();

        $schedule->call(function () {
            $individus = RegistrasiIndividu::where('status_individu', 'Pengajuan Pendaftaran')
                ->where('created_at', '<=', Carbon::now()->subHours(24))
                ->get();

            foreach ($individus as $individu) {
                $individu->status_individu = 'Dalam proses penilaian';
                $individu->save();
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
