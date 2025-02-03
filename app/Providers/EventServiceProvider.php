<?php

namespace App\Providers;

use App\Events\KaryaUpdated;
use App\Listeners\UpdateJumlahKarya;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    // protected $listen = [
    //     Registered::class => [
    //         SendEmailVerificationNotification::class,
    //     ],
    // ];

protected $listen = [
    Registered::class => [
        SendEmailVerificationNotification::class,
    ],
    KaryaUpdated::class => [
        UpdateJumlahKarya::class,
    ],
];





public function boot()
{
    parent::boot();
}
}
