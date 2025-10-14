<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Club;
use App\Models\ClubRequest;
use App\Models\ClubJoinRequest;
use App\Models\User;
use App\Http\Requests\ClubFormRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ClubController extends Controller
{
    /**
     * Hiển thị danh sách CLB, yêu cầu thành lập, yêu cầu tham gia.
     */
    public function index()
    {
        $clubs = Club::with('manager')->get();
        $clubRequests = ClubRequest::with('creator')->get();
        $clubJoinRequests = ClubJoinRequest::with(['user', 'club'])->get();
        $students = User::where('role', 'student')->get();

        return view('admin.clubs.index', compact(
            'clubs',
            'clubRequests',
            'clubJoinRequests',
            'students'
        ));
    }

    /**
     * Hiển thị form tạo CLB.
     */
    public function create()
    {
        return view('admin.clubs.create');
    }

    /**
     * Lưu CLB mới.
     */
    public function store(ClubFormRequest $request)
    {
        $data = $request->validated();

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('uploads', 'public');
        }

        Club::create($data);

        return redirect()
            ->route('admin.clubs.index')
            ->with('success', 'Thêm CLB thành công');
    }

    /**
     * Hiển thị form chỉnh sửa CLB.
     */
    public function edit($id)
    {
        $club = Club::findOrFail($id);
        return view('admin.clubs.edit', compact('club'));
    }

    /**
     * Cập nhật thông tin CLB.
     */
    public function update(ClubFormRequest $request, Club $club)
    {
        $data = $request->validated();

        if ($request->hasFile('logo')) {
            // Xoá logo cũ nếu có
            if ($club->logo) {
                Storage::disk('public')->delete($club->logo);
            }

            $data['logo'] = $request->file('logo')->store('uploads', 'public');
        }

        $club->update($data);

        return redirect()
            ->route('admin.clubs.index')
            ->with('success', 'Cập nhật CLB thành công');
    }

    /**
     * Xóa CLB.
     */
    public function destroy(Club $club)
    {
        if ($club->logo) {
            Storage::disk('public')->delete($club->logo);
        }

        $club->delete();

        return redirect()
            ->route('admin.clubs.index')
            ->with('success', 'Xóa CLB thành công');
    }

    /**
     * Gán chủ nhiệm cho CLB.
     */
   // app/Http/Controllers/Admin/ClubController.php

public function showAssignForm(Club $club)
{
    // Lấy danh sách sinh viên
    $students = User::where('role', 'student')->get();

    return view('admin.clubs.assign_manager', compact('club', 'students'));
}

public function assignManager(Request $request, Club $club)
{
    $request->validate([
        'manager_id' => 'required|exists:users,id',
    ]);

    $club->manager_id = $request->manager_id;
    $club->save();

    return redirect()->route('admin.clubs.index')->with('success', 'Đã gán/chỉnh chủ nhiệm cho CLB thành công!');
}


    /**
     * Hiển thị danh sách yêu cầu thành lập CLB.
     */
    public function showRequests()
    {
        $clubRequests = ClubRequest::all();
        return view('admin.club-requests.index', compact('clubRequests'));
    }

    /**
     * Duyệt hoặc từ chối yêu cầu thành lập CLB.
     */
    public function handleRequest(Request $request, ClubRequest $clubRequest)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
        ]);

        $status = $request->action === 'approve' ? 'approved' : 'rejected';
        $clubRequest->update(['status' => $status]);

        if ($status === 'approved') {
            Club::create([
                'name' => $clubRequest->name,
                'description' => $clubRequest->description,
                'field' => $clubRequest->field,
                'status' => 'active',
            ]);
        }

        return redirect()
            ->route('admin.club-requests.index')
            ->with('success', $status === 'approved'
                ? 'Duyệt CLB thành công'
                : 'Từ chối CLB thành công');
    }

    /**
     * Hiển thị danh sách yêu cầu tham gia CLB.
     */
  public function showJoinRequests()
{
    $joinRequests = ClubJoinRequest::with('user', 'club')->get();
    return view('admin.club-join-requests.index', compact('joinRequests'));
}

    /**
     * Duyệt hoặc từ chối yêu cầu tham gia CLB.
     */
    public function handleJoinRequest(Request $request, ClubJoinRequest $clubJoinRequest)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
        ]);

        $status = $request->action === 'approve' ? 'approved' : 'rejected';
        $clubJoinRequest->update(['status' => $status]);

        return redirect()
            ->route('admin.club-join-requests.index')
            ->with('success', $status === 'approved'
                ? 'Duyệt yêu cầu tham gia thành công'
                : 'Từ chối yêu cầu tham gia thành công');
    }
}
