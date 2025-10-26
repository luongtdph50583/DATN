<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ClubMember;
use App\Models\Club;
use App\Models\Member;

class ClubMemberSeeder extends Seeder
{
    public function run()
    {
        $faker = \Faker\Factory::create();

        $clubIds = Club::pluck('id')->toArray();
        $memberIds = Member::pluck('id')->toArray();

        for ($i = 0; $i < 30; $i++) {
            ClubMember::create([
                'club_id' => $faker->randomElement($clubIds),
                'member_id' => $faker->randomElement($memberIds),
                'role' => $faker->randomElement(['admin', 'member']),
                'joined_at' => $faker->dateTimeBetween('-1 year', 'now'),
            ]);
        }
    }
}
