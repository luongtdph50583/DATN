<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ClubJoinFormQuestion;
use App\Models\Club;
use Faker\Factory as Faker;

class ClubJoinFormQuestionSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('vi_VN');

        $clubs = Club::all();

        if ($clubs->isEmpty()) {
            return;
        }

        // Câu hỏi mẫu cho form tuyển thành viên
        $defaultQuestions = [
            [
                'question' => 'Bạn đã từng tham gia CLB nào trước đây chưa?',
                'type' => 'text',
                'is_required' => false,
                'order' => 1,
            ],
            [
                'question' => 'Bạn có kỹ năng gì đặc biệt?',
                'type' => 'textarea',
                'is_required' => true,
                'order' => 2,
            ],
            [
                'question' => 'Bạn có thể dành bao nhiêu giờ/tuần cho CLB?',
                'type' => 'select',
                'options' => ['1-5 giờ', '5-10 giờ', '10-15 giờ', 'Trên 15 giờ'],
                'is_required' => true,
                'order' => 3,
            ],
            [
                'question' => 'Lý do bạn muốn tham gia CLB này?',
                'type' => 'textarea',
                'is_required' => true,
                'order' => 4,
            ],
            [
                'question' => 'Bạn có sẵn sàng tham gia các hoạt động ngoài giờ không?',
                'type' => 'radio',
                'options' => ['Có', 'Không', 'Tùy hoạt động'],
                'is_required' => true,
                'order' => 5,
            ],
            [
                'question' => 'Bạn muốn đóng góp gì cho CLB?',
                'type' => 'textarea',
                'is_required' => false,
                'order' => 6,
            ],
        ];

        foreach ($clubs as $club) {
            // Mỗi CLB có 3-6 câu hỏi ngẫu nhiên
            $numQuestions = $faker->numberBetween(3, 6);
            $selectedQuestions = $faker->randomElements($defaultQuestions, $numQuestions);

            foreach ($selectedQuestions as $index => $q) {
                ClubJoinFormQuestion::create([
                    'club_id' => $club->id,
                    'question' => $q['question'],
                    'description' => $faker->optional(0.3)->sentence(),
                    'type' => $q['type'],
                    'options' => $q['options'] ?? null,
                    'order' => $q['order'] ?? ($index + 1),
                    'is_required' => $q['is_required'] ?? true,
                    'is_active' => true,
                    'validation_rules' => null,
                ]);
            }
        }
    }
}
