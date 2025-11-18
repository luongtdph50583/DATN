<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\ClubJoinFormQuestion;
use App\Models\ClubJoinRequest;
use App\Models\ClubRecruitmentForm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RecruitFormController extends Controller
{
    /**
     * Danh sách các form tuyển thành viên
     */
    public function listForms($club_id)
    {
        $club = Club::findOrFail($club_id);
        $this->authorizeClubManager($club);

        $forms = $club->recruitmentForms()->withCount('questions')->get();

        return view('client.pages.club.recruit_forms_list', compact('club', 'forms'));
    }

    /**
     * Tạo form mới
     */
    public function createForm($club_id)
    {
        $club = Club::findOrFail($club_id);
        $this->authorizeClubManager($club);

        return view('client.pages.club.recruit_form_create', compact('club'));
    }

    /**
     * Lưu form mới
     */
    public function storeForm(Request $request, $club_id)
    {
        $club = Club::findOrFail($club_id);
        $this->authorizeClubManager($club);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'is_default' => 'nullable|boolean',
        ]);

        DB::transaction(function () use ($club, $validated) {
            // Nếu đặt làm mặc định, bỏ mặc định của các form khác
            if ($validated['is_default'] ?? false) {
                $club->recruitmentForms()->update(['is_default' => false]);
            }

            $nextOrder = ($club->recruitmentForms()->max('order') ?? 0) + 1;

            $club->recruitmentForms()->create([
                'name' => $validated['name'],
                'description' => $validated['description'] ?? null,
                'is_default' => $validated['is_default'] ?? false,
                'is_active' => true,
                'order' => $nextOrder,
            ]);
        });

        return redirect()->route('club_manager.recruit_forms.list', ['club_id' => $club_id])
            ->with('success', 'Đã tạo form tuyển thành viên mới.');
    }

    /**
     * Hiển thị form tạo câu hỏi tuyển thành viên
     */
    public function create($club_id, $form_id = null)
    {
        $club = Club::findOrFail($club_id);
        $this->authorizeClubManager($club);

        // Nếu không có form_id, lấy form mặc định hoặc form đầu tiên
        if ($form_id) {
            $form = $club->recruitmentForms()->findOrFail($form_id);
        } else {
            $form = $club->defaultRecruitmentForm ?? $club->recruitmentForms()->first();
            if (!$form) {
                // Nếu chưa có form nào, tạo form mặc định
                $form = $club->recruitmentForms()->create([
                    'name' => 'Form tuyển thành viên mặc định',
                    'description' => 'Form tuyển thành viên mặc định của CLB',
                    'is_default' => true,
                    'is_active' => true,
                    'order' => 1,
                ]);
            }
        }

        $questions = $form->questions()->orderBy('order')->get();
        $allForms = $club->recruitmentForms()->get();

        $questionTypes = [
            'short_text' => 'Trả lời ngắn',
            'long_text' => 'Trả lời dài',
            'number' => 'Số',
            'select' => 'Lựa chọn 1 đáp án',
            'checkbox' => 'Nhiều đáp án',
        ];

        return view('client.pages.club.recruit_form', compact('club', 'form', 'questions', 'allForms', 'questionTypes'));
    }

    /**
     * Lưu câu hỏi vào form tuyển thành viên
     */
    public function store(Request $request, $club_id)
    {
        $club = Club::findOrFail($club_id);
        $this->authorizeClubManager($club);

        $validated = $request->validate([
            'form_id' => 'required|exists:club_recruitment_forms,id',
            'question' => 'required|string|max:255',
            'description' => 'nullable|string|max:500',
            'type' => 'required|in:short_text,long_text,number,select,checkbox',
            'options' => 'nullable|string',
            'order' => 'nullable|integer|min:1',
            'is_required' => 'nullable|boolean',
        ]);

        $form = $club->recruitmentForms()->findOrFail($validated['form_id']);

        $options = null;
        if (in_array($validated['type'], ['select', 'checkbox'])) {
            $options = collect(preg_split('/\r\n|\r|\n/', $validated['options'] ?? ''))
                ->filter(fn ($value) => filled(trim($value)))
                ->values()
                ->all();
        }

        $nextOrder = $validated['order']
            ?? (($form->questions()->max('order') ?? 0) + 1);

        $form->questions()->create([
            'club_id' => $club->id,
            'question' => $validated['question'],
            'description' => $validated['description'] ?? null,
            'type' => $validated['type'],
            'options' => $options,
            'order' => $nextOrder,
            'is_required' => (bool) ($validated['is_required'] ?? false),
            'is_active' => true,
        ]);

        return redirect()->route('club_manager.recruit_form.create', ['club_id' => $club_id, 'form_id' => $form->id])
            ->with('success', 'Đã thêm câu hỏi mới cho form tuyển thành viên.');
    }

    /**
     * Hiển thị danh sách yêu cầu tham gia CLB (để xử lý)
     */
    public function index($club_id)
    {
        $club = Club::findOrFail($club_id);
        $this->authorizeClubManager($club);

        // Lấy tất cả yêu cầu tham gia CLB
        $requests = ClubJoinRequest::where('club_id', $club_id)
            ->with(['user.member', 'formAnswers.question'])
            ->orderBy('requested_at', 'desc')
            ->get();

        return view('client.pages.club.recruit_requests', compact('club', 'requests'));
    }

    /**
     * Duyệt yêu cầu tham gia CLB từ form tuyển
     * (Tương tự ClubMemberRequestController::approve)
     */
    public function approve(Request $request, $club_id, $member_id)
    {
        // Logic tương tự ClubMemberRequestController::approve
        // Có thể refactor để dùng chung method
        return app(ClubMemberRequestController::class)->approve($request, $club_id, $member_id);
    }

    /**
     * Từ chối yêu cầu tuyển thành viên.
     */
    public function reject(Request $request, $club_id, $request_id)
    {
        return app(ClubMemberRequestController::class)->reject($request, $club_id, $request_id);
    }

    /**
     * Bật/tắt câu hỏi trong form.
     */
    public function toggle($club_id, $question_id)
    {
        $club = Club::findOrFail($club_id);
        $this->authorizeClubManager($club);

        $question = ClubJoinFormQuestion::where('club_id', $club_id)
            ->where('id', $question_id)
            ->firstOrFail();
        $question->is_active = !$question->is_active;
        $question->save();

        return redirect()->back()->with('success', 'Đã cập nhật trạng thái câu hỏi.');
    }

    /**
     * Xóa form
     */
    public function destroyForm($club_id, $form_id)
    {
        $club = Club::findOrFail($club_id);
        $this->authorizeClubManager($club);

        $form = $club->recruitmentForms()->findOrFail($form_id);
        
        // Không cho xóa nếu form đang là mặc định
        if ($form->is_default) {
            return redirect()->back()->with('error', 'Không thể xóa form mặc định. Vui lòng đặt form khác làm mặc định trước.');
        }

        $form->delete();

        return redirect()->route('club_manager.recruit_forms.list', ['club_id' => $club_id])
            ->with('success', 'Đã xóa form tuyển thành viên.');
    }

    /**
     * Đặt form làm mặc định
     */
    public function setDefault($club_id, $form_id)
    {
        $club = Club::findOrFail($club_id);
        $this->authorizeClubManager($club);

        $form = $club->recruitmentForms()->findOrFail($form_id);

        DB::transaction(function () use ($club, $form) {
            $club->recruitmentForms()->update(['is_default' => false]);
            $form->update(['is_default' => true]);
        });

        return redirect()->back()->with('success', 'Đã đặt form làm mặc định.');
    }

    /**
     * Kiểm tra quyền quản lý CLB
     */
    private function authorizeClubManager($club)
    {
        $user = Auth::user();
        $managedClubs = $user->getManagedClubs();

        if (!$managedClubs->contains('id', $club->id)) {
            abort(403, 'Bạn không có quyền quản lý CLB này.');
        }
    }
}

