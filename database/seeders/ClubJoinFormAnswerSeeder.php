<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ClubJoinFormAnswer;
use App\Models\ClubJoinRequest;
use App\Models\ClubJoinFormQuestion;
use Faker\Factory as Faker;

class ClubJoinFormAnswerSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create('vi_VN');

        // Lấy các requests đã có câu hỏi form
        $requests = ClubJoinRequest::with('club.joinFormQuestions')->get();

        foreach ($requests as $request) {
            $club = $request->club;
            if (!$club) continue;

            $questions = $club->joinFormQuestions()->where('is_active', true)->get();

            if ($questions->isEmpty()) {
                continue;
            }

            foreach ($questions as $question) {
                // Kiểm tra đã có câu trả lời chưa
                $existingAnswer = ClubJoinFormAnswer::where('request_id', $request->id)
                    ->where('question_id', $question->id)
                    ->exists();

                if ($existingAnswer) {
                    continue;
                }

                // Tạo câu trả lời dựa trên loại câu hỏi
                $answer = $this->generateAnswer($question, $faker);

                ClubJoinFormAnswer::create([
                    'request_id' => $request->id,
                    'question_id' => $question->id,
                    'answer' => is_string($answer) ? $answer : null,
                    'answer_json' => is_array($answer) ? $answer : null,
                ]);
            }
        }
    }

    private function generateAnswer($question, $faker)
    {
        return match($question->type) {
            'text' => $faker->sentence(),
            'textarea' => $faker->paragraph(2),
            'select', 'radio' => $faker->randomElement($question->options ?? []),
            'checkbox' => $faker->randomElements($question->options ?? [], $faker->numberBetween(1, min(3, count($question->options ?? [])))),
            'number' => (string)$faker->numberBetween(1, 100),
            'email' => $faker->safeEmail(),
            'phone' => '0' . $faker->numberBetween(300000000, 999999999),
            'date' => $faker->date('Y-m-d'),
            default => $faker->sentence(),
        };
    }
}
