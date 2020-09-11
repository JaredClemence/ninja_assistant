<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Subscriber;
use Faker\Generator as Faker;
use App\User;

$factory->define(Subscriber::class, function (Faker $faker) {
    return [
        'user_id'=> factory(User::class)->create(),
        'expire'=>$faker->dateTimeBetween('-1 week', '52 weeks')
    ];
});
