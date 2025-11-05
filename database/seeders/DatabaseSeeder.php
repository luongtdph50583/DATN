<?php
   namespace Database\Seeders;

use App\Models\Club;
use App\Models\Member;
use Illuminate\Database\Seeder;

   class DatabaseSeeder extends Seeder
   {
       public function run()
       {
           $this->call([
                UserSeeder::class,
                ClubSeeder::class,
                MemberSeeder::class,
              ClubMemberSeeder::class,
               EventSeeder::class,
           ]);
       }
   }
