<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;

class UserSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        $users = [];
        $departments = ['CNTT', 'Kinh tế', 'Mỹ thuật', 'Kỹ thuật', 'Ngôn ngữ học'];
        for ($i = 1; $i <= 15; $i++) {
            $users[] = [
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'email_verified_at' => $faker->optional()->dateTimeThisYear(),
                'password' => Hash::make('password'),
                'role' => $i <= 2 ? 'admin' : ($i <= 5 ? 'club_manager' : 'member'),
                'status' => $faker->randomElement(['active', 'inactive']),
                'phone' => $faker->phoneNumber,
                'avatar' => $faker->imageUrl(),
                'student_id' => 'STU' . str_pad($i, 5, '0', STR_PAD_LEFT),
                'department' => $faker->randomElement($departments),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('users')->insert($users);
    }
}