<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class EventSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        $events = [];
        for ($i = 1; $i <= 12; $i++) {
            $events[] = [
                'club_id' => $i <= 8 ? $faker->numberBetween(1, 10) : null,
                'name' => $faker->sentence(4),
                'description' => $faker->paragraph,
                'event_date' => $faker->dateTimeBetween('now', '+6 months'),
                'location' => $faker->address,
                'media_id' => $faker->numberBetween(1, 15),
                'status' => $faker->randomElement(['pending', 'approved', 'rejected']),
                'created_by' => $faker->numberBetween(1, 5),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('events')->insert($events);
    }
}