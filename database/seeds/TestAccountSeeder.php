<?php

use Illuminate\Database\Seeder;
use App\User;
use Illuminate\Support\Facades\Hash;
use App\Contact;
use App\DailyActivityLogEntry;

class TestAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = factory( User::class )->create([
            'email'=>'jaredclemence@gmail.com',
            'name'=>'Jared Clemence',
            'password'=>Hash::make('password')
            ]);
        for($i=0; $i<40; $i++ ){
            $contact = factory( Contact::class )->create( [
                'user_id'=> $user->id
            ] );
            $contactCount = rand(1,5);
            factory(DailyActivityLogEntry::class,$contactCount)->create([
                'user_id'=>$user->id,
                'contact_id'=>$contact->id,
                'action'=>'call'
            ]);
        }
    }
}
