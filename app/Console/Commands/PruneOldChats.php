<?php

namespace App\Console\Commands;

use App\Models\Chat;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;

class PruneOldChats extends Command
{
    protected $signature   = 'chats:prune {--days=20 : Days of inactivity before deletion}';
    protected $description = 'Delete chats that have not been used in the given number of days';

    public function handle(): int
    {
        $days    = (int) $this->option('days');
        $cutoff  = Carbon::now()->subDays($days);
        $deleted = Chat::where('updated_at', '<', $cutoff)->delete();

        $this->info("Pruned {$deleted} chat(s) inactive for more than {$days} days.");

        return Command::SUCCESS;
    }
}
