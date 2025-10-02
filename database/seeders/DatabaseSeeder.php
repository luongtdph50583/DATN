<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as Faker;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('vi_VN');

        // 1. Thêm 20 người dùng (4 admin, 6 club_manager, 10 club_member)
        $users = [];
        $roles = [
            array_fill(0, 4, 'admin'),
            array_fill(0, 6, 'club_manager'),
            array_fill(0, 10, 'club_member'),
        ];
        $roles = array_merge(...$roles); // Gộp mảng

        for ($i = 0; $i < 20; $i++) {
            $users[] = [
                'name' => $faker->name,
                'email' => $faker->unique()->safeEmail,
                'password' => Hash::make('password123'),
                'role' => $roles[$i],
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('users')->insert($users);

        // Lấy danh sách ID người dùng
        $userIds = DB::table('users')->pluck('id')->toArray();
        $managerIds = DB::table('users')->where('role', 'club_manager')->pluck('id')->toArray();
        $memberIds = DB::table('users')->where('role', 'club_member')->pluck('id')->toArray();

        // 2. Thêm 15 câu lạc bộ
        $clubs = [];
        $clubFields = [
            'Âm nhạc', 'Công nghệ', 'Thể thao', 'Mỹ thuật', 'Tình nguyện',
            'Văn học', 'Nhảy', 'Kịch', 'Nhiếp ảnh', 'Khoa học',
            'Kinh doanh', 'Môi trường', 'Ẩm thực', 'Du lịch', 'Thời trang'
        ];

        for ($i = 0; $i < 15; $i++) {
            $clubs[] = [
                'name' => 'CLB ' . $faker->randomElement($clubFields),
                'description' => $faker->sentence(10),
                'logo' => 'logos/' . $faker->uuid . '.png',
                'field' => $clubFields[$i],
                'status' => $faker->randomElement(['pending', 'approved', 'rejected']),
                'manager_id' => $faker->randomElement($managerIds),
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('clubs')->insert($clubs);

        // Lấy danh sách ID câu lạc bộ
        $clubIds = DB::table('clubs')->pluck('id')->toArray();

        // 3. Thêm 30 thành viên CLB
        $clubMembers = [];
        foreach ($clubIds as $clubId) {
            $numMembers = $faker->numberBetween(2, 3); // Mỗi CLB có 2-3 thành viên
            $selectedMembers = $faker->randomElements($memberIds, $numMembers);

            foreach ($selectedMembers as $index => $memberId) {
                $clubMembers[] = [
                    'club_id' => $clubId,
                    'user_id' => $memberId,
                    'role' => $index === 0 ? $faker->randomElement(['member', 'co_manager']) : 'member',
                    'status' => $faker->randomElement(['pending', 'approved', 'rejected']),
                    'joined_at' => $faker->randomElement(['approved']) === 'approved' ? now() : null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        DB::table('club_members')->insert($clubMembers);

        // 4. Thêm 25 sự kiện
        $events = [];
        $locations = ['Hội trường A', 'Phòng 101', 'Phòng Lab 1', 'Sân vận động', 'Sảnh chính', 'Phòng 303', 'Phòng tập 1'];
        foreach ($clubIds as $clubId) {
            $numEvents = $faker->numberBetween(1, 3); // Mỗi CLB có 1-3 sự kiện
            for ($i = 0; $i < $numEvents; $i++) {
                $startTime = $faker->dateTimeBetween('2025-10-01', '2025-12-31');
                $endTime = (clone $startTime)->modify('+'.$faker->numberBetween(2, 24).' hours');
                $events[] = [
                    'club_id' => $clubId,
                    'title' => $faker->sentence(4),
                    'description' => $faker->paragraph,
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'location' => $faker->randomElement($locations),
                    'status' => $faker->randomElement(['pending', 'approved', 'rejected']),
                    'created_by' => $faker->randomElement($managerIds),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        DB::table('events')->insert($events);

        // Lấy danh sách ID sự kiện
        $eventIds = DB::table('events')->pluck('id')->toArray();

        // 5. Thêm 40 đăng ký sự kiện
        $eventRegistrations = [];
        foreach ($eventIds as $eventId) {
            $numRegistrations = $faker->numberBetween(1, 3); // Mỗi sự kiện có 1-3 người đăng ký
            $selectedMembers = $faker->randomElements($memberIds, $numRegistrations);

            foreach ($selectedMembers as $memberId) {
                $status = $faker->randomElement(['registered', 'attended', 'cancelled']);
                $eventRegistrations[] = [
                    'event_id' => $eventId,
                    'user_id' => $memberId,
                    'status' => $status,
                    'registered_at' => now(),
                    'attended_at' => $status === 'attended' ? now() : null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        DB::table('event_registrations')->insert($eventRegistrations);

        // 6. Thêm 30 bài viết
        $posts = [];
        $postTypes = ['announcement', 'document', 'post'];
        foreach ($clubIds as $clubId) {
            $numPosts = $faker->numberBetween(2, 3); // Mỗi CLB có 2-3 bài viết
            for ($i = 0; $i < $numPosts; $i++) {
                $type = $faker->randomElement($postTypes);
                $posts[] = [
                    'club_id' => $clubId,
                    'user_id' => $faker->randomElement($managerIds),
                    'title' => $faker->sentence(4),
                    'content' => $type === 'document' ? null : $faker->paragraph,
                    'type' => $type,
                    'file_path' => $type === 'document' ? 'docs/' . $faker->uuid . '.pdf' : null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        DB::table('posts')->insert($posts);

        // Lấy danh sách ID bài viết
        $postIds = DB::table('posts')->pluck('id')->toArray();

        // 7. Thêm 50 bình luận
        $comments = [];
        foreach ($postIds as $postId) {
            $numComments = $faker->numberBetween(1, 3); // Mỗi bài viết có 1-3 bình luận
            $selectedMembers = $faker->randomElements($memberIds, $numComments);

            foreach ($selectedMembers as $memberId) {
                $comments[] = [
                    'post_id' => $postId,
                    'user_id' => $memberId,
                    'content' => $faker->sentence(8),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }
        DB::table('comments')->insert($comments);

        // 8. Thêm 50 thông báo
        $notifications = [];
        $notificationTypes = ['system', 'club'];
        for ($i = 0; $i < 50; $i++) {
            $type = $faker->randomElement($notificationTypes);
            $notifications[] = [
                'user_id' => $faker->randomElement($userIds),
                'title' => $type === 'system' ? 'Thông báo hệ thống' : $faker->sentence(4),
                'content' => $faker->sentence(10),
                'type' => $type,
                'club_id' => $type === 'club' ? $faker->randomElement($clubIds) : null,
                'read_at' => $faker->boolean(30) ? now() : null, // 30% thông báo đã đọc
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }
        DB::table('notifications')->insert($notifications);
    }
}