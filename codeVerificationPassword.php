<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class codeVerificationPassword extends Mailable
{
    use Queueable, SerializesModels;
    private $token;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $this->subject('Recuperação de senha');
        $this->to(session('email'), 'Arão Domingos');

        return $this->view('mail.code-verification', [
            'token' => $this->token
        ]);
    }
}
