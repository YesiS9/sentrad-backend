<?php


namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class KaryaUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $portofolioId;

    public function __construct($portofolioId)
    {
        $this->portofolioId = $portofolioId;
    }
}


