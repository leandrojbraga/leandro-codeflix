<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\CastMember;
use Faker\Generator as Faker;

$factory->define(CastMember::class, function (Faker $faker) {
    $types = array_keys(CastMember::TYPES);

    return [
        'name' => $faker->firstName,
        'type' => $types[array_rand($types)]
    ];
});
