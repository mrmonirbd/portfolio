<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ContactMail extends Mailable
{
    use Queueable, SerializesModels;

    public $name;
    public $email;
    public $subject;
    public $content;

    public function __construct($name, $email, $subject, $content)
    {
        $this->name = $name;
        $this->email = $email;
        $this->subject = $subject;
        $this->content = $content;
    }

    public function build()
    {
        return $this->from(config('mail.from.address'), config('mail.from.name')) // Set From address explicitly
                    ->subject($this->subject)
                    ->view('mail.contactmail')
                    ->with([
                        'name' => $this->name,
                        'email' => $this->email,
                        'content' => $this->content,
                    ]);
    }
}
