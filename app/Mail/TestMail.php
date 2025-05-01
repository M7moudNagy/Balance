<?php
namespace App\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TestMail extends Mailable
{
    use Queueable, SerializesModels;

    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function build()
    {
        $resetUrl = url('https://mostafamnesey.github.io/Balance-Front/#/resetpassword?token=' . $this->token);

        return $this->from('mneseym@gmail.com')
            ->subject('Reset Your Password')
            ->view('emails.reset_password')
            ->with(['resetUrl' => $resetUrl]);
    }
//    public function build()
//    {
//        return $this->from('mneseym@gmail.com')
//            ->subject('Test Email using SendGrid')
//            ->view('welcome');
//    }

}
