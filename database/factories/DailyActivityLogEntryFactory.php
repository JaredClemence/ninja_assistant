<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\AppDailyActivityLogEntry;
use Faker\Generator as Faker;

$factory->define(AppDailyActivityLogEntry::class, function (Faker $faker) {
    return [
        'user_id'=>$faker->randomNumber(),
        'contact_id'=>$faker->randomNumber(),
        'action'=>'call',
        'family'=>$faker->sentence,
        'occupation'=>$faker->sentence,
        'recreation'=>$faker->sentence,
        'dreams'=>$faker->sentence,
        'created_at'=>$faker->dateTimeBetween('-5 weeks')
    ];
});
