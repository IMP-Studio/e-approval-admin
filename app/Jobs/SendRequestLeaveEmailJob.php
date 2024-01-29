<?php

namespace App\Jobs;

use App\Mail\RequestLeaveEmail;
use App\Models\Presence;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendRequestLeaveEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $presence;
    protected $user;
    protected $approver;
    protected $leave;

    /**
     * Create a new job instance.
     *
     * @param Presence $presence
     * @param User $user
     * @param User $approver
     * @param mixed $leave
     */
    public function __construct(Presence $presence, User $user, User $approver, $leave)
    {
        $this->presence = $presence;
        $this->user = $user;
        $this->approver = $approver;
        $this->leave = $leave;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        \Mail::to($this->approver->email)->send(new RequestLeaveEmail($this->presence, $this->user, $this->approver, $this->leave));
    }
}
