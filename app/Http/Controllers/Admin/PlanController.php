<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClubPlan;
use App\Models\Club;
use App\Models\PlanTask;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PlanController extends Controller
{
    public function index(Request $request)
    {
        $query = ClubPlan::with(['club', 'createdBy', 'approvedBy'])->latest();

        if ($request->filled('club_id')) {
            $query->where('club_id', $request->club_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $plans = $query->paginate(15);
        $clubs = Club::orderBy('name')->get();

        return view('admin.plans.index', compact('plans', 'clubs'));
    }

    public function create()
    {
        $clubs = Club::orderBy('name')->get();
        return view('admin.plans.create', compact('clubs'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'club_id'     => 'required|exists:clubs,id',
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after_or_equal:start_date',
            'budget'      => 'nullable|numeric|min:0',
        ]);

        $validated['created_by'] = Auth::id();
        $validated['status'] = 'pending'; 

        ClubPlan::create($validated);

        return redirect()->route('admin.plans.index')
            ->with('success', 'Kế hoạch đã được tạo và chờ duyệt!');
    }

    public function show(ClubPlan $plan)
    {
        $plan->load(['club', 'createdBy', 'approvedBy', 'tasks.assignedTo']);
        return view('admin.plans.show', compact('plan'));
    }

    public function edit(ClubPlan $plan)
    {
        $clubs = Club::orderBy('name')->get();
        return view('admin.plans.edit', compact('plan', 'clubs'));
    }

    public function update(Request $request, ClubPlan $plan)
    {
        $validated = $request->validate([
            'club_id'     => 'required|exists:clubs,id',
            'title'       => 'required|string|max:255',
            'description' => 'required|string',
            'start_date'  => 'required|date',
            'end_date'    => 'required|date|after_or_equal:start_date',
            'budget'      => 'nullable|numeric|min:0',
            'status'      => 'required|in:draft,pending,approved,rejected,completed,cancelled',
        ]);

        $plan->update($validated);

        return redirect()->route('admin.plans.index')
            ->with('success', 'Cập nhật kế hoạch thành công!');
    }

    public function destroy(ClubPlan $plan)
    {
        $plan->delete();
        return redirect()->route('admin.plans.index')
            ->with('success', 'Xóa kế hoạch thành công!');
    }

    // Phê duyệt
    public function approve(ClubPlan $plan)
    {
        if ($plan->status !== 'pending') {
            return back()->with('error', 'Chỉ có thể duyệt kế hoạch đang chờ!');
        }

        $plan->update([
            'status' => 'approved',
            'approved_by' => Auth::id(),
        ]);

        return redirect()->route('admin.plans.index')
            ->with('success', 'Đã duyệt kế hoạch!');
    }

    // Từ chối
    public function reject(ClubPlan $plan)
    {
        if ($plan->status !== 'pending') {
            return back()->with('error', 'Chỉ có thể từ chối kế hoạch đang chờ!');
        }

        $plan->update(['status' => 'rejected']);

        return redirect()->route('admin.plans.index')
            ->with('success', 'Đã từ chối kế hoạch!');
    }
}