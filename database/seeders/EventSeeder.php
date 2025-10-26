<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;
use App\Models\Club;
use App\Models\User;

class EventSeeder extends Seeder
{
    public function run()
    {
        $faker = \Faker\Factory::create();

        $clubIds = Club::pluck('id')->toArray();
        $userIds = User::pluck('id')->toArray();

        for ($i = 0; $i < 15; $i++) {
            $start = $faker->dateTimeBetween('now', '+1 month');
            $end = (clone $start)->modify('+2 hours');

            Event::create([
                'club_id' => $faker->randomElement($clubIds),
                'name' => ucfirst($faker->words(3, true)),
                'description' => $faker->paragraph(),
                'start_time' => $start,
                'end_time' => $end,
                'location' => $faker->address(),
                'max_participants' => $faker->numberBetween(20, 200),
                'is_public' => $faker->boolean(),
                'status' => $faker->randomElement(['pending', 'approved', 'rejected']),
                'created_by' => $faker->randomElement($userIds),
                'approval_by' => $faker->randomElement($userIds),
                'budget' => $faker->randomFloat(2, 100000, 2000000),
            ]);
        }
    }
}
