<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\ViewLog;
use Carbon\Carbon;
use Faker\Generator as Faker;

$factory->define(ViewLog::class, function (Faker $faker) {
    return [
        'user_id' => 1,
        'ip' => $faker->unique()->ipv4,
        'view_at' => Carbon::now()->subDays($faker->numberBetween(0, 7)),
    ];
});
