<?php
   namespace Database\Seeders;

use App\Models\Member;
use Illuminate\Database\Seeder;

   class DatabaseSeeder extends Seeder
   {
       public function run()
       {
           $this->call([
              MemberSeeder::class,
               UserSeeder::class,
               ClubSeeder::class,
               ClubMemberSeeder::class,
               MediaSeeder::class, 
               EventSeeder::class,
               EventRegistrationSeeder::class,
               PostSeeder::class,
               NotificationSeeder::class,
               ClubJoinRequestSeeder::class,
               ClubLeaveRequestSeeder::class,
               CommentSeeder::class,
               SessionSeeder::class,
               FundTransactionSeeder::class,
           ]);
       }
   }
