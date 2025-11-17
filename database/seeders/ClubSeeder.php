<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Club;
use App\Models\User;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\Hash;

class ClubSeeder extends Seeder
{
    public function run()
    {
        // $faker = Faker::create('vi_VN');

        // // Lấy danh sách user role = 'member'
        // $members = User::where('role', 'member')->pluck('id')->toArray();

        // // Nếu không có member nào, tạo tạm
        // if (empty($members)) {
        //     $tempMember = User::create([
        //         'name' => 'Member Tạm Thời',
        //         'email' => 'temp.member@club.com',
        //         'password' => Hash::make('password'),
        //         'role' => 'member',
        //         'status' => 'active',
        //     ]);
        //     $members = [$tempMember->id];
        // }

        // $clubNames = [
        //     'Tin Học', 'Tiếng Anh', 'Bóng Đá', 'Khiêu Vũ', 'Tình Nguyện',
        //     'Nghiên Cứu Khoa Học', 'Âm Nhạc', 'Mỹ Thuật', 'Kinh Doanh Trẻ',
        //     'Robot', 'Marketing', 'Truyền Thông', 'Môi Trường', 'Cờ Vua'
        // ];

        // $createdNames = [];

        // for ($i = 0; $i < 10; $i++) {
        //     do {
        //         $baseName = 'CLB ' . $clubNames[array_rand($clubNames)];
        //         $name = $baseName . ($faker->boolean(30) ? ' ' . $faker->numberBetween(1, 99) : '');
        //     } while (in_array($name, $createdNames) || Club::where('name', $name)->exists());

        //     $createdNames[] = $name;
        //     $field = $this->mapFieldFromName($name);

        //     Club::create([
        //         'name' => $name,
        //         'field' => $field,
        //         'description' => $faker->paragraphs(3, true),
        //         'logo' => $faker->imageUrl(300, 300, 'sports', true, 'club'),
        //         'status' => 'active',
        //         'manager_id' => $faker->randomElement($members), // lấy từ member hiện có
        //         'email' => $faker->unique()->safeEmail(),
        //         'phone' => '0' . $faker->numberBetween(300000000, 999999999),
        //         'member_limit' => $faker->numberBetween(15, 150),
        //         'founded_at' => $faker->dateTimeBetween('-5 years', 'now')->format('Y-m-d'),
        //         'location' => $faker->randomElement([
        //             'TP. Hồ Chí Minh', 'Hà Nội', 'Đà Nẵng', 'Cần Thơ', 'Hải Phòng',
        //             'Trường ĐH Bách Khoa', 'Trường ĐH Sư Phạm', 'Ký túc xá Khu A'
        //         ]),
        //         'rules' => $faker->paragraphs(2, true),
        //     ]);
        // }
    }

    // private function mapFieldFromName($name)
    // {
    //     $mapping = [
    //         'Tin Học' => 'Công nghệ',
    //         'Tiếng Anh' => 'Ngoại ngữ',
    //         'Bóng Đá' => 'Thể thao',
    //         'Khiêu Vũ' => 'Nghệ thuật',
    //         'Tình Nguyện' => 'Tình nguyện',
    //         'Nghiên Cứu Khoa Học' => 'Khoa học',
    //         'Âm Nhạc' => 'Nghệ thuật',
    //         'Mỹ Thuật' => 'Nghệ thuật',
    //         'Kinh Doanh Trẻ' => 'Kinh doanh',
    //         'Robot' => 'Công nghệ',
    //         'Marketing' => 'Kinh doanh',
    //         'Truyền Thông' => 'Truyền thông',
    //         'Môi Trường' => 'Môi trường',
    //         'Cờ Vua' => 'Trí tuệ'
    //     ];

    //     foreach ($mapping as $keyword => $field) {
    //         if (strpos($name, $keyword) !== false) {
    //             return $field;
    //         }
    //     }

    //     return 'Khác';
    // }

}
