<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    public function run()
    {
        $faker = \Faker\Factory::create();

        // Tạo 1 admin và 1 club_manager cố định
        User::create([
            'name' => 'Admin System',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('123456'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        User::create([
            'name' => 'Club Manager',
            'email' => 'manager@example.com',
            'password' => Hash::make('123456'),
            'role' => 'club_manager',
            'status' => 'active',
        ]);

        // Fake thêm 20 user member
        for ($i = 0; $i < 20; $i++) {
            User::create([
                'name' => $faker->name(),
                'email' => $faker->unique()->safeEmail(),
                'password' => Hash::make('123456'),
                'role' => 'member',
                'status' => $faker->randomElement(['active', 'inactive']),
                'avatar' => $faker->imageUrl(200, 200, 'people', true),
                'remember_token' => \Str::random(10),
            ]);
        }
    }
}
