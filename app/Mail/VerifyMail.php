<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class VerifyMail extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $clearPassword;

    /**
     * Create a new message instance.
     *
     * @param $user
     * @param $clearPassword
     *
     * @return void
     */
    public function __construct($user, $clearPassword)
    {
        $this->user = $user;
        $this->clearPassword = $clearPassword;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.verifyUser');
    }
}
