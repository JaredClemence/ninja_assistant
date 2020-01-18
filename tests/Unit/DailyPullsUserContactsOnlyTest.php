<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\User;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Ninja\Daily;
use App\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * @group daily
 */
class DailyPullsUserContactsOnlyTest extends TestCase
{
    use RefreshDatabase;
    /**
     * Verify that the Daily object only pulls contacts from the active user.
     *
     * @return void
     */
    public function testNoCrossPulls()
    {
        $users = factory( User::class, 3 )->create();
        $users->each( function( $user ){
            factory( Contact::class, 15 )->create(['user_id'=>$user->id]);
        } );
        $selectedUser = $users->random();
        $count = 0;
        $daily = new Daily($selectedUser);
        $daily->fresh();
        $calls = clone $daily->calls;
        $mail = clone $daily->mail;
        
        foreach( $calls as $contact ){
            $this->assertEquals( $selectedUser->id, $contact->user_id, "The system picked someone else's contact for a daily call.");
        }
        foreach( $mail as $contact ){
            $this->assertEquals( $selectedUser->id, $contact->user_id, "The system picked someone else's contact for a daily mail.");
        }
    }
}
