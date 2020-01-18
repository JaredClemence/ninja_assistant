<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;
use App\Contact;
use App\Http\Controllers\Ninja\Daily;

/**
 * @group dailydisplay
 */
class DailyControllerTest extends TestCase
{
    use RefreshDatabase;
    
    /**
     * When a user skips a contact,
     * 1. the daily page is reloaded.
     * 2. the skipped contact no longer appears on the list.
     * 3. the other contacts are not affected in the display
     */
    public function testSkip()
    {
        $user = $this->initUser();
        $this->actingAs($user)->get(route('daily_call'));
        $daily = new Daily( $user );
        $calls = collect($daily->calls);
        $skip = $calls->random();
        $this->actingAs($user)->get(route('skip_contact',['contact'=>$skip]));
        $page = $this->actingAs($user)->get(route('daily_call'));
        
        $daily = new Daily($user);
        $newCallList = collect($daily->calls);
        $newCallList->each( function( $contact ) use ($deactivate){
            $this->assertNotEquals($deactivate->id, $contact->id, "None of the new call list contain the deactivated account.");
        } );
        $calls->each( function($call) use ($deactivate, $newCallList){
            if($call===$deactivate) return;
            else $this->assertTrue($newCallList->contains($call));
        } );
    }
    
    /**
     * When a user deactivates a contact,
     * 1. the contact is no longer active
     * 2. the daily page is reloaded.
     * 3. the other contacts are not affected in the display
     */
    public function testDeactivate()
    {
        $user = $this->initUser();
        $this->actingAs($user)->get(route('daily_call'));
        $daily = new Daily( $user );
        $calls = collect($daily->calls);
        $deactivate = $calls->random();
        $this->actingAs($user)->get(route('deactivate_contact',['contact'=>$deactivate]));
        $contact = Contact::where('id',$deactivate->id)->get()->first();
        $this->assertEquals(false, $contact->is_active, "The contact did not deactivate.");
        
        $daily = new Daily($user);
        $newCallList = collect($daily->calls);
        $newCallList->each( function( $contact ) use ($deactivate){
            $this->assertNotEquals($deactivate->id, $contact->id, "None of the new call list contain the deactivated account.");
        } );
        $calls->each( function($call) use ($deactivate, $newCallList){
            if($call===$deactivate) return;
            else $this->assertTrue($newCallList->contains($call));
        } );
        
        
    }

    private function initUser() {
        $user = factory( User::class )->create();
        factory(Contact::class,40)->create(['user_id'=>$user->id]);
        return $user;
    }

}
