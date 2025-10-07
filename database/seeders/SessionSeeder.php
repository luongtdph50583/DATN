<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class SessionSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        $sessions = [];
        for ($i = 1; $i <= 10; $i++) {
            $sessions[] = [
                'id' => $faker->uuid,
                'user_id' => $faker->numberBetween(1, 15),
                'ip_address' => $faker->ipv4,
                'user_agent' => $faker->userAgent,
                'payload' => base64_encode($faker->text),
                'last_activity' => time() - $faker->numberBetween(0, 3600),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('sessions')->insert($sessions);
    }
}