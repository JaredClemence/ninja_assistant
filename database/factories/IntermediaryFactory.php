<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Clemence\Contact\IntermediateRecord as Model;
use Faker\Generator as Faker;
use App\Http\Controllers\AbstractFactory\CsvLines\ContactJsonObj;

$factory->define(Model::class, function (Faker $faker) {
    $name = $faker->name;
    $address = '\"' . str_replace("\n",";", $faker->address) . '\"';
    $email = $faker->email;
    $phone = $faker->phoneNumber;
    $json = new ContactJsonObj();
    $json->address = $address;
    $json->email = $email;
    $json->name = $name;
    $json->notes = "";
    $json->phones = [ $phone ];
    return [
        'header'=>'name,address,notes,email,phone',
        'line'=>"$name,$address,,$email,$phone",
        'json'=>serialize($json),
        'format'=>'factory',
        'finished'=>0,
        'contact_csv_id'=>0,
        'contact_id'=>0,
        'user_id'=>0
    ];
});

$factory->state(Model::class, 'finished', function (Faker $faker) {
    return [
        'finished'=>1
    ];
});
