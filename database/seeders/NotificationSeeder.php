<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class NotificationSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        $notifications = [];
        for ($i = 1; $i <= 10; $i++) {
            $notifications[] = [
                'title' => $faker->sentence(4),
                'content' => $faker->paragraph,
                'sent_to' => $faker->randomElement(['all', 'club_' . $faker->numberBetween(1, 10), 'user_' . $faker->numberBetween(1, 15)]),
                'created_by' => $faker->numberBetween(1, 2), // Admin
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('notifications')->insert($notifications);
    }
}