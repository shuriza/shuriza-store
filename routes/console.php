<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Auto-cancel pending orders (runs daily at midnight)
Schedule::command('orders:auto-cancel')->daily();

// Replay failed payment webhooks every 10 minutes
Schedule::command('payment:webhooks:replay --limit=100')->everyTenMinutes();
