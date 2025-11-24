<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Club;
use App\Models\ClubJoinFormQuestion;
use App\Models\ClubJoinRequest;
use App\Models\ClubJoinFormAnswer;

class JoinClubController extends Controller
{
    /**
     * Hiển thị form đăng ký tham gia CLB
     */
    public function showForm($clubId)
    {
        $club = Club::findOrFail($clubId);

        $questions = ClubJoinFormQuestion::where('club_id', $clubId)
            ->where('is_active', true)
            ->orderBy('order')
            ->get();

        return view('client.pages.member.join_club_form', compact('club', 'questions'));
    }

    /**
     * Xử lý submit form
     */public function submitForm(Request $request, $clubId)
{
    $club = Club::findOrFail($clubId);

    $questions = ClubJoinFormQuestion::where('club_id', $clubId)
        ->where('is_active', true)
        ->get();

    // --- Dynamic validation ---
    $rules = [];
    foreach ($questions as $q) {
        $fieldKey = "questions.{$q->id}";

        if ($q->type === 'checkbox') {
            // Checkbox là array
            $rules[$fieldKey] = $q->is_required ? 'required|array' : 'nullable|array';
            $rules["{$fieldKey}.*"] = $q->validation_rules ?: 'string';
        } else {
            // Text, textarea, select, radio, number, email, phone, date
            $rules[$fieldKey] = $q->is_required ? 'required' : 'nullable';
            if ($q->validation_rules) {
                $rules[$fieldKey] .= '|'.$q->validation_rules;
            }
        }
    }

    // --- Validate request ---
    $validated = $request->validate($rules);

    // --- Tạo request tham gia ---
    $joinRequest = ClubJoinRequest::create([
        'club_id' => $clubId,
        'user_id' => $request->user()->id,
        'status' => 'pending', // pending, approved, rejected
    ]);

    // --- Lưu câu trả lời ---
    $questionsData = $validated['questions'] ?? [];

    foreach ($questions as $q) {
        $answer = $questionsData[$q->id] ?? null;

        if (is_array($answer)) {
            $answerJson = $answer;
            $answerText = null;
        } else {
            $answerJson = null;
            $answerText = $answer;
        }

        ClubJoinFormAnswer::create([
            'request_id' => $joinRequest->id,
            'question_id' => $q->id,
            'answer' => $answerText,
            'answer_json' => $answerJson,
        ]);
    }

    return redirect()->route('formation-request.index', $clubId)
        ->with('success', 'Bạn đã gửi yêu cầu tham gia CLB thành công!');
}

}
