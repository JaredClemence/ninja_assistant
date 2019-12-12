<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\ContactCsvFile;
use Faker\Generator as Faker;

$factory->define(ContactCsvFile::class, function (Faker $faker) {
    return [
        'format'=>$faker->randomElement(['iphone','android'])
    ];
});
