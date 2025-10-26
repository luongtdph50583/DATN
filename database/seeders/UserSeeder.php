<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        // 1) Tạo tài khoản admin cố định
        DB::table('users')->insert([
            'name'       => 'Admin',
            'email'      => 'admin@gmail.com',
            'password'   => Hash::make('123456'), // mật khẩu: 123456
            'role'       => 'admin',
            'status'     => 'active',
            'avatar'     => null,
            'remember_token' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // 2) Tạo thêm N tài khoản fake để test
        $count = 20; // chỉnh số lượng theo ý bạn
        $roles = ['member', 'club_manager'];

        $users = [];
        for ($i = 0; $i < $count; $i++) {
            $name = $faker->name;
            $email = $faker->unique()->safeEmail;
            $users[] = [
                'name'       => $name,
                'email'      => $email,
                'password'   => Hash::make('password'), // mật khẩu mặc định cho fake users
                'role'       => $roles[array_rand($roles)],
                'status'     => 'active',
                'avatar'     => null,
                'remember_token' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('users')->insert($users);
    }
}