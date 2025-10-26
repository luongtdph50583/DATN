<?php
   namespace Database\Seeders;

use App\Models\Member;
use Illuminate\Database\Seeder;

   class DatabaseSeeder extends Seeder
   {
       public function run()
       {
           $this->call([
               UserSeeder::class,
          
           ]);
       }
   }