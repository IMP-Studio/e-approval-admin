<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\Employee;
use App\Models\Leave;
use App\Models\Presence;
use App\Models\Telework;
use App\Models\WorkTrip;
use App\Models\StatusCommit;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Jobs\SendResultSubmissionEmailJob;

class AutoRejectCommand extends Command
{
    protected $signature = 'users:auto-reject-command';
    protected $description = 'Automatically reject submission';

    public function handle()
    {
        try{
            $today = now()->toDateString();
            $currentTime = now()->format('H:i');
            $statuses = ['pending', 'preliminary'];

            $statusCommits = StatusCommit::with('statusable')
            ->whereIn('status', $statuses)
            ->whereDate('created_at', $today)
            ->get();

            if($currentTime >= '11:00'){
                foreach ($statusCommits as $sCommit) {

                    $user = User::with(['employee'])->where('id', $sCommit->statusable->user_id)->first();
                    $statusable = $sCommit->statusable;

                    if($sCommit->statusable_type == 'App\Models\Leave'){
                        $leave = Leave::with('presence', 'statusCommit')
                        ->whereHas('statusCommit', function ($query) use ($statusable) {
                            $query->where('statusable_type', 'App\Models\Leave')
                                ->where('statusable_id', $statusable->id);
                        })
                        ->first();
                        $presence = Presence::with('leave', 'statusCommit')->where('id', $leave->presence_id)->first();

                        if($leave->start_date < $today){
                            $sCommit->status = 'rejected';
                            $sCommit->approver_id = 1;
                            $sCommit->description = 'Proposal mu ditolak karena melewati batas waktu untuk presensi';
                            $sCommit->save();

                            Log::info('Leave:', $leave->toArray());
                            dispatch(new SendResultSubmissionEmailJob($presence, $user,null, null, $leave));
                        }

                    }elseif($sCommit->statusable_type == 'App\Models\Telework'){
                        $telework = Telework::with('presence', 'statusCommit')
                        ->whereHas('statusCommit', function ($query) use ($statusable) {
                            $query->where('statusable_type', 'App\Models\Telework')
                                ->where('statusable_id', $statusable->id);
                        })
                        ->first();

                        $presence = Presence::with('telework', 'statusCommit')->where('id', $telework->presence_id)->first();

                        $sCommit->status = 'rejected';
                        $sCommit->approver_id = 1;
                        $sCommit->description = 'Proposal mu ditolak karena melewati batas waktu untuk presensi';
                        $sCommit->save();

                        dispatch(new SendResultSubmissionEmailJob($presence, $user,null, $telework, null));
                    }elseif($sCommit->statusable_type == 'App\Models\WorkTrip'){
                        $workTrip = WorkTrip::with('presence', 'statusCommit')
                        ->whereHas('statusCommit', function ($query) use ($statusable) {
                            $query->where('statusable_type', 'App\Models\WorkTrip')
                                ->where('statusable_id', $statusable->id);
                        })
                        ->first();
                        $presence = Presence::with('worktrip', 'statusCommit')->where('id', $workTrip->presence_id)->first();

                        $sCommit->status = 'rejected';
                        $sCommit->approver_id = 1;
                        $sCommit->description = 'Proposal mu ditolak karena melewati batas waktu untuk presensi';
                        $sCommit->save();

                        dispatch(new SendResultSubmissionEmailJob($presence, $user,$workTrip, null, null));
                    }
                    
                }
            }else{
                $this->info('The command is only executable after 11:00.');
            }
        }catch (\Exception $e) {
            $errorMessage = 'Error: ' . $e->getMessage();
            $stackTrace = $e->getTraceAsString();
        
            // Log the error
            Log::error($errorMessage);
            Log::error($stackTrace);
        
            // Display the error in the console
            $this->error($errorMessage);
            $this->error($stackTrace);
        }
    }
}
