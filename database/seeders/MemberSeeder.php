<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Member;
use App\Models\User;
use Faker\Factory as Faker;

class MemberSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('vi_VN'); // Dữ liệu tiếng Việt chuẩn

        // Lấy tất cả user có role = 'member' và chưa có hồ sơ member
        $memberUsers = User::where('role', 'member')
            ->whereDoesntHave('member') // Tránh tạo trùng nếu đã có
            ->get();

        foreach ($memberUsers as $user) {
            Member::create([
                'user_id' => $user->id,

                // Thông tin học tập
               'student_code' => 'PH' . $faker->numerify('#####'),
                'course' => 'K' . $faker->numberBetween(45, 65),
                'major' => $faker->randomElement([
                    'Công nghệ Thông tin', 'Kỹ thuật Phần mềm', 'An toàn Thông tin',
                    'Kinh tế Quốc tế', 'Quản trị Kinh doanh', 'Marketing',
                    'Luật Kinh tế', 'Tài chính Ngân hàng', 'Kỹ thuật Điện tử',
                    'Kiến trúc', 'Thiết kế Đồ họa', 'Tâm lý học'
                ]),

                // Thông tin cá nhân
                'gender' => $faker->randomElement(['male', 'female', 'other']),
                'date_of_birth' => $faker->dateTimeBetween('-30 years', '-18 years')->format('Y-m-d'),
                'address' => $faker->address(),
                'phone' => '0' . $faker->randomElement([3,5,7,8,9]) . $faker->numberBetween(10000000, 99999999),

                // CCCD (12 số, hợp lệ Việt Nam)
                'citizen_id' => $faker->unique()->numerify('############'),
                'issued_date' => $faker->dateTimeBetween('-10 years', 'now')->format('Y-m-d'),
                'issued_place' => $faker->randomElement([
                    'CA TP. Hồ Chí Minh', 'CA Hà Nội', 'CA Đà Nẵng',
                    'CA Cần Thơ', 'CA Hải Phòng', 'CA Bình Dương'
                ]),
                'ethnicity' => $faker->randomElement(['Kinh', 'Tày', 'Thái', 'Mường', 'Hoa', 'Khmer', 'Nùng']),

                // Trạng thái
                'status' => $faker->randomElement(['active', 'inactive']),
            ]);
        }

        // Nếu muốn tạo thêm member không gắn user (không khuyến khích), bỏ qua
    }
}