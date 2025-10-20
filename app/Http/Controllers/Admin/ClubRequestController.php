<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClubRequest;
use Illuminate\Http\Request;

class ClubRequestController extends Controller
{
    /**
     * Hiển thị danh sách yêu cầu tạo câu lạc bộ
     */
    public function index()
    {
        $requests = ClubRequest::latest()->get();
        return view('admin.club-requests.index', compact('requests'));
    }

    /**
     * Hiển thị chi tiết một yêu cầu cụ thể
     */
    public function show(ClubRequest $clubRequest)
    {
        return view('admin.club_requests.show', compact('clubRequest'));
    }

    /**
     * Xử lý duyệt hoặc từ chối yêu cầu
     */
    public function handle(Request $request, ClubRequest $clubRequest)
    {
        $action = $request->input('action');

        if ($action === 'approve') {
            $clubRequest->status = 'approved';
        } elseif ($action === 'reject') {
            $clubRequest->status = 'rejected';
        }

        $clubRequest->save();

        return redirect()
            ->route('admin.club-requests.index')
            ->with('success', 'Đã cập nhật trạng thái yêu cầu thành công!');
    }
}
