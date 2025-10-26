<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;
use App\Models\User;

class ClubSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create();

        // Lấy danh sách user có vai trò "club_manager"
        $managers = User::where('role', 'club_manager')->pluck('id')->toArray();

        // Nếu chưa có manager nào, tạo tạm 1 admin làm quản lý
        if (empty($managers)) {
            $managers = [User::first()->id ?? 1];
        }

        // Danh sách CLB mẫu
        $clubNames = [
            'CLB Âm nhạc',
            'CLB Bóng rổ',
            'CLB Công nghệ',
            'CLB Tình nguyện',
            'CLB Nhiếp ảnh',
            'CLB Sách & Tri thức',
            'CLB Thiết kế',
        ];

        $clubs = [];

        foreach ($clubNames as $name) {
            $clubs[] = [
                'name'          => $name,
                'description'   => $faker->sentence(10),
                'logo'          => null,
                'field'         => $faker->randomElement(['Thể thao', 'Nghệ thuật', 'Công nghệ', 'Giáo dục']),
                'status'        => $faker->randomElement(['active', 'pending', 'inactive']),
                'manager_id'    => $faker->randomElement($managers),
                'email'         => $faker->unique()->safeEmail,
                'phone'         => $faker->phoneNumber,
                'member_limit'  => $faker->numberBetween(20, 200),
                'created_at'    => now(),
                'updated_at'    => now(),
            ];
        }

        DB::table('clubs')->insert($clubs);
    }
}
