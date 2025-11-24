<?php

namespace Database\Seeders;

use App\Models\ClubEvent;
use Illuminate\Database\Seeder;

class ClubEventSeeder extends Seeder
{
    public function run()
    {
        // Tạo 50 sự kiện mẫu – HOÀN HẢO!
        ClubEvent::factory(50)->create();
    }
}