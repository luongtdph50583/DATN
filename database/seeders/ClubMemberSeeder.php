<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ClubMember;
use App\Models\Club;
use App\Models\Member;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;

class ClubMemberSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('vi_VN');

        $clubs = Club::with('manager')->get();
        $members = Member::all();

        if ($clubs->isEmpty() || $members->isEmpty()) {
            $this->command->warn('Không có dữ liệu trong bảng clubs hoặc members.');
            return;
        }

        // Xóa dữ liệu cũ (nếu cần, có thể bỏ qua để tránh lỗi foreign key)
        // DB::table('club_members')->truncate();

        $records = [];
        $usedLeaderIds = []; // đảm bảo 1 người chỉ làm chủ nhiệm 1 CLB
        $usedClubMemberPairs = []; // Lưu các cặp (club_id, member_id) đã sử dụng

        foreach ($clubs as $club) {
            $clubId = $club->id;

            // 1️⃣ Chọn chủ nhiệm
            $leaderMember = null;

            if ($club->manager_id) {
                $leaderMember = $members
                    ->where('user_id', $club->manager_id)
                    ->whereNotIn('id', $usedLeaderIds)
                    ->first();
            }

            // Nếu chưa có leader, lấy ngẫu nhiên
            if (!$leaderMember) {
                $availableMembers = $members->whereNotIn('id', $usedLeaderIds);
                if ($availableMembers->isEmpty()) {
                    $this->command->warn("Không còn member nào để làm chủ nhiệm cho CLB $clubId");
                    continue;
                }
                $leaderMember = $availableMembers->random();
            }

            // Kiểm tra duplicate (club_id, member_id)
            $pairKey = "$clubId-{$leaderMember->id}";
            if (isset($usedClubMemberPairs[$pairKey])) {
                continue; // Đã tồn tại, bỏ qua
            }
            $usedClubMemberPairs[$pairKey] = true;

            // Đánh dấu member đã làm leader
            $usedLeaderIds[] = $leaderMember->id;

            // Thêm chủ nhiệm vào bảng club_members
            $records[] = [
                'club_id' => $clubId,
                'member_id' => $leaderMember->id,
                'role' => 'club_manager',
                'status' => 'active',
                'note' => 'Chủ nhiệm CLB từ khi thành lập',
                'joined_at' => $faker->dateTimeBetween('-2 years', '-1 year'),
                'appointed_at' => $faker->dateTimeBetween('-2 years', '-1 year'),
                'created_at' => now(),
                'updated_at' => now(),
            ];

            // 2️⃣ Thêm các thành viên còn lại (role = 'member')
            $numMembers = $faker->numberBetween(5, 15);
            $availableMembers = $members->whereNotIn('id', [$leaderMember->id]);
            $regularMembers = $availableMembers->count() > 0
                ? $availableMembers->random(min($numMembers, $availableMembers->count()))
                : collect();

            foreach ($regularMembers as $member) {
                $pairKey = "$clubId-{$member->id}";
                // Kiểm tra duplicate trước khi thêm
                if (isset($usedClubMemberPairs[$pairKey])) {
                    continue; // Đã tồn tại, bỏ qua
                }
                $usedClubMemberPairs[$pairKey] = true;

                $records[] = [
                    'club_id' => $clubId,
                    'member_id' => $member->id,
                    'role' => 'member',
                    'status' => $faker->randomElement(['active', 'active', 'inactive']),
                    'note' => null,
                    'joined_at' => $faker->dateTimeBetween('-1 year', 'now'),
                    'appointed_at' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        // Chèn dữ liệu (kiểm tra lại duplicate trước khi insert)
        if (!empty($records)) {
            // Chỉ insert những record chưa tồn tại
            foreach ($records as $record) {
                ClubMember::firstOrCreate(
                    [
                        'club_id' => $record['club_id'],
                        'member_id' => $record['member_id'],
                    ],
                    $record
                );
            }
        }

        $this->command->info("Seed bảng club_members thành công với " . count($records) . " bản ghi!");
    }
}
