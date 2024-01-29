<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Presence;
use Illuminate\Bus\Queueable;
use App\Mail\ResultSubmissionEmail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;

class SendResultSubmissionEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $presence;
    protected $user;
    protected $workTrip;
    protected $telework;
    protected $leave;


    /**
     * Create a new job instance.
     *
     * @param Presence $presence
     * @param User $user
     * @param mixed $workTrip
     * @param mixed $telework
     * @param mixed $leave
     */
    public function __construct(Presence $presence, User $user, $workTrip, $telework , $leave)
    {
        $this->presence = $presence;
        $this->user = $user;
        $this->workTrip = $workTrip;
        $this->telework = $telework;
        $this->leave = $leave;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            \Mail::to($this->user->email)->send(new ResultSubmissionEmail($this->presence, $this->user, $this->workTrip, $this->telework,$this->leave));
        } catch (\Exception $e) {
            \Log::error('Error: ' . $e->getMessage());
            \Log::error('File: ' . $e->getFile());
            \Log::error('Line: ' . $e->getLine());
        }
    
    }
}
