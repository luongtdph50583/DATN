<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class ClubJoinRequestSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        $requests = [];
        for ($i = 1; $i <= 15; $i++) {
            $requests[] = [
                'club_id' => $faker->numberBetween(1, 10),
                'user_id' => $faker->numberBetween(6, 15),
                'status' => $faker->randomElement(['pending', 'approved', 'rejected']),
                'requested_at' => $faker->dateTimeThisYear(),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('club_join_requests')->insert($requests);
    }
}