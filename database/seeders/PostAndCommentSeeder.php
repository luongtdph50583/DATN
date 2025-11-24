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
        // Lấy danh sách user và club
        $users = User::all();
        $clubs = Club::all();

        if ($users->count() == 0 || $clubs->count() == 0) {
            $this->command->info("Vui lòng seed Users và Clubs trước!");
            return;
        }

        // Nội dung bài viết bằng tiếng Việt
        $postContents = [
            [
                'title' => 'Chào mừng thành viên mới',
                'content' => 'CLB chúng tôi xin chào đón các thành viên mới tham gia và hy vọng các bạn sẽ có những trải nghiệm bổ ích.',
                'type' => 'notice'
            ],
            [
                'title' => 'Lịch tập luyện tuần này',
                'content' => 'Tuần này CLB sẽ tổ chức buổi tập luyện vào thứ Tư và thứ Sáu lúc 18h tại sân trường.',
                'type' => 'post'
            ],
            [
                'title' => 'Hướng dẫn tham gia sự kiện',
                'content' => 'Mọi thành viên vui lòng đọc kỹ hướng dẫn tham gia sự kiện để đảm bảo an toàn và hiệu quả.',
                'type' => 'document'
            ],
            [
                'title' => 'Cuộc thi bóng đá sinh viên',
                'content' => 'CLB sẽ tham gia giải bóng đá sinh viên vào cuối tháng. Mọi người đăng ký tham gia trước ngày 20/12.',
                'type' => 'post'
            ],
            [
                'title' => 'Hội thảo kỹ năng mềm',
                'content' => 'CLB tổ chức hội thảo về kỹ năng mềm và giao tiếp cho sinh viên. Thời gian: 14h Chủ nhật tại phòng A101.',
                'type' => 'notice'
            ]
        ];

        $posts = collect();

        foreach ($postContents as $data) {
            $user = $users->random();
            $club = $clubs->random();

            $post = Post::create([
                'club_id' => $club->id,
                'user_id' => $user->id,
                'title' => $data['title'],
                'content' => $data['content'],
                'type' => $data['type'],
                'is_visible' => true,
                'status' => 'approved',
                'visibility' => 'public',
                'thumbnail' => null,
                'is_featured' => false,
                'approved_by' => $users->random()->id,
                'approved_at' => now(),
                'rejection_reason' => null,
            ]);

            $posts->push($post);
        }

        // Bình luận ngẫu nhiên bằng tiếng Việt
        $commentsContent = [
            'Tôi rất hào hứng với hoạt động này!',
            'Thông tin rất hữu ích, cảm ơn CLB.',
            'Mong CLB sẽ tổ chức thêm nhiều sự kiện như thế này.',
            'Tôi muốn đăng ký tham gia buổi tập.',
            'CLB thật tuyệt vời!',
            'Xin hỏi có thể tham gia không?',
            'Rất mong nhận được hướng dẫn chi tiết hơn.',
            'Hoạt động này rất ý nghĩa.',
            'Tôi đồng ý với nội dung bài viết.',
            'Cảm ơn các bạn đã chia sẻ thông tin.'
        ];

        for ($i = 1; $i <= 20; $i++) {
            $post = $posts->random();
            $user = $users->random();
            $content = $commentsContent[array_rand($commentsContent)];

            // 50% khả năng là reply của comment trước
            $parentComment = null;
            if (Comment::count() > 0 && rand(0,1)) {
                $parentComment = Comment::inRandomOrder()->first();
            }

            Comment::create([
                'post_id' => $post->id,
                'user_id' => $user->id,
                'parent_id' => $parentComment?->id,
                'content' => $content,
                'status' => 'visible',
                'likes_count' => rand(0,10),
            ]);
        }

        $this->command->info("Đã tạo 5 bài viết và 20 bình luận bằng tiếng Việt thành công!");
    }
}
