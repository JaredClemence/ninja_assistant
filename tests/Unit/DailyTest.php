<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\User;
use App\Contact;
use App\Http\Controllers\Ninja\Daily;
use App\DailyData;

/**
 * @group daily
 */
class DailyTest extends TestCase
{
    use RefreshDatabase;
    
    public function testDailySuccessfulInit()
    {
        $user = $this->initUser();
        $daily = new Daily($user);
        $this->assertInstanceOf(Daily::class,$daily,"A new daily object is created.");
        $this->assertGreaterThan(0, $daily->calls->count(), "There are more than zero contacts for calling.");
        $this->assertGreaterThan(0, $daily->mail->count(), "There are more than zero contacts for mailing.");
        
        $data = DailyData::initToday($user);
        $callers = $data->readCallList();
        $maillers = $data->readMailList();
        
        $this->assertEquals( count($callers), $daily->calls->count(), "The number of ids in the caller list is not the same as the number of callers.");
        $this->assertEquals( count($maillers), $daily->mail->count(), "The number of ids in the caller list is not the same as the number of callers.");
    }
    
    public function testLoadFromExistingData(){
        $user = $this->initUser();
        $contacts = $user->contacts;
        $callList = $contacts->random(13);
        $mailList = $contacts->random(2);
        $data = DailyData::initToday($user);
        $data->setCallList($callList);
        $data->setMailList($mailList);
        $data->save();
        
        $daily = new Daily($user);
        
        foreach( $daily->calls as $caller ){
            $this->assertTrue( $callList->contains($caller->id), "The daily has a caller that is not in the previous daily data.");
        }
        foreach( $daily->mail as $mailer ){
            $this->assertTrue( $mailList->contains($mailer->id), "The daily has a mailer that is not in the previous daily data.");
        }
    }

    private function initUser() {
        $user = factory(User::class)->create();
        factory(Contact::class,20)->create(['user_id'=>$user->id]);
        return $user;
    }

}
