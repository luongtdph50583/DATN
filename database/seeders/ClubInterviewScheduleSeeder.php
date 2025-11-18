<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ClubInterviewSchedule;
use App\Models\ClubJoinRequest;
use App\Models\Club;
use App\Models\User;
use Faker\Factory as Faker;

class ClubInterviewScheduleSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('vi_VN');

        // Lấy các requests có trạng thái liên quan đến phỏng vấn
        $requests = ClubJoinRequest::whereIn('status', ['interview', 'interview_completed', 'scheduling_interview'])
            ->whereNotNull('interview_scheduled_at')
            ->get();

        if ($requests->isEmpty()) {
            // Nếu không có request nào, tạo từ các request pending
            $requests = ClubJoinRequest::where('status', 'pending')
                ->limit(10)
                ->get();
        }

        foreach ($requests as $request) {
            $club = Club::find($request->club_id);
            if (!$club) continue;

            $status = match($request->status) {
                'interview_completed' => $faker->randomElement(['completed', 'no_show']),
                'interview' => 'scheduled',
                'scheduling_interview' => 'scheduled',
                default => 'scheduled'
            };

            ClubInterviewSchedule::create([
                'request_id' => $request->id,
                'club_id' => $request->club_id,
                'interviewer_id' => $request->interviewer_id ?? $club->manager_id ?? null,
                'scheduled_at' => $request->interview_scheduled_at ?? $faker->dateTimeBetween('now', '+1 month'),
                'location' => $request->interview_location ?? $faker->randomElement([
                    'Phòng họp A101', 'Phòng họp B205', 'Sảnh chính',
                    'Ký túc xá Khu A', 'Thư viện tầng 3', 'CLB Phòng'
                ]),
                'status' => $status,
                'note' => $request->interview_note ?? $faker->optional(0.6)->sentence(),
                'interview_result' => $request->interview_note ?? ($status === 'completed' ? $faker->optional(0.7)->paragraph(1) : null),
                'score' => $request->interview_score ?? ($status === 'completed' ? $faker->numberBetween(60, 100) : null),
                'completed_at' => $request->interview_completed_at ?? ($status === 'completed' ? $faker->dateTimeBetween('-1 month', 'now') : null),
            ]);
        }
    }
}
