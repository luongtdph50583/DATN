<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class MemberSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        // Lấy danh sách user có vai trò 'member'
        $users = User::where('role', 'member')->get();

        $members = [];

        foreach ($users as $user) {
            $members[] = [
                'user_id'       => $user->id,
                'gender'        => $faker->randomElement(['male', 'female', 'other']),
                'date_of_birth' => $faker->date(),
                'address'       => $faker->address,
                'course'        => 'K' . $faker->numberBetween(60, 70),
                'major'         => $faker->randomElement(['CNTT', 'Kinh tế', 'Ngôn ngữ Anh', 'Thiết kế']),
                'citizen_id'    => $faker->unique()->numerify('###########'),
                'issued_date'   => $faker->date(),
                'issued_place'  => $faker->city,
                'ethnicity'     => $faker->randomElement(['Kinh', 'Tày', 'Thái']),
                'phone'         => $faker->phoneNumber,
                'status'        => $faker->randomElement(['active', 'inactive']),
                'created_at'    => now(),
                'updated_at'    => now(),
            ];
        }

        DB::table('members')->insert($members);
    }
}
