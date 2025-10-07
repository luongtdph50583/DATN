<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class PostSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        $posts = [];
        for ($i = 1; $i <= 15; $i++) {
            $posts[] = [
                'club_id' => $faker->numberBetween(1, 10),
                'user_id' => $faker->numberBetween(3, 15),
                'title' => $faker->sentence(5),
                'content' => $faker->paragraphs(3, true),
                'media_id' => $faker->numberBetween(1, 15),
                'type' => $faker->randomElement(['post', 'notice', 'document']),
                'status' => $faker->randomElement(['visible', 'hidden']),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('posts')->insert($posts);
    }
}