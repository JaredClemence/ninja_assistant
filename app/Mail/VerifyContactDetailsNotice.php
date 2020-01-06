<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\User;
use Illuminate\Support\Facades\Log;

class VerifyContactDetailsNotice extends Mailable
{
    use Queueable, SerializesModels;

    /** @var User */
    public $user;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        $subject = "Ninja Assistant - Please verify your contact list before we finish.";
        $builder = $this->from(env('MAIL_FROM_ADDRESS'))->to($this->user->email)->subject($subject)->text('email.text.verify');
        $stringEmails = array_map( function( $email ){
            extract($email);
            if( $name ){
                return "$name <$address>";
            }else{
                return "$address";
            }
        }, $this->to);
        $email = implode(", ", $stringEmails);
        $subject = $this->subject;
        $time = \Carbon\Carbon::now()->format("m-d-Y H:i:s O");
        Log::info("Email with subject ($subject) sent to ($email) at time ($time).");
        return $builder;
    }
}
