<?php
// App\Listeners\UpdateJumlahKarya.php
namespace App\Listeners;

use App\Events\KaryaUpdated;
use App\Models\Portofolio;
use Illuminate\Support\Facades\DB;

class UpdateJumlahKarya
{
    public function __construct()
    {
        //
    }

    public function handle(KaryaUpdated $event)
    {
        $portofolioId = $event->portofolioId; // Changed from senimanId to portofolioId
        $jumlahKarya = DB::table('karyas')->where('portofolio_id', $portofolioId)->count();

        Portofolio::where('id', $portofolioId) // Use portofolio_id to find the portfolio
            ->update(['jumlah_karya' => $jumlahKarya]);
    }
}


