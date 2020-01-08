<?php

namespace Tests\Unit\Middleware;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\User;

class OnlyNonFinishedIntermediatesCauseRedirectTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     * @group middleware
     * @dataProvider redirectSource
     */
    public function testRedirect($caseNo)
    {
        $user = factory(User::class)->create();
        $redirectEndpoint = '';
        switch( $caseNo ){
            case 0:
                $this->buildNoContacts($user);
                $redirectEndpoint = '/contacts/upload';
                break;
            case 1:
                $this->buildWithIntermediateRecords($user);
                $redirectEndpoint = '/contacts/upload';
                break;
            case 2:
                $contacts = 4;
                $intermediates = 0;
                $this->buildWithFinishedContacts( $user, $contacts, $intermediates );
                $redirectEndpoint = '/ninja/daily';
                break;
            case 3:
                $contacts = 4;
                $intermediates = 2;
                $this->buildWithFinishedContacts( $user, $contacts, $intermediates );
                //changed after we introduced an email that sends users to the 
                //verification page.
                $redirectEndpoint = '/ninja/daily';
                break;
        }
        $result = $this->actingAs($user)->get('/');
        $result->assertRedirect($redirectEndpoint);
    }
    public function redirectSource(){
        return [
            'No Data'=>[0],
            'Intermidete Records'=>[1],
            'Finished Contacts' =>[2],
            'Finished Contacts with New Intermediate Records' =>[3]
        ];
    }

    public function buildNoContacts($user) {
        return;
    }

    public function buildWithIntermediateRecords($user) {
        $id = $user->id;
        $intermediates = factory(\App\Clemence\Contact\IntermediateRecord::class,5)->create(['user_id'=>$id]);
    }

    public function buildWithFinishedContacts($user, $contacts, $intermediates) {
        $id = $user->id;
        $params = ['user_id'=>$id];
        $contacts = factory(\App\Contact::class, $contacts)->create($params);
        $contacts->each( function( $contact ) use ($id) {
            factory(\App\Clemence\Contact\IntermediateRecord::class, 1)->state('finished')->create(['user_id'=>$id,'contact_id'=>$contact->id]);
        } );
        $intermediates = factory(\App\Clemence\Contact\IntermediateRecord::class, $intermediates)->create($params);
    }

}
