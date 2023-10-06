<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\User;  

//--- COMMAND UNTUK USER YANG BOLOS ---\\

class MarkSkippedUsersCommand extends Command
{
    protected $signature = 'users:mark-skipped';
    protected $description = 'Mark users who skipped work';
    
    public function handle()
    {
        $today = now()->toDateString();
    
        $usersWhoHaveNotMarked = DB::table('users')
            ->leftJoin('presences', function($join) use ($today) {
                $join->on('users.id', '=', 'presences.user_id')
                    ->where('presences.date', $today);
            })
            ->whereNull('presences.id')
            ->whereDoesntHave('roles', function ($query) {
                $query->where('name', 'super-admin');
            })
            ->select('users.id')
            ->get();
    
        foreach ($usersWhoHaveNotMarked as $user) {
            DB::table('presences')->insert([
                'user_id' => $user->id,
                'category' => 'skip',
                'entry_time' => '00:00:00',
                'exit_time' => '00:00:00',
                'temporary_entry_time' => '00:00:00',
                'date' => $today,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    
        $this->info('Marked users who skipped work today. (Menandakan users yang bolos kerja hari ini)');
    }      
}
