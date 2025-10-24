<?php
   namespace Database\Seeders;

use App\Models\Member;
use Illuminate\Database\Seeder;

   class DatabaseSeeder extends Seeder
   {
       public function run()
       {
           $this->call([
<<<<<<< HEAD
              MemberSeeder::class,
=======
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
               FundTransactionSeeder::class,
>>>>>>> e19791b35ef93345d7918183ee77950f85bbb094
           ]);
       }
   }