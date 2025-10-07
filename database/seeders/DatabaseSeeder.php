<?php
   namespace Database\Seeders;

   use Illuminate\Database\Seeder;

   class DatabaseSeeder extends Seeder
   {
       public function run()
       {
           $this->call([
               UserSeeder::class,
               ClubSeeder::class,
               ClubMemberSeeder::class,
               MediaSeeder::class, // Chạy trước để tạo dữ liệu cho bảng media
               EventSeeder::class,
               EventRegistrationSeeder::class,
               PostSeeder::class,
               NotificationSeeder::class,
               ClubJoinRequestSeeder::class,
               ClubLeaveRequestSeeder::class,
               CommentSeeder::class,
               SessionSeeder::class,
           ]);
       }
   }