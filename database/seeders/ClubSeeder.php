<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Club;
use App\Models\User;

class ClubSeeder extends Seeder
{
    public function run()
    {
        // Lấy danh sách member sẵn có
        $members = User::where('role', 'member')->get();

        if ($members->isEmpty()) {
            $tempMember = User::create([
                'name' => 'Thành viên tạm thời',
                'email' => 'temp.member@club.com',
                'password' => bcrypt('123456'),
                'role' => 'member',
                'status' => 'active',
            ]);
            $members = collect([$tempMember]);
        }

        $clubs = [
            [
                'name' => 'Câu Lạc Bộ Tin Học',
                'slogan' => 'Nâng tầm tri thức công nghệ cho sinh viên',
                'field' => 'Công nghệ',
                'description' => 'CLB Tin Học là nơi trao đổi kiến thức lập trình, công nghệ mới, và tổ chức các cuộc thi về IT cho sinh viên.',
                'logo' => 'club_logos/tinhoc.png',
                'location' => 'TP. Hồ Chí Minh',
                'email' => 'tinhoc@club.edu.vn',
                'phone' => '0901234567',
                'member_limit' => 100,
            ],
            [
                'name' => 'Câu Lạc Bộ Tiếng Anh',
                'slogan' => 'Cùng nhau chinh phục ngoại ngữ',
                'field' => 'Ngoại ngữ',
                'description' => 'CLB Tiếng Anh tạo môi trường thực hành, giao tiếp và học hỏi kỹ năng tiếng Anh cho sinh viên.',
                'logo' => 'club_logos/tienganh.png',
                'location' => 'Hà Nội',
                'email' => 'tienganh@club.edu.vn',
                'phone' => '0912345678',
                'member_limit' => 80,
            ],
            [
                'name' => 'Câu Lạc Bộ Bóng Đá',
                'slogan' => 'Nơi kết nối những trái tim đam mê bóng đá',
                'field' => 'Thể thao',
                'description' => 'CLB Bóng Đá tổ chức các buổi tập luyện, giải đấu nội bộ và tham gia các giải đấu sinh viên.',
                'logo' => 'club_logos/bongda.png',
                'location' => 'Đà Nẵng',
                'email' => 'bongda@club.edu.vn',
                'phone' => '0923456789',
                'member_limit' => 120,
            ],
            [
                'name' => 'Câu Lạc Bộ Âm Nhạc',
                'slogan' => 'Gắn kết đam mê và sáng tạo âm nhạc',
                'field' => 'Nghệ thuật',
                'description' => 'CLB Âm Nhạc phát triển kỹ năng chơi nhạc, hát và tổ chức các buổi biểu diễn nghệ thuật cho sinh viên.',
                'logo' => 'club_logos/amnhac.png',
                'location' => 'Cần Thơ',
                'email' => 'amnhac@club.edu.vn',
                'phone' => '0934567890',
                'member_limit' => 60,
            ],
            [
                'name' => 'Câu Lạc Bộ Tình Nguyện',
                'slogan' => 'Lan tỏa yêu thương, kết nối cộng đồng',
                'field' => 'Tình nguyện',
                'description' => 'CLB Tình Nguyện tổ chức các hoạt động từ thiện, chương trình giúp đỡ cộng đồng và nâng cao tinh thần xã hội cho sinh viên.',
                'logo' => 'club_logos/tinhnguyen.png',
                'location' => 'Hải Phòng',
                'email' => 'tinhnguyen@club.edu.vn',
                'phone' => '0945678901',
                'member_limit' => 50,
            ],
        ];

        foreach ($clubs as $clubData) {
            Club::create([
                'name' => $clubData['name'],
                'slogan' => $clubData['slogan'],
                'field' => $clubData['field'],
                'description' => $clubData['description'],
                'logo' => $clubData['logo'],
                'status' => 'active',
                'manager_id' => $members->random()->id,
                'advisor_id' => null,
                'advisor_status' => 'pending',
                'email' => $clubData['email'],
                'phone' => $clubData['phone'],
                'member_limit' => $clubData['member_limit'],
                'founded_at' => now()->subYears(rand(1, 5))->format('Y-m-d'),
                'location' => $clubData['location'],
                'rules' => 'Thành viên phải tham gia đầy đủ các hoạt động của CLB và tôn trọng nội quy chung.',
            ]);
        }
    }
}
