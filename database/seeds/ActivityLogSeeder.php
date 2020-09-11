<?php

use Illuminate\Database\Seeder;
use App\User;
use App\DailyActivityLogEntry;
use Illuminate\Support\Facades\Hash;
use App\Contact;

class ActivityLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = factory(User::class)->create(['password'=>Hash::make('password')]);
        $contacts = $this->createContacts(50, $user);
        $this->createLogEntries( 100, $contacts, $user );
    }

    public function createContacts($qt, $user) {
        return factory(Contact::class, $qt)->create(['user_id'=>$user->id]);
    }

    public function createLogEntries($qt, $contacts, $user) {
        $contacts->each( function( $contact ) use ($qt, $user) {
            factory( DailyActivityLogEntry::class, $qt )->create([
                'user_id'=>$user->id,
                'contact_id'=>$contact->id
            ]);
        } );
    }

}
