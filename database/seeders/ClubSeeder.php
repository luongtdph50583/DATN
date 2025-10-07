<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class ClubSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        $clubs = [];
        $fields = ['Âm nhạc', 'Kỹ thuật', 'Thể thao', 'Mỹ thuật', 'Văn học'];
        for ($i = 1; $i <= 10; $i++) {
            $clubs[] = [
                'name' => $faker->company . ' Club',
                'description' => $faker->paragraph,
                'logo' => $faker->imageUrl(),
                'field' => $faker->randomElement($fields),
                'status' => $faker->randomElement(['active', 'pending', 'inactive']),
                'manager_id' => $faker->numberBetween(3, 5), // Gán ngẫu nhiên club_manager
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('clubs')->insert($clubs);
    }
}