<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

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
