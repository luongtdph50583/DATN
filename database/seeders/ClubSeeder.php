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
        $faker = Faker::create('vi_VN');

        // Lấy danh sách club_manager
        $clubManagers = User::where('role', 'club_manager')->pluck('id')->toArray();

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

        $clubNames = [
            'Tin Học', 'Tiếng Anh', 'Bóng Đá', 'Khiêu Vũ', 'Tình Nguyện',
            'Nghiên Cứu Khoa Học', 'Âm Nhạc', 'Mỹ Thuật', 'Kinh Doanh Trẻ',
            'Robot', 'Marketing', 'Truyền Thông', 'Môi Trường', 'Cờ Vua'
        ];

        $createdNames = []; // mảng lưu tên đã tạo

        for ($i = 0; $i < 10; $i++) {
            do {
                $baseName = 'CLB ' . $clubNames[array_rand($clubNames)];
                // Nếu muốn thêm số để đảm bảo uniqueness
                $name = $baseName . ($faker->boolean(30) ? ' ' . $faker->numberBetween(1, 99) : '');
            } while (in_array($name, $createdNames) || Club::where('name', $name)->exists());

            $createdNames[] = $name;
            $field = $this->mapFieldFromName($name);

            Club::create([
                'name' => $name,
                'description' => $faker->paragraphs(3, true),
                'logo' => $faker->optional(0.9)->imageUrl(300, 300, 'sports', true, 'club'),
                'field' => $field,
                'status' => $faker->randomElement(['active', 'pending', 'inactive']),
                'manager_id' => $faker->optional(0.9)->randomElement($clubManagers),
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
