<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\ContentDescriptor;
use Faker\Generator as Faker;

$factory->define(ContentDescriptor::class, function (Faker $faker) {
    return [
        'name' => $faker->city
    ];
});
