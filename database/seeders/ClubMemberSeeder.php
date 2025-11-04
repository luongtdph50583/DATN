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
        $members = Member::with('user')->get();

        if ($clubs->isEmpty() || $members->isEmpty()) {
            $this->command->warn('Không có dữ liệu trong bảng clubs hoặc members.');
            return;
        }

        DB::table('club_members')->truncate();

        $records = [];
        $usedPairs = []; // Tránh trùng (club_id, member_id)

        foreach ($clubs as $club) {
            $clubId = $club->id;
            $availableMembers = $members->all(); // array of Member objects

            // 1. Chủ nhiệm (club_manager) - ưu tiên manager của CLB
            $leaderMember = null;
            if ($club->manager) {
                $leaderMember = collect($availableMembers)
                    ->first(fn($m) => $m->user_id == $club->manager->id);
            }

            if (!$leaderMember) {
                $leaderMember = collect($availableMembers)->random();
            }

            $this->addMember(
                $records, $usedPairs, $faker,
                $clubId, $leaderMember->id,
                'club_manager', 'active',
                $faker->dateTimeBetween('-2 years', '-1 year'),
                $faker->dateTimeBetween('-2 years', '-1 year'),
                'Chủ nhiệm CLB từ khi thành lập'
            );

            // Loại leader khỏi danh sách còn lại
            $remainingMembers = collect($availableMembers)
                ->reject(fn($m) => $m->id == $leaderMember->id);

            // 2. Ban quản lý (2-4 người)
            $managementRoles = ['deputy_manager', 'secretary', 'treasurer', 'event_manager', 'communication'];
            $numManagers = min($faker->numberBetween(2, 4), $remainingMembers->count());
            $managers = $numManagers > 0 ? $remainingMembers->random($numManagers) : collect();

            foreach ($managers as $idx => $member) {
                $role = $managementRoles[$idx] ?? 'communication';
                $this->addMember(
                    $records, $usedPairs, $faker,
                    $clubId, $member->id,
                    $role, 'active',
                    $faker->dateTimeBetween('-1 year', 'now'),
                    $faker->dateTimeBetween('-1 year', 'now'),
                    $faker->sentence()
                );
            }

            // Loại managers khỏi danh sách
            $remainingMembers = $remainingMembers->reject(fn($m) => $managers->pluck('id')->contains($m->id));

            // 3. Thành viên thường (5–20 người)
            $numMembers = min($faker->numberBetween(5, 20), $remainingMembers->count());
            $regularMembers = $numMembers > 0 ? $remainingMembers->random($numMembers) : collect();

            foreach ($regularMembers as $member) {
                $status = $faker->randomElement(['active', 'active', 'inactive']); // 66% active
                $this->addMember(
                    $records, $usedPairs, $faker,
                    $clubId, $member->id,
                    'member', $status,
                    $faker->dateTimeBetween('-1 year', 'now'),
                    null,
                    $status === 'inactive' ? 'Nghỉ học / chuyển trường' : null
                );
            }

            // Loại thành viên thường khỏi danh sách
            $remainingMembers = $remainingMembers->reject(fn($m) => $regularMembers->pluck('id')->contains($m->id));

            // 4. Khách mời (0–3 người)
            if ($remainingMembers->isNotEmpty() && $faker->boolean(50)) {
                $numGuests = min($faker->numberBetween(1, 3), $remainingMembers->count());
                $guests = $remainingMembers->random($numGuests);

                foreach ($guests as $member) {
                    $this->addMember(
                        $records, $usedPairs, $faker,
                        $clubId, $member->id,
                        'guest', 'active',
                        $faker->dateTimeBetween('-1 month', 'now'),
                        null,
                        'Khách mời tham gia sự kiện'
                    );
                }
            }
        }

        // Chèn hàng loạt
        if (!empty($records)) {
            ClubMember::insert($records);
        }

        $this->command->info("Seed bảng club_members thành công với " . count($records) . " bản ghi!");
    }

    private function addMember(
        array &$records,
        array &$usedPairs,
        $faker,
        $clubId,
        $memberId,
        $role,
        $status,
        $joinedAt,
        $appointedAt,
        $note
    ) {
        $key = "$clubId-$memberId";
        if (isset($usedPairs[$key])) return;

        $usedPairs[$key] = true;

        $records[] = [
            'club_id' => $clubId,
            'member_id' => $memberId,
            'role' => $role,
            'status' => $status,
            'note' => $note,
            'joined_at' => $joinedAt,
            'appointed_at' => in_array($role, ['club_manager', 'deputy_manager', 'secretary', 'treasurer', 'event_manager', 'communication'])
                ? $appointedAt
                : null,
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }
}
