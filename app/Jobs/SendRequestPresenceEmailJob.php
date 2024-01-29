<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Presence;
use Illuminate\Bus\Queueable;
use App\Mail\RequestLeaveEmail;
use App\Mail\RequestPresenceEmail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendRequestPresenceEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $presence;
    protected $user;
    protected $approver;
    protected $workTrip;
    protected $telework;

    /**
     * Create a new job instance.
     *
     * @param Presence $presence
     * @param User $user
     * @param User $approver
     * @param mixed $workTrip
     * @param mixed $telework
     */
    public function __construct(Presence $presence, User $user, User $approver, $workTrip, $telework)
    {
        $this->presence = $presence;
        $this->user = $user;
        $this->approver = $approver;
        $this->workTrip = $workTrip;
        $this->telework = $telework;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        \Mail::to($this->approver->email)->send(new RequestPresenceEmail($this->presence, $this->user, $this->approver, $this->workTrip, $this->telework));
    }
}
