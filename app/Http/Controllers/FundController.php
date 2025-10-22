<?php

namespace App\Http\Controllers;

use App\Models\FundTransaction;
use App\Models\Club;
use App\Http\Requests\FundTransactionRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FundController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = FundTransaction::with(['club', 'creator', 'approver']);

        // Filter by club if user is club manager
        if (Auth::user()->role === 'club_manager') {
            $query->whereHas('club', function($q) {
                $q->where('manager_id', Auth::id());
            });
        }

        // Apply filters
        if ($request->filled('club_id')) {
            $query->where('club_id', $request->club_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transactions = $query->orderBy('created_at', 'desc')->paginate(15);
        $clubs = Club::all();

        return view('admin.funds.index', compact('transactions', 'clubs'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $clubs = Club::all();
        return view('admin.funds.create', compact('clubs'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(FundTransactionRequest $request)
    {
        $data = $request->validated();
        $data['created_by'] = Auth::id();
        
        // Set status based on user role
        if (Auth::user()->role === 'admin') {
            $data['status'] = 'approved';
            $data['approved_by'] = Auth::id();
        }

        FundTransaction::create($data);

        return redirect()->route(Auth::user()->role === 'admin' ? 'admin.funds.index' : 'club-manager.funds.index')
            ->with('success', 'Giao dịch quỹ đã được tạo thành công.');
    }

    /**
     * Display the specified resource.
     */
    public function show(FundTransaction $fund)
    {
        $fund->load(['club', 'creator', 'approver']);
        return view('admin.funds.show', compact('fund'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(FundTransaction $fund)
    {
        $clubs = Club::all();
        return view('admin.funds.edit', compact('fund', 'clubs'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(FundTransactionRequest $request, FundTransaction $fund)
    {
        $data = $request->validated();
        
        // Only admin or creator can update
        if (Auth::user()->role !== 'admin' && $fund->created_by !== Auth::id()) {
            return redirect()->back()->with('error', 'Bạn không có quyền chỉnh sửa giao dịch này.');
        }

        $fund->update($data);

        return redirect()->route(Auth::user()->role === 'admin' ? 'admin.funds.index' : 'club-manager.funds.index')
            ->with('success', 'Giao dịch quỹ đã được cập nhật thành công.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(FundTransaction $fund)
    {
        // Only admin can delete
        if (Auth::user()->role !== 'admin') {
            return redirect()->back()->with('error', 'Bạn không có quyền xóa giao dịch này.');
        }

        $fund->delete();

        return redirect()->route(Auth::user()->role === 'admin' ? 'admin.funds.index' : 'club-manager.funds.index')
            ->with('success', 'Giao dịch quỹ đã được xóa thành công.');
    }

    /**
     * Approve a fund transaction
     */
    public function approve(FundTransaction $fund)
    {
        if (Auth::user()->role !== 'admin') {
            return redirect()->back()->with('error', 'Bạn không có quyền phê duyệt giao dịch này.');
        }

        $fund->update([
            'status' => 'approved',
            'approved_by' => Auth::id()
        ]);

        return redirect()->back()
            ->with('success', 'Giao dịch quỹ đã được phê duyệt.');
    }

    /**
     * Reject a fund transaction
     */
    public function reject(FundTransaction $fund)
    {
        if (Auth::user()->role !== 'admin') {
            return redirect()->back()->with('error', 'Bạn không có quyền từ chối giao dịch này.');
        }

        $fund->update([
            'status' => 'rejected',
            'approved_by' => Auth::id()
        ]);

        return redirect()->back()
            ->with('success', 'Giao dịch quỹ đã bị từ chối.');
    }

    /**
     * Get fund summary for a club
     */
    public function summary(Request $request)
    {
        $query = FundTransaction::query();

        if ($request->filled('club_id')) {
            $query->where('club_id', $request->club_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        $transactions = $query->get();

        $totalIncome = $transactions->where('type', 'income')->sum('amount');
        $totalExpense = $transactions->where('type', 'expense')->sum('amount');
        $balance = $totalIncome - $totalExpense;

        return response()->json([
            'total_income' => $totalIncome,
            'total_expense' => $totalExpense,
            'balance' => $balance,
            'formatted_income' => number_format($totalIncome, 0, ',', '.') . ' VND',
            'formatted_expense' => number_format($totalExpense, 0, ',', '.') . ' VND',
            'formatted_balance' => number_format($balance, 0, ',', '.') . ' VND',
        ]);
    }
}
