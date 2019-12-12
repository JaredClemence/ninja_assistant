<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\UploadedFile;
use Faker\Generator as Faker;

$factory->define(UploadedFile::class, function (Faker $faker) {
    $name = $faker->randomNumber(4);
    return [
        'name' => $name,
        'full_path' => '/fake/path/for/testing/' . $name,
        'user_id' => 1
    ];
});

$factory->state(UploadedFile::class, 'processed', function( Faker $faker ){
    $date = $faker->dateTimeBetween('-15 days');
    return [
        'processed'=>1,
        'process_date'=>$date->format('Y-m-d')
    ];
} );

$factory->state(UploadedFile::class, 'archived', function( Faker $faker ){
    $date = $faker->dateTimeBetween('-180 days');
    return [
        'archived'=>1,
        'processed'=>1,
        'process_date'=>$date->format('Y-m-d')
    ];
} );