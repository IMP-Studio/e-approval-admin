<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Presence;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;
use Carbon\Carbon;

class RequestLeaveEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $presence;
    public $user;
    public $approvers;
    public $leave;
    
    /**
     * Create a new message instance.
     */
    public function __construct(Presence $presence, User $user, User $approvers, $leave )
    {
        $this->presence = $presence;
        $this->user = $user;
        $this->approvers = $approvers;
        $this->leave = $leave;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $date = Carbon::parse($this->presence->date)->format('d F Y');

        return $this->subject('Pengajuan Cuti | '.$this->user->name.' | '.$date)
                    ->view('email.request_leave_email')
                    ->with([
                        'leave' => $this->leave,
                    ]);
    }
}
