<?php

namespace App\Mail;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Presence;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;

class RequestPresenceEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $presence;
    public $user;
    public $approvers;
    public $workTrip;
    public $telework;

    /**
     * Create a new message instance.
     */
    public function __construct(Presence $presence, User $user, User $approvers, $workTrip, $telework )
    {
        $this->presence = $presence;
        $this->user = $user;
        $this->approvers = $approvers;
        $this->workTrip = $workTrip;
        $this->telework = $telework;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $date = Carbon::parse($this->presence->date)->format('d F Y');

        return $this->subject('Pengajuan Presensi | '.$this->user->name.' | '.$date)
                    ->view('email.request_presence_email')
                    ->with([
                        'workTrip' => $this->workTrip,
                        'telework' => $this->telework,
                    ]);
    }
}

