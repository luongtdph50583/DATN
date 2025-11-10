<?php
   namespace Database\Seeders;

use App\Models\Club;
use App\Models\Member;
use App\Models\Post;
use Illuminate\Database\Seeder;

   class DatabaseSeeder extends Seeder
   {
       public function run()
       {
           $this->call([
          PostAndCommentSeeder::class,
           ]);
       }
   }
