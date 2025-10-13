<?php

     namespace Database\Seeders;

     use Illuminate\Database\Seeder;
     use App\Models\Event;
     use Faker\Factory as Faker;

     class EventSeeder extends Seeder
     {
         public function run()
         {
             $faker = Faker::create('vi_VN'); // Sử dụng locale tiếng Việt
             $clubs = [1, 2, 3]; // Giả sử có 3 CLB với ID 1, 2, 3
             $users = [1, 2, 3, 4, 5]; // Giả sử có 5 người dùng với ID 1 đến 5

             for ($i = 1; $i <= 10; $i++) {
                 Event::create([
                     'club_id' => $clubs[array_rand($clubs)], // Chọn ngẫu nhiên CLB
                     'name' => $faker->sentence(3), // Tên sự kiện ngẫu nhiên
                     'description' => $faker->paragraph(2), // Mô tả ngẫu nhiên
                     'event_date' => $faker->dateTimeBetween('+1 week', '+3 months'), // Ngày trong 1-3 tháng tới
                     'location' => $faker->city . ', ' . $faker->streetAddress, // Địa điểm ngẫu nhiên
                     'status' => $faker->randomElement(['pending', 'approved', 'rejected']), // Trạng thái ngẫu nhiên
                     'created_by' => $users[array_rand($users)], // Chọn ngẫu nhiên người tạo
                 ]);
             }
         }
     }