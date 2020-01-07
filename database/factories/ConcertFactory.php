<?php

use Carbon\Carbon;
use Faker\Generator as Faker;

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| This directory should contain each of the model factory definitions for
| your application. Factories provide a convenient way to generate new
| model instances for testing / seeding your application's database.
|
*/

$factory->define(App\Concert::class, function (Faker $faker) {
    return [
        'title' => 'The Red Chord',
        'subtitle' => 'with Animosity and Lethargy',
        'date' => Carbon::parse('+2 weeks'),
        'ticket_price' => 2000,
        'venue' => 'The Mosh Pit',
        'venue_address' => '123 Example Lane',
        'city' => 'Laraville',
        'state' => 'ON',
        'zip' => '90210',
        'additional_information' => 'Some sample additional information'
    ];
});

$factory->state(App\Concert::class, 'published', function ($faker) {
    return [
      'published_at' => Carbon::parse('-1 week'),
    ];
});

$factory->state(App\Concert::class, 'unpublished', function ($faker) {
    return [
        'published_at' => null,
    ];
});
