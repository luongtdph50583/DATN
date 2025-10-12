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
  public function index()
{
    $clubs = Club::with('manager')->get();
    $clubRequests = ClubRequest::all();
    $clubJoinRequests = ClubJoinRequest::with('user', 'club')->get();
    $users = User::all();
    return view('admin.clubs.index', compact('clubs', 'clubRequests', 'clubJoinRequests', 'users'));
}

    public function create()
    {
        return view('admin.clubs.create');
    }

    public function store(ClubFormRequest $request)
    {
        $data = $request->validated();
        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('uploads', 'public');
        }
        Club::create($data);
        return redirect()->route('admin.clubs.index')->with('success', 'Thêm CLB thành công');
    }

    public function update(ClubFormRequest $request, Club $club)
    {
        $data = $request->validated();
        if ($request->hasFile('logo')) {
            if ($club->logo) {
                Storage::disk('public')->delete($club->logo);
            }
            $data['logo'] = $request->file('logo')->store('uploads', 'public');
        }
        $club->update($data);
        return redirect()->route('admin.clubs.index')->with('success', 'Cập nhật CLB thành công');
    }

    public function destroy(Club $club)
    {
        if ($club->logo) {
            Storage::disk('public')->delete($club->logo);
        }
        $club->delete();
        return redirect()->route('admin.clubs.index')->with('success', 'Xóa CLB thành công');
    }

    public function assignManager(Request $request, Club $club)
    {
        $request->validate([
            'manager_id' => 'required|exists:users,id',
        ], [
            'manager_id.required' => 'Vui lòng chọn chủ nhiệm.',
            'manager_id.exists' => 'Chủ nhiệm không tồn tại.',
        ]);
        $club->update(['manager_id' => $request->manager_id]);
        return redirect()->route('admin.clubs.index')->with('success', 'Gán chủ nhiệm thành công');
    }

    public function showRequests()
    {
        $clubRequests = ClubRequest::all();
        return view('admin.club-requests.index', compact('clubRequests'));
    }

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

        return redirect()->route('admin.club-requests.index')->with('success', $status === 'approved' ? 'Duyệt CLB thành công' : 'Từ chối CLB thành công');
    }

    public function showJoinRequests()
    {
        $clubJoinRequests = ClubJoinRequest::with('user', 'club')->get();
        return view('admin.club-join-requests.index', compact('clubJoinRequests'));
    }

    public function handleJoinRequest(Request $request, ClubJoinRequest $clubJoinRequest)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
        ]);
        $status = $request->action === 'approve' ? 'approved' : 'rejected';
        $clubJoinRequest->update(['status' => $status]);
        return redirect()->route('admin.club-join-requests.index')->with('success', $status === 'approved' ? 'Duyệt yêu cầu tham gia thành công' : 'Từ chối yêu cầu tham gia thành công');
    }
}