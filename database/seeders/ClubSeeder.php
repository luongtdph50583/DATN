<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Club;
use App\Models\User;
use Faker\Factory as Faker;

class ClubSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('vi_VN'); // Tên, địa điểm, mô tả tiếng Việt

        // Lấy danh sách các club_manager (nếu có)
        $clubManagers = User::where('role', 'club_manager')->pluck('id')->toArray();

        // Nếu không có manager nào, tạo 1 cái tạm (tránh lỗi)
        if (empty($clubManagers)) {
            $tempManager = User::create([
                'name' => 'Manager Tạm Thời',
                'email' => 'temp.manager@club.com',
                'password' => \Illuminate\Support\Facades\Hash::make('password'),
                'role' => 'club_manager',
                'status' => 'active',
            ]);
            $clubManagers = [$tempManager->id];
        }

        // Tên CLB thực tế hơn
        $clubNames = [
            'Tin Học', 'Tiếng Anh', 'Bóng Đá', 'Khiêu Vũ', 'Tình Nguyện',
            'Nghiên Cứu Khoa Học', 'Âm Nhạc', 'Mỹ Thuật', 'Kinh Doanh Trẻ',
            'Robot', 'Marketing', 'Truyền Thông', 'Môi Trường', 'Cờ Vua'
        ];

        for ($i = 0; $i < 10; $i++) { // Tăng lên 10 CLB cho đa dạng
            $name = 'CLB ' . $clubNames[array_rand($clubNames)];
            $field = $this->mapFieldFromName($name); // Tự động gán lĩnh vực theo tên

            Club::create([
                'name' => $name,
                'description' => $faker->paragraphs(3, true),
                'logo' => $faker->optional(0.9)->imageUrl(300, 300, 'sports', true, 'club'),
                'field' => $field,
                'status' => $faker->randomElement(['active', 'inactive']),
                'manager_id' => $faker->optional(0.9)->randomElement($clubManagers), // 90% có manager
                'email' => $faker->unique()->safeEmail(),
                'phone' => '0' . $faker->numberBetween(3, 9) . $faker->numberBetween(10000000, 99999999),
                'member_limit' => $faker->numberBetween(15, 150),
             'founded_at' => $faker->dateTimeBetween('-5 years', 'now')->format('Y-m-d'),

                'location' => $faker->randomElement([
                    'TP. Hồ Chí Minh', 'Hà Nội', 'Đà Nẵng', 'Cần Thơ', 'Hải Phòng',
                    'Trường ĐH Bách Khoa', 'Trường ĐH Sư Phạm', 'Ký túc xá Khu A'
                ]),
                'rules' => $faker->optional(0.7)->paragraphs(2, true),
            ]);
        }
    }

    /**
     * Tự động gán lĩnh vực phù hợp theo tên CLB
     */
    private function mapFieldFromName($name)
    {
        $mapping = [
            'Tin Học' => 'Công nghệ',
            'Tiếng Anh' => 'Ngoại ngữ',
            'Bóng Đá' => 'Thể thao',
            'Khiêu Vũ' => 'Nghệ thuật',
            'Tình Nguyện' => 'Tình nguyện',
            'Nghiên Cứu Khoa Học' => 'Khoa học',
            'Âm Nhạc' => 'Nghệ thuật',
            'Mỹ Thuật' => 'Nghệ thuật',
            'Kinh Doanh Trẻ' => 'Kinh doanh',
            'Robot' => 'Công nghệ',
            'Marketing' => 'Kinh doanh',
            'Truyền Thông' => 'Truyền thông',
            'Môi Trường' => 'Môi trường',
            'Cờ Vua' => 'Trí tuệ'
        ];

        foreach ($mapping as $keyword => $field) {
            if (strpos($name, $keyword) !== false) {
                return $field;
            }
        }

        return 'Khác';
    }
}