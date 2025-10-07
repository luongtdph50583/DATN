<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class ClubMemberSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        $clubMembers = [];
        for ($i = 1; $i <= 20; $i++) {
            $clubMembers[] = [
                'club_id' => $faker->numberBetween(1, 10),
                'user_id' => $faker->numberBetween(6, 15), // Thành viên
                'role' => $faker->randomElement(['admin', 'member']),
                'joined_at' => $faker->dateTimeThisYear(),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('club_members')->insert($clubMembers);
    }
}