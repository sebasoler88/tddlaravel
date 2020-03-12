<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Model;
use Faker\Generator as Faker;
use Illuminate\Support\Carbon;

$factory->define(\App\Concert::class, function (Faker $faker) {
    return [
        'title' => 'Example band',
        'subtitle' => 'with the fake openers',
        'date' => Carbon::parse('+2 weeks'),
        'ticket_price' => 2000,
        'venue' => 'The example Theatre',
        'venue_address' => '123 Example Lane',
        'city' => 'Laraville',
        'state' => 'ON',
        'zip' => '17916',
        'additional_information' => 'For tickets, call (555) 555-5555.',
    ];
});
