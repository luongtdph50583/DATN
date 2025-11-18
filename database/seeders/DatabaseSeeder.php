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
            UserSeeder::class,
            MemberSeeder::class,
            ClubSeeder::class,
            ClubMemberSeeder::class,
            ClubJoinFormQuestionSeeder::class,
            ClubJoinRequestSeeder::class,
            ClubInterviewScheduleSeeder::class,
            ClubJoinFormAnswerSeeder::class,
            PostAndCommentSeeder::class,
            // Các seeders khác nếu có
            EventSeeder::class,
            EventRegistrationSeeder::class,
            FundTransactionSeeder::class,
            NotificationSeeder::class,
        ]);

        $this->command->info('✅ Seed dữ liệu hoàn tất!');
        $this->command->info('');
        $this->command->info('📧 Thông tin đăng nhập:');
        $this->command->info('   - Admin: admin@gmail.com / 123456');
        $this->command->info('   - Member: member@gmail.com / 123456');
        $this->command->info('   - Manager 1-5: manager1@gmail.com đến manager5@gmail.com / 123456');
    }
}
