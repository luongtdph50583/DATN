<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClubRequest;
use App\Models\Club;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClubRequestController extends Controller
{
    // danh sách (paginate)
    public function index()
    {
        $requests = ClubRequest::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('admin.club-requests.index', compact('requests'));
    }

    // show chi tiết (route model binding)
    public function show(ClubRequest $clubRequest)
    {
        $clubRequest->load('user');
        return view('admin.club-requests.show', compact('clubRequest'));
    }

    // cập nhật trạng thái
    public function updateStatus(Request $request, ClubRequest $clubRequest)
    {
        $data = $request->validate([
            'status' => 'required|in:pending,approved,rejected'
        ]);

        $status = $data['status'];

        DB::beginTransaction();
        try {
            $clubRequest->status = $status;
            $clubRequest->save();

            if ($status === 'approved') {
                // kiểm tra trùng tên CLB
                $exists = Club::where('name', $clubRequest->name)->exists();
                if ($exists) {
                    DB::rollBack();
                    return redirect()->back()->with('error', 'Tên CLB đã tồn tại. Vui lòng đổi tên trước khi duyệt.');
                }

                // tạo club mới
                $club = Club::create([
                    'name' => $clubRequest->name,
                    'description' => $clubRequest->description,
                    'logo' => $clubRequest->logo ?? null, // reuse path nếu có
                    'leader_id' => $clubRequest->user_id,
                    'field' => $clubRequest->field ?? null,
                ]);

                // nếu muốn: thêm bản ghi member chủ nhiệm vào bảng membership ở đây

                DB::commit();
                return redirect()->back()->with('success', 'Yêu cầu đã được duyệt và CLB đã được tạo!');
            }

            DB::commit();
            return redirect()->back()->with('success', 'Trạng thái yêu cầu đã được cập nhật!');

        } catch (\Throwable $e) {
            DB::rollBack();
            // Bạn có thể log lỗi: \Log::error($e);
            return redirect()->back()->with('error', 'Có lỗi xảy ra: ' . $e->getMessage());
        }
    }
}
