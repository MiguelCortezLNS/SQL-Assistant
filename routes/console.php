<?php

use App\Console\Commands\PruneOldChats;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Delete chats inactive for more than 20 days, runs every night at 02:00
Schedule::command(PruneOldChats::class)->dailyAt('02:00');
