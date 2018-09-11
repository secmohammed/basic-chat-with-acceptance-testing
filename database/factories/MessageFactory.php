<?php

use Faker\Generator as Faker;

$factory->define(App\Models\Chat\Message::class, function (Faker $faker) {
	return [
		'body' => $faker->sentence(5),
		'user_id' => function () {
			return factory(App\Models\User::class)->create()->id;
		},
	];
});
