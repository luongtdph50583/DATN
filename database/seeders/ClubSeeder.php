<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Club;
use App\Models\User;

class ClubSeeder extends Seeder
{
    public function run()
    {
        $faker = \Faker\Factory::create();
        $manager = User::where('role', 'club_manager')->first();

        for ($i = 0; $i < 5; $i++) {
            Club::create([
                'name' => 'CLB ' . ucfirst($faker->word()),
                'description' => $faker->paragraph(),
                'logo' => $faker->imageUrl(300, 300, 'sports', true),
                'field' => $faker->randomElement(['Công nghệ', 'Nghệ thuật', 'Kinh doanh', 'Tình nguyện']),
                'status' => $faker->randomElement(['active', 'pending', 'inactive']),
                'manager_id' => $manager->id ?? null,
                'email' => $faker->unique()->safeEmail(),
                'phone' => '09' . $faker->numberBetween(10000000, 99999999),
                'member_limit' => $faker->numberBetween(20, 100),
            ]);
        }
    }
}
