<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Post;
use App\Models\Comment;
use App\Models\User;
use App\Models\Club;

class PostAndCommentSeeder extends Seeder
{
    public function run()
    {
        // 🔹 Lấy user và club đầu tiên (hoặc tạo nếu chưa có)
        $user = User::first() ?? User::create([
            'name' => 'Admin Test',
            'email' => 'admin@example.com',
            'password' => bcrypt('123456'),
        ]);

        $club = Club::first() ?? Club::create([
            'name' => 'Câu lạc bộ CNTT',
            'description' => 'Câu lạc bộ chia sẻ kiến thức công nghệ thông tin.',
        ]);

        // 📝 Tạo 1 bài viết mẫu
        $post = Post::create([
            'club_id' => $club->id,
            'user_id' => $user->id,
            'title' => 'Buổi sinh hoạt CLB tuần này',
            'content' => 'Tuần này CLB sẽ có buổi sinh hoạt chuyên đề về Laravel và VueJS. Hẹn gặp các bạn vào thứ 7!',
            'type' => 'post',
            'status' => 'visible',
        ]);

        // 💬 Tạo 5 bình luận mẫu cho bài viết
        for ($i = 1; $i <= 5; $i++) {
            Comment::create([
                'post_id' => $post->id,
                'user_id' => $user->id,
                'content' => "Đây là bình luận số $i của thành viên.",
                'status' => 'visible',
                'likes_count' => rand(0, 10),
            ]);
        }

        // 💬 Thêm bình luận trả lời (reply)
        Comment::create([
            'post_id' => $post->id,
            'user_id' => $user->id,
            'parent_id' => null, // trả lời bình luận đầu tiên
            'content' => 'Cảm ơn bạn, mình rất mong buổi sinh hoạt này!',
            'status' => 'visible',
        ]);

        $this->command->info('✅ Đã tạo bài viết và bình luận mẫu thành công!');
    }
}
