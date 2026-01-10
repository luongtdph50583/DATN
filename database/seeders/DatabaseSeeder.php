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
        $this->command->info('🔵 Bắt đầu seed dữ liệu...');

        // Xóa dữ liệu cũ nếu cần (tùy chọn)
        // $this->command->warn('⚠️ Đang xóa dữ liệu cũ...');
        // DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        // DB::table('users')->truncate();
        // ... các bảng khác
        // DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->call([
           FacultyMemberSeeder::class,
        ]);

        $this->command->info('✅ Seed dữ liệu hoàn tất!');
      
    }
}
