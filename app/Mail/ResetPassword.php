<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class ResetPassword extends Mailable
{
    use Queueable, SerializesModels;

    public $user;
    public $clearPassword;

    /**
     * ResetPassword constructor.
     *
     * @param $user
     * @param $clearPassword
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
        return $this->view('emails.resetPassword');
    }
}
