<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\DailyActivityLogEntry;
use Faker\Generator as Faker;
use App\Contact;
use App\User;

$factory->define(DailyActivityLogEntry::class, function (Faker $faker) {
    $user = factory(User::class)->create();
    return [
        'user_id'=>$user->id,
        'contact_id'=>factory(Contact::class)->create(['user_id'=>$user->id]),
        'action'=>'call',
        'family'=>$faker->sentence,
        'occupation'=>$faker->sentence,
        'recreation'=>$faker->sentence,
        'dreams'=>$faker->sentence,
        'created_at'=>$faker->dateTimeBetween('-5 weeks')
    ];
});
