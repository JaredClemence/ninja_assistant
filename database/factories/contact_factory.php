<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Contact;
use Faker\Generator as Faker;
use App\Clemence\PhoneNumber;

$factory->define(Contact::class, function (Faker $faker) {
    return [
        'name'=>$faker->name,
        'address'=>$faker->address,
        'note'=>'',
        'email'=>$faker->email,
        'address'=> $faker->streetAddress,
        'active'=>1,
        'user_id'=>0,
    ];
});

$factory->afterCreating(Contact::class, function($contact, $faker){
    $types = ['Mobile','Work','Home'];
    $phone = new PhoneNumber();
    $phone->number = $faker->phoneNumber;
    $phone->name = $faker->randomElement($types);
    $contact->phones()->save($phone);
    if( rand(1,100) %2==0){
        $phone = new PhoneNumber();
        $phone->number = $faker->phoneNumber;
        $phone->name = $faker->randomElement($types);
        $contact->phones()->save($phone);
    }
} );
