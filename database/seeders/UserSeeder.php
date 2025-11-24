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
                'password' => Hash::make('12345678'), // mật khẩu cố định
                'role' => 'admin',
                'status' => 'active',
                'avatar' => null,
                'remember_token' => Str::random(10),
            ]
        );

        // 2. Tạo thêm 5 Club Managers với tên giả
        for ($i = 0; $i < 5; $i++) {
            User::create([
                'name' => $faker->name(),
                'email' => $faker->unique()->safeEmail(),
             'password' => Hash::make('12345678'),
                'role' => 'member', // sẽ được gán role manager qua ClubMember
                'status' => 'active',
                'avatar' => null,
                'remember_token' => Str::random(10),
            ]);
        }

        // 3. Tạo thêm 30 thành viên (member) ngẫu nhiên
        for ($i = 0; $i < 30; $i++) {
            User::create([
                'name' => $faker->name(),
                'email' => $faker->unique()->safeEmail(),
             'password' => Hash::make('12345678'),
                'role' => 'member',
               'status' => 'active',
                'avatar' => null,
                'remember_token' => Str::random(10),
            ]);
        }
    }

}
