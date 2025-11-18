<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Event;
use App\Models\Club;
use App\Models\ClubMember;
use App\Models\User;
use App\Models\EventFundRequest;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class EventSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('vi_VN');

        $clubIds = Club::pluck('id')->toArray();
        $adminIds = User::where('role', 'admin')->pluck('id')->toArray();

        if (empty($clubIds)) {
            throw new \Exception('Không tìm thấy CLB nào. Hãy chạy ClubSeeder trước.');
        }

        $eventNames = [
            'Hội thảo Công nghệ 4.0', 'Giải Bóng Đá Sinh Viên', 'Đêm Nhạc Acoustic',
            'Workshop Lập Trình Web', 'Hội Chợ Sách Cũ', 'Tình Nguyện Mùa Hè Xanh',
            'Cuộc Thi Hùng Biện Tiếng Anh', 'Triển Lãm Mỹ Thuật', 'Hội Thảo Khởi Nghiệp',
            'Đêm Gala Kỷ Niệm Thành Lập', 'Chương Trình Ca Hát Gây Quỹ', 'Lớp Học Kỹ Năng Mềm'
        ];

        for ($i = 0; $i < 15; $i++) {
            $clubId = $faker->randomElement($clubIds);
            $club = Club::find($clubId);

            // Lấy danh sách member của CLB này
            $creatorIds = ClubMember::where('club_id', $clubId)->pluck('member_id')->toArray();

            // Nếu CLB chưa có member, fallback admin
            if (empty($creatorIds)) {
                $creatorIds = $adminIds;
            }

            $createdBy = $faker->randomElement($creatorIds);

            $start = $faker->dateTimeBetween('+1 week', '+2 months');
            $end = (clone $start)->modify('+3 hours');

            $name = $this->generateEventName($club->field, $eventNames, $faker);

            $status = $faker->randomElement(['pending', 'approved', 'rejected']);
            $approvalBy = ($status === 'approved' || $status === 'rejected')
                ? $faker->randomElement($adminIds)
                : null;

            DB::transaction(function () use (
                $faker,
                $clubId,
                $name,
                $start,
                $end,
                $status,
                $approvalBy,
                $createdBy
            ) {
                $budgetEstimated = $faker->numberBetween(500000, 15000000);
                $budgetRequested = $faker->boolean(70)
                    ? $faker->numberBetween(100000, $budgetEstimated)
                    : 0;
                $budgetClub = $budgetEstimated - $budgetRequested;

                $event = Event::create([
                    'club_id' => $clubId,
                    'name' => $name,
                    'description' => $faker->paragraphs(2, true),
                    'start_time' => $start,
                    'end_time' => $end,
                    'location' => $faker->randomElement([
                        'Hội trường A - ĐH Bách Khoa',
                        'Sân vận động trường',
                        'Phòng họp CLB',
                        'Công viên Lê Văn Tám',
                        'Nhà văn hóa Thanh Niên',
                        'Trường THPT Chuyên Lê Hồng Phong'
                    ]),
                    'max_participants' => $faker->numberBetween(20, 300),
                    'is_public' => $faker->boolean(80),
                    'status' => $status,
                    'created_by' => $createdBy,
                    'approval_by' => $approvalBy,
                    'media_id' => null,

                    'budget_estimated' => $budgetEstimated,
                    'budget_requested' => $budgetRequested,
                    'budget_club' => $budgetClub,
                ]);

                // Nếu có ngân sách xin cấp, tạo EventFundRequest
                if ($budgetRequested > 0) {
                    EventFundRequest::create([
                        'event_id' => $event->id,
                        'requested_by' => $event->created_by,
                        'amount_requested' => $budgetRequested,
                        'status' => 'pending_disbursement',
                        'note' => 'Tự động tạo từ Seeder khi có ngân sách xin cấp.',
                    ]);
                }
            });
        }
    }

    private function generateEventName($field, $eventNames, $faker)
    {
        $fieldMap = [
            'Công nghệ' => ['Hội thảo AI', 'Cuộc thi Code', 'Workshop DevOps'],
            'Nghệ thuật' => ['Triển lãm tranh', 'Đêm nhạc hội', 'Lớp học vẽ'],
            'Thể thao' => ['Giải bóng đá', 'Giải cầu lông', 'Ngày hội thể thao'],
            'Tình nguyện' => ['Hiến máu nhân đạo', 'Dọn vệ sinh công viên', 'Trao quà cho trẻ em'],
            'Kinh doanh' => ['Hội thảo khởi nghiệp', 'Cuộc thi ý tưởng kinh doanh', 'Talkshow CEO'],
        ];

        if (isset($fieldMap[$field]) && $faker->boolean(70)) {
            return $faker->randomElement($fieldMap[$field]);
        }

        return $faker->randomElement($eventNames);
    }
}
