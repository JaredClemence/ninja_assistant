<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Contact;
use Faker\Generator as Faker;

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
