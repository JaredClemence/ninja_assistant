<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Clemence\PhoneNumber;
use Faker\Generator as Faker;

$factory->define(PhoneNumber::class, function (Faker $faker) {
    return [
        'contact_id'=> factory('App\Clemence\Contact')->create()->id,
        'number'=>$faker->phoneNumber,
        'name'=>$faker->randomElement(['Mobile','Home','Work','Other'])
    ];
});
