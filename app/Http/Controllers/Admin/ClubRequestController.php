<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClubRequest;
use App\Models\Club;
use App\Models\ClubMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ClubRequestController extends Controller
{
    /**
     * 📋 Danh sách yêu cầu tạo CLB
     */
    public function index(Request $request)
    {
        $query = ClubRequest::with('user');

        // 🔍 Tìm kiếm theo tên CLB
        if ($request->filled('keyword')) {
            $query->where('name', 'like', '%' . $request->keyword . '%');
        }

        $requests = $query->orderByDesc('created_at')->paginate(10);

        return view('admin.club-requests.index', compact('requests'));
    }

    /**
     * 👁 Xem chi tiết yêu cầu
     */
    public function show($id)
    {
        $clubRequest = ClubRequest::with('user')->findOrFail($id);

        // 🔗 Kiểm tra xem CLB đã được tạo hay chưa (theo tên)
        $club = Club::where('name', $clubRequest->name)->first();

        // 👤 Lấy chủ nhiệm nếu CLB đã được tạo
        $manager = $club?->members()->where('role', 'manager')->first();

        // 👥 Giới hạn số lượng thành viên
        $memberLimit = $club?->member_limit ?? $clubRequest->member_limit ?? 50;

        return view('admin.club-requests.show', compact('clubRequest', 'club', 'manager', 'memberLimit'));
    }

    /**
     * ✅ Duyệt yêu cầu tạo CLB
     */
    public function approve($id)
    {
        $clubRequest = ClubRequest::findOrFail($id);

        // ⚠️ Kiểm tra trùng tên CLB
        if (Club::where('name', $clubRequest->name)->exists()) {
            return back()->with('error', 'Tên CLB này đã tồn tại!');
        }

        DB::transaction(function () use ($clubRequest) {
            // 🏗 Tạo CLB mới
            $club = Club::create([
                'name'         => $clubRequest->name,
                'description'  => $clubRequest->description,
                'field'        => $clubRequest->field,
                'email'        => $clubRequest->email,
                'phone'        => $clubRequest->phone,
                'logo'         => $clubRequest->logo,
                'status'       => 'active',
                'manager_id'   => $clubRequest->user_id,
                'member_limit' => $clubRequest->member_limit ?? 50,
            ]);

            // 👤 Gán người gửi làm chủ nhiệm CLB
            ClubMember::create([
                'club_id'   => $club->id,
                'member_id' => $clubRequest->user_id,
                'role'      => 'manager',
                'joined_at' => now(),
            ]);

            // 🔄 Cập nhật trạng thái yêu cầu
            $clubRequest->update(['status' => 'approved']);
        });

        return redirect()
            ->route('admin.club-requests.index')
            ->with('success', '✅ Đã duyệt yêu cầu, tạo CLB và gán người gửi làm chủ nhiệm!');
    }

    /**
     * ❌ Từ chối yêu cầu
     */
    public function reject($id)
    {
        $clubRequest = ClubRequest::findOrFail($id);

        $clubRequest->update(['status' => 'rejected']);

        return redirect()
            ->route('admin.club-requests.index')
            ->with('success', '❌ Đã từ chối yêu cầu tạo CLB!');
    }

    /**
     * 🗑 Xóa yêu cầu
     */
    public function destroy($id)
    {
        $clubRequest = ClubRequest::findOrFail($id);

        // 🧹 Xóa logo nếu có
        if ($clubRequest->logo && Storage::exists('public/' . $clubRequest->logo)) {
            Storage::delete('public/' . $clubRequest->logo);
        }

        $clubRequest->delete();

        return redirect()
            ->route('admin.club-requests.index')
            ->with('success', '🗑 Đã xóa yêu cầu thành công!');
    }
}
