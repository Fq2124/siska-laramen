<?php

namespace App\Mail\Partners;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ApplicantListEmail extends Mailable
{
    use Queueable, SerializesModels;
    public $vacancy, $filename;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($vacancy, $filename)
    {
        $this->vacancy = $vacancy;
        $this->filename = $filename;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $vacancy = $this->vacancy;
        $filename = $this->filename;
        $recruitmentDate = Carbon::parse($vacancy->recruitmentDate_start)->format('j F Y') . " - " .
            Carbon::parse($vacancy->recruitmentDate_end)->format('j F Y');

        return $this->subject("" . $vacancy->judul . ": Application List for " . $recruitmentDate)
            ->from(env('MAIL_USERNAME'), 'SISKA - Sistem Informasi Karier')
            ->view('emails.partners.applicantList')
            ->attach(env('APP_URL') . '/local/storage/app/public/users/partners/reports/applications/' . $filename);
    }
}