<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Concert;
use App\Ticket;
use Faker\Generator as Faker;
use Illuminate\Support\Carbon;

$factory->define(Ticket::class, function (Faker $faker) {
    return [
        'concert_id' => function () {
            return factory(Concert::class)->create()->id;
        },
    ];
});

$factory->state(Ticket::class, 'reserved', function ($faker) {
    return [
        'reserved_at' => Carbon::now(),
    ];
});

