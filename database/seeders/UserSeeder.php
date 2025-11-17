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
        $faker = Faker::create('vi_VN'); // Dùng locale Việt Nam cho tên hợp lý

        // 1. Admin cố định
        User::create([
            'name' => 'Admin System',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('123456'),
            'role' => 'admin',
            'status' => 'active',
            'avatar' => null,
            'remember_token' => Str::random(10),
        ]);

          User::create([
            'name' => 'Van Tam',
            'email' => 'tamnvph49996@gmail.com',
            'password' => Hash::make('123456'),
            'role' => 'admin',
            'status' => 'active',
            'avatar' => null,
            'remember_token' => Str::random(10),
        ]);

        // 2. Club Manager cố định
       User::create([
            'name' => 'Manager CLB',
            'email' => 'manager@gmail.com',
            'password' => Hash::make('123456'),
            'role' => 'manager',   // ⭐ Manager
            'status' => 'active',
            'avatar' => null,
            'remember_token' => Str::random(10),
        ]);
        // 3. Tạo 20 thành viên (member) ngẫu nhiên
        for ($i = 0; $i < 20; $i++) {
            User::create([
                'name' => $faker->name(),
                'email' => $faker->unique()->safeEmail(),
                'password' => Hash::make('password'), // Mật khẩu chung cho dễ test
                'role' => 'member',
                'status' => $faker->randomElement(['active', 'inactive']),
                'avatar' => $faker->optional(0.8)->imageUrl(200, 200, 'people', true), // 80% có avatar
                'remember_token' => Str::random(10),
            ]);
        }
    }
}