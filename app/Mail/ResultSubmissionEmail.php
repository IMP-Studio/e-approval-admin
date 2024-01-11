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

class ResultSubmissionEmail extends Mailable
{
    use Queueable, SerializesModels;

    public $presence;
    public $user;
    public $workTrip;
    public $telework;
    public $leave;

    /**
     * Create a new message instance.
     */
    public function __construct(Presence $presence, User $user, $workTrip, $telework, $leave )
    {
        $this->presence = $presence;
        $this->user = $user;
        $this->workTrip = $workTrip;
        $this->telework = $telework;
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

        return $this->subject('Hasil Approval | '.$this->user->name.' | '.$date)
                    ->view('email.result_submission_email')
                    ->with([
                        'workTrip' => $this->workTrip,
                        'telework' => $this->telework,
                        'leave' => $this->leave,
                    ]);
    }
}
