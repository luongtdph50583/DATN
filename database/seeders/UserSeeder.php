<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('vi_VN');

        // 1. Admin cố định
        User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Admin System',
                'password' => Hash::make('123456'),
                'role' => 'admin',
                'status' => 'active',
                'avatar' => null,
                'remember_token' => Str::random(10),
            ]
        );

        // 2. Member cố định để test
        User::firstOrCreate(
            ['email' => 'member@gmail.com'],
            [
                'name' => 'Member User',
                'password' => Hash::make('123456'),
                'role' => 'member',
                'status' => 'active',
                'avatar' => null,
                'remember_token' => Str::random(10),
            ]
        );

        // 3. Tạo thêm một số Club Managers
        for ($i = 1; $i <= 5; $i++) {
            User::firstOrCreate(
                ['email' => "manager{$i}@gmail.com"],
                [
                    'name' => 'Club Manager ' . $i,
                    'password' => Hash::make('123456'),
                    'role' => 'member', // Sẽ được gán làm manager qua ClubMember
                    'status' => 'active',
                    'avatar' => null,
                    'remember_token' => Str::random(10),
                ]
            );
        }

        // 4. Tạo thêm 30 thành viên (member) ngẫu nhiên
        for ($i = 0; $i < 30; $i++) {
            User::create([
                'name' => $faker->name(),
                'email' => $faker->unique()->safeEmail(),
                'password' => Hash::make('123456'), // Mật khẩu chung cho dễ test
                'role' => 'member',
                'status' => $faker->randomElement(['active', 'inactive']),
                'avatar' => null,
                'remember_token' => Str::random(10),
            ]);
        }
    }
}
