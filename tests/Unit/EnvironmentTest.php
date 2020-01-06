<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Mail;
use App\Mail\NinjaAssistantSignupConfirmation;
use App\User;

/**
 * @group environment
 */
class EnvironmentTest extends TestCase
{
    use RefreshDatabase;
    
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testMailFrom()
    {
        $this->assertEquals("jaredclemence@gmail.com",env("MAIL_FROM"));
    }
    
    public function testSendMail(){
        $user = User::firstOrCreate( [
            'name'=>"Jared Clemence",
            'email'=>"jaredclemence@gmail.com",
            'password'=>''
        ] );
        $mail = new NinjaAssistantSignupConfirmation($user);
        Mail::send($mail);
        $this->assertTrue(true, "No error occured during the send operation.");
    }
}
