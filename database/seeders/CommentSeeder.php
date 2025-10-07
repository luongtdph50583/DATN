<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class CommentSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        $comments = [];
        for ($i = 1; $i <= 20; $i++) {
            $comments[] = [
                'post_id' => $faker->numberBetween(1, 15),
                'user_id' => $faker->numberBetween(6, 15),
                'content' => $faker->sentence(10),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('comments')->insert($comments);
    }
}