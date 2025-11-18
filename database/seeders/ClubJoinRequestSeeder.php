<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ClubJoinRequest;
use App\Models\Club;
use App\Models\User;
use App\Models\Member;
use Faker\Factory as Faker;

class ClubJoinRequestSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('vi_VN');
        
        $clubs = Club::all();
        $users = User::where('role', 'member')->get();

        if ($clubs->isEmpty() || $users->isEmpty()) {
            return;
        }

        $statuses = ['pending', 'scheduling_interview', 'interview', 'interview_completed', 'approved', 'rejected', 'cancelled'];
        
        // Tạo 20 yêu cầu tham gia CLB với các trạng thái khác nhau
        for ($i = 0; $i < 20; $i++) {
            $club = $clubs->random();
            $user = $users->random();
            $status = $faker->randomElement($statuses);
            
            // Đảm bảo user chưa là thành viên của club này
            $existingMember = \App\Models\ClubMember::where('club_id', $club->id)
                ->whereHas('member', function($q) use ($user) {
                    $q->where('user_id', $user->id);
                })
                ->exists();

            if ($existingMember) {
                continue;
            }

            $requestedAt = $faker->dateTimeBetween('-3 months', 'now');
            
            // Tạo request với thông tin phỏng vấn nếu status liên quan đến interview
            $data = [
                'club_id' => $club->id,
                'user_id' => $user->id,
                'status' => $status,
                'note' => $faker->optional(0.8)->paragraph(2) ?? $faker->optional(0.5)->sentence(),
                'handled_by' => $faker->optional(0.6)->randomElement([$club->manager_id]),
                'requested_at' => $requestedAt,
                'handled_at' => null,
                'interview_scheduled_at' => null,
                'interviewer_id' => null,
                'interview_location' => null,
                'interview_note' => null,
                'interview_result' => null,
                'interview_score' => null,
                'interview_completed_at' => null,
            ];

            // Nếu status là interview hoặc interview_completed, thêm thông tin phỏng vấn
            if (in_array($status, ['interview', 'interview_completed'])) {
                $data['interview_scheduled_at'] = $faker->dateTimeBetween($requestedAt, '+1 month');
                $data['interviewer_id'] = $club->manager_id ?? $users->random()->id;
                $data['interview_location'] = $faker->randomElement([
                    'Phòng họp A101', 'Phòng họp B205', 'Sảnh chính',
                    'Ký túc xá Khu A', 'Thư viện tầng 3', 'CLB Phòng'
                ]);
                $data['interview_result'] = $status === 'interview_completed' 
                    ? $faker->randomElement(['completed', 'no_show']) 
                    : 'pending';
                $data['interview_score'] = $status === 'interview_completed' 
                    ? $faker->numberBetween(60, 100) 
                    : null;
                if ($status === 'interview_completed' && $data['interview_scheduled_at']) {
                    $scheduledAt = is_string($data['interview_scheduled_at']) 
                        ? new \DateTime($data['interview_scheduled_at'])
                        : $data['interview_scheduled_at'];
                    $now = new \DateTime();
                    if ($scheduledAt < $now) {
                        $data['interview_completed_at'] = $faker->dateTimeBetween($scheduledAt, 'now');
                    } else {
                        $data['interview_completed_at'] = $faker->dateTimeBetween('-1 week', 'now');
                    }
                } else {
                    $data['interview_completed_at'] = null;
                }
                $data['interview_note'] = $faker->optional(0.7)->paragraph(1);
            }

            // Nếu đã xử lý, set handled_at
            if (in_array($status, ['approved', 'rejected', 'cancelled', 'interview_completed'])) {
                $data['handled_at'] = $faker->dateTimeBetween($requestedAt, 'now');
            }

            ClubJoinRequest::create($data);
        }
    }
}
