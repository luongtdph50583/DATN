<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Member;
use App\Models\User;

class MemberSeeder extends Seeder
{
    public function run()
    {
        $faker = \Faker\Factory::create();

        $userIds = User::where('role', 'member')->pluck('id')->toArray();

        foreach ($userIds as $userId) {
            Member::create([
                'user_id' => $userId,
                'gender' => $faker->randomElement(['male', 'female', 'other']),
                'date_of_birth' => $faker->date(),
                'address' => $faker->address(),
                'course' => 'K' . $faker->numberBetween(45, 50),
                'major' => $faker->randomElement(['CNTT', 'Kinh tế', 'Luật', 'Marketing']),
                'citizen_id' => $faker->unique()->numerify('############'),
                'issued_date' => $faker->date(),
                'issued_place' => $faker->city(),
                'ethnicity' => $faker->randomElement(['Kinh', 'Tày', 'Nùng', 'Hoa']),
                'phone' => '09' . $faker->numberBetween(10000000, 99999999),
                'status' => $faker->randomElement(['active', 'inactive']),
            ]);
        }
    }
}
