<?php

use Baytek\Laravel\Content\Types\Committee\Models\Committee;

/**
 * Committees
 */
$factory->define(Committee::class, function (Faker\Generator $faker) {

    $title = ucwords(implode(' ', $faker->words(rand(1,3))));

    return [
        'key' => str_slug($title),
        'title' => $title,
        'content' => null,
        'status' => Committee::APPROVED,
        'language' => App::getLocale(),
    ];
});
