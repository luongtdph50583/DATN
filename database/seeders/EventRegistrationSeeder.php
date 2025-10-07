<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class EventRegistrationSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        $registrations = [];
        for ($i = 1; $i <= 20; $i++) {
            $registrations[] = [
                'event_id' => $faker->numberBetween(1, 12),
                'user_id' => $faker->numberBetween(6, 15),
                'registered_at' => $faker->dateTimeThisYear(),
                'attendance_status' => $faker->randomElement(['pending', 'attended', 'absent']),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('event_registrations')->insert($registrations);
    }
}