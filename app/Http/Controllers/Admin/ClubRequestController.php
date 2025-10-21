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


    public function handle(Request $request, ClubRequest $clubRequest)
    {
        $action = $request->input('action');

        if ($action === 'approve') {
            $clubRequest->status = 'approved';
            // Có thể tạo CLB mới từ yêu cầu ở đây nếu muốn
        } elseif ($action === 'reject') {
            $clubRequest->status = 'rejected';
        }

        $clubRequest->save();

        return redirect()->route('admin.club-requests.index')
                         ->with('success', 'Đã xử lý yêu cầu thành công!');
    }
}
