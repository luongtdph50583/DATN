<?php
   namespace Database\Seeders;

use App\Models\Club;
use App\Models\ClubRequest;
use App\Models\Member;
use App\Models\Post;
use Illuminate\Database\Seeder;

   class DatabaseSeeder extends Seeder
   {
       public function run()
       {
           $this->call([
                UserSeeder::class,
                MemberSeeder::class,
                ClubSeeder::class,
                ClubMemberSeeder::class,
          PostAndCommentSeeder::class,
           ]);
       }
   }
