<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ClubMember;
use App\Models\Club;
use App\Models\Member;
use Illuminate\Support\Facades\DB;

class ClubMemberSeeder extends Seeder
{
    public function run()
    {
        $faker = \Faker\Factory::create();

        $clubIds = Club::pluck('id')->toArray();
        $memberIds = Member::pluck('id')->toArray();

        if (empty($clubIds) || empty($memberIds)) {
            $this->command->warn('⚠️ Không có dữ liệu trong bảng clubs hoặc members.');
            return;
        }

        // Xóa dữ liệu cũ để tránh trùng (nếu cần)
        DB::table('club_members')->truncate();

        $records = [];

        foreach ($clubIds as $clubId) {
            // 🔹 Mỗi CLB có 1 admin
            $leaderId = $faker->randomElement($memberIds);
            $records[] = [
                'club_id' => $clubId,
                'member_id' => $leaderId,
                'role' => 'admin',
                'joined_at' => $faker->dateTimeBetween('-1 year', 'now'),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // 🔹 Thêm 4–10 thành viên ngẫu nhiên khác (tránh trùng admin)
            $otherMembers = collect($memberIds)
                ->reject(fn($id) => $id === $leaderId)
                ->random(rand(4, 10));

            foreach ($otherMembers as $memberId) {
                $records[] = [
                    'club_id' => $clubId,
                    'member_id' => $memberId,
                    'role' => 'member',
                    'joined_at' => $faker->dateTimeBetween('-1 year', 'now'),
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Chèn dữ liệu
        ClubMember::insert($records);

        $this->command->info('✅ Seed bảng club_members thành công với ' . count($records) . ' bản ghi!');
    }
}
