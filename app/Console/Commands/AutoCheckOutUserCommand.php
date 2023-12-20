<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AutoCheckOutUserCommand extends Command
{
    protected $signature = 'users:auto-checkout';
    protected $description = 'Automatically check out users who forgot';

    public function handle()
    {
        $today = now()->toDateString();
        $usersWhoForgotCheckout = DB::table('presences')
            ->where('date', $today)
            ->where('entry_time', '!=', '00:00:00')
            ->where('exit_time', '=', '00:00:00')
            ->select('user_id')
            ->get();

        foreach ($usersWhoForgotCheckout as $presence) {
            DB::table('presences')
                ->where('user_id', $presence->user_id)
                ->where('date', $today)
                ->update([
                    'exit_time' => '23:59:00',
                    'updated_at' => now()
                ]);
        }
        $this->info('Starting auto checkout process...');
        $this->info('Found ' . $usersWhoForgotCheckout->count() . ' users to checkout.');
        $this->info('Automatically checked out users who forgot today.');
    }
}
