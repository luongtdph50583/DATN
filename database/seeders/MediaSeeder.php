<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class MediaSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();
        $media = [];
        for ($i = 1; $i <= 15; $i++) {
            $related_type = $faker->randomElement(['clubs', 'events', 'posts']);
            $related_id = $related_type === 'clubs' ? $faker->numberBetween(1, 10) :
                         ($related_type === 'events' ? $faker->numberBetween(1, 12) : $faker->numberBetween(1, 15));
            $media[] = [
                'file_name' => $faker->word . '.' . $faker->fileExtension,
                'file_path' => $faker->imageUrl(),
                'file_type' => $faker->randomElement(['image', 'pdf', 'docx', 'xlsx']),
                'related_id' => $related_id,
                'related_type' => $related_type,
                'uploaded_by' => $faker->numberBetween(1, 15),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('media')->insert($media);
    }
}