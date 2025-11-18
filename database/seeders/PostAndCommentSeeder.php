<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Post;
use App\Models\Comment;
use App\Models\User;
use App\Models\Club;
use Illuminate\Support\Str;

class PostAndCommentSeeder extends Seeder
{
    public function run()
    {
        $faker = \Faker\Factory::create();

        // Lấy danh sách user và club
        $users = User::all();
        $clubs = Club::all();

        if ($users->count() == 0 || $clubs->count() == 0) {
            $this->command->info("Vui lòng seed Users và Clubs trước!");
            return;
        }

        // Tạo 5 bài viết
        $posts = collect();
        for ($i = 1; $i <= 5; $i++) {
            $user = $users->random();
            $club = $clubs->random();

            $post = Post::create([
                'club_id' => $club->id,
                'user_id' => $user->id,
                'title' => $faker->sentence,
                'content' => $faker->paragraphs(3, true),
                'type' => $faker->randomElement(['post','notice','document']),
                'is_visible' => true,
                'status' => 'approved',
                'visibility' => 'public',
                'thumbnail' => null,
                'is_featured' => $faker->boolean(30),
                'approved_by' => $users->random()->id,
                'approved_at' => now(),
                'rejection_reason' => null,
            ]);

            $posts->push($post);
        }

        // Tạo 20 bình luận ngẫu nhiên
        for ($i = 1; $i <= 20; $i++) {
            $post = $posts->random();
            $user = $users->random();

            // 50% khả năng là reply của 1 comment trước
            $parentComment = null;
            if (Comment::count() > 0 && rand(0,1)) {
                $parentComment = Comment::inRandomOrder()->first();
            }

            Comment::create([
                'post_id' => $post->id,
                'user_id' => $user->id,
                'parent_id' => $parentComment?->id,
                'content' => $faker->sentence,
                'status' => 'visible',
                'likes_count' => rand(0,10),
            ]);
        }

        $this->command->info("Đã tạo 5 bài viết và 20 bình luận thành công!");
    }
}
