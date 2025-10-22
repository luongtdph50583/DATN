<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClubRequest;
use Illuminate\Http\Request;

class ClubRequestController extends Controller
{
    public function index(Request $request)
{
    $query = ClubRequest::with('user');

    // 🔍 Lọc theo tên CLB hoặc người gửi
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where('name', 'like', "%{$search}%")
              ->orWhereHas('user', function ($q) use ($search) {
                  $q->where('name', 'like', "%{$search}%");
              });
    }

    // 🔖 Lọc theo trạng thái
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    // 📚 Lọc theo lĩnh vực
    if ($request->filled('field')) {
        $query->where('field', $request->field);
    }

    $requests = $query->latest()->paginate(10);

    return view('admin.club-requests.index', compact('requests'));
}
public function show(ClubRequest $clubRequest)
{
    return view('admin.club-requests.show', compact('clubRequest'));
}


 public function handle(Request $request, \App\Models\ClubRequest $clubRequest)
{
    $action = $request->input('action');

    if ($action === 'approve') {
        // ✅ 1. Cập nhật trạng thái yêu cầu
        $clubRequest->update(['status' => 'approved']);

        // ✅ 2. Tạo CLB mới từ yêu cầu
        \App\Models\Club::create([
            'name'        => $clubRequest->name,
            'description' => $clubRequest->description,
            'field'       => $clubRequest->field,
            'logo'        => $clubRequest->logo ?? null,
            'leader_id'   => $clubRequest->user_id,
            'status'      => 'active', // hoặc 'pending' nếu bạn muốn duyệt 2 bước
        ]);

        // (Tùy chọn) Xóa yêu cầu sau khi tạo CLB
        // $clubRequest->delete();

    } elseif ($action === 'reject') {
        $clubRequest->update(['status' => 'rejected']);
    }

    return redirect()
        ->route('admin.club-requests.index')
        ->with('success', 'Đã xử lý yêu cầu thành công!');
}

}
