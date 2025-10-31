<?php

namespace App\Http\Controllers;

use App\Models\FundTransaction;
use App\Models\Club;
use App\Http\Requests\FundTransactionRequest;
use App\Models\Event;
use App\Models\Fund;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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

    // Nếu có sự kiện, lưu event_id
    if ($request->filled('event_id')) {
        $data['event_id'] = $request->input('event_id');
    }

    // Xử lý upload nhiều file
    $receipts = [];
    if ($request->hasFile('receipt')) {
        foreach ($request->file('receipt') as $file) {
            $path = $file->store('receipts', 'public');
            $receipts[] = $path;
        }
    }
    $data['receipt'] = !empty($receipts) ? json_encode($receipts) : null;

    // Xác định trạng thái duyệt
    if (Auth::user()->role === 'admin') {
        $data['status'] = 'approved';
        $data['approved_by'] = Auth::id();
    } else {
        $data['status'] = 'pending';
    }

    // Lấy quỹ CLB
    $fund = Fund::where('club_id', $request->club_id)->firstOrFail();
    $data['fund_id'] = $fund->id;

    // Tạo giao dịch
    $transaction = FundTransaction::create($data);

    // Cập nhật ngân sách sự kiện hoặc quỹ CLB
DB::beginTransaction();
try {
    if (stripos($data['category'], 'hoạt động sự kiện') !== false && !empty($data['event_id'])) {
        $event = Event::find($data['event_id']);
        if ($event) {
            $event->budget_current += ($data['type'] === 'income') ? $data['amount'] : -$data['amount'];
            if ($event->budget_current < 0) $event->budget_current = 0;
            $event->save();
        }
    } else {
        $fund->balance += ($data['type'] === 'income') ? $data['amount'] : -$data['amount'];
        if ($fund->balance < 0) $fund->balance = 0;
        $fund->save();
    }

    DB::commit();
} catch (\Exception $e) {
    DB::rollBack();
    return redirect()->back()->with('error', 'Lỗi: ' . $e->getMessage());
}


    return redirect()->route(
        Auth::user()->role === 'admin'
            ? 'admin.funds.index'
            : 'club-manager.funds.index'
    )->with('success', 'Giao dịch quỹ đã được tạo thành công.');
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
          $events = Event::where('club_id', $fund->club_id)->get(); // lọc theo CLB nếu cần
        return view('admin.funds.edit', compact('fund', 'clubs', 'events'));
    }

    /**
     * Update the specified resource in storage.
     */
public function update(FundTransactionRequest $request, FundTransaction $fund)
{
    $data = $request->validated();

    if (Auth::user()->role !== 'admin' && $fund->created_by !== Auth::id()) {
        return redirect()->back()->with('error', 'Bạn không có quyền chỉnh sửa giao dịch này.');
    }

    DB::beginTransaction();
    try {
        // 🔹 1. Lưu thông tin cũ
        $oldType = $fund->type;
        $oldAmount = $fund->amount;
        $oldCategory = $fund->category;
        $oldEventId = $fund->event_id;
        $oldClubFund = Fund::where('club_id', $fund->club_id)->first();

    // 🔹 2. Xử lý file đính kèm
$oldReceipts = is_array(json_decode($fund->receipt, true)) ? json_decode($fund->receipt, true) : ($fund->receipt ? [$fund->receipt] : []);

// === XÓA FILE CŨ ===
$keepReceipts = $request->input('keep_receipts', []);
$deleteReceipts = array_diff($oldReceipts, $keepReceipts);

foreach ($deleteReceipts as $fileToDelete) {
    if (Storage::disk('public')->exists($fileToDelete)) {
        Storage::disk('public')->delete($fileToDelete);
    }
}

// Giữ lại file còn lại
$receipts = $keepReceipts;

// === UPLOAD FILE MỚI ===
if ($request->hasFile('receipt')) {
    foreach ($request->file('receipt') as $file) {
        $path = $file->store('receipts', 'public');
        $receipts[] = $path;
    }
}

// Cập nhật DB
$data['receipt'] = !empty($receipts) ? json_encode($receipts) : null;
    

        // 🔹 3. Hoàn tác tác động cũ (không cap về 0)
        if ($oldCategory === 'Hoạt động sự kiện' && $oldEventId) {
            $oldEvent = Event::find($oldEventId);
            if ($oldEvent) {
                $oldEvent->budget_current -= ($oldType === 'income') ? $oldAmount : -$oldAmount;
                $oldEvent->save();
            }
        } elseif ($oldClubFund) {
            $oldClubFund->balance -= ($oldType === 'income') ? $oldAmount : -$oldAmount;
            $oldClubFund->save();
        }

        // 🔹 3.5. Validation chống overspend (sau hoàn tác, trước update)
        $newType = $data['type'];
        $newAmount = $data['amount'];
        $newCategory = $data['category'];
       // Nếu category là hoạt động sự kiện → gán event_id mới từ request
if ($newCategory === 'Hoạt động sự kiện' && $request->filled('event_id')) {
    $data['event_id'] = $request->input('event_id');
    $newEventId = $data['event_id'];
} else {
    $data['event_id'] = null;
    $newEventId = null;
}

        // Reload để lấy giá trị sau hoàn tác
        $clubFund = Fund::where('club_id', $fund->club_id)->firstOrFail();

        $targetCurrent = 0;
        $targetEntity = null;
        if ($newCategory === 'Hoạt động sự kiện' && $newEventId) {
            $event = Event::findOrFail($newEventId);
            $targetCurrent = $event->budget_current;
            $targetEntity = 'ngân sách sự kiện';
        } else {
            $targetCurrent = $clubFund->balance;
            $targetEntity = 'quỹ câu lạc bộ';
        }

        if ($newType !== 'income' && $newAmount > $targetCurrent) {
            throw new \Exception("Không đủ số dư {$targetEntity} để thực hiện giao dịch chi này (số dư hiện tại: {$targetCurrent}).");
        }

        // 🔹 4. Cập nhật dữ liệu mới
        // Unset event_id nếu không phải category event (tránh lưu thừa)
        if ($newCategory !== 'Hoạt động sự kiện') {
            $data['event_id'] = null;
        }
        $fund->update($data);

        // 🔹 5. Áp dụng tác động mới (không cap về 0)
        if ($newCategory === 'Hoạt động sự kiện' && $newEventId) {
            $event = Event::findOrFail($newEventId);  // Reload nếu cần
            $event->budget_current += ($newType === 'income') ? $newAmount : -$newAmount;
            $event->save();
        } elseif ($clubFund) {
            $clubFund->balance += ($newType === 'income') ? $newAmount : -$newAmount;
            $clubFund->save();
        }

        DB::commit();
        return redirect()->route(
            Auth::user()->role === 'admin' ? 'admin.funds.index' : 'club-manager.funds.index'
        )->with('success', 'Giao dịch quỹ đã được cập nhật thành công.');
    } catch (\Exception $e) {
        DB::rollBack();
        return redirect()->back()->with('error', 'Đã xảy ra lỗi: ' . $e->getMessage());
    }
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
    $clubId = $request->input('club_id');

    // Lấy dữ liệu tổng từ bảng fund_transactions
    $query = \App\Models\FundTransaction::query();

    if ($clubId) {
        $query->where('club_id', $clubId);
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

    // Tổng thu và chi
    $totalIncome = (clone $query)->where('type', 'income')->sum('amount');
    $totalExpense = (clone $query)->where('type', 'expense')->sum('amount');

    // Lấy số dư hiện tại từ bảng funds
    $fundBalance = 0;
    if ($clubId) {
        $fund = \App\Models\Fund::where('club_id', $clubId)->first();
        $fundBalance = $fund ? $fund->balance : 0;
    }

    // Trả về JSON
    return response()->json([
        'income' => $totalIncome,
        'expense' => $totalExpense,
        'balance' => $fundBalance,
        'formatted_income' => number_format($totalIncome, 0, ',', '.') . ' VND',
        'formatted_expense' => number_format($totalExpense, 0, ',', '.') . ' VND',
        'formatted_balance' => number_format($fundBalance, 0, ',', '.') . ' VND',
    ]);
}

   public function getClubBalance($clubId)
{
    try {
        // Lấy quỹ của CLB (mỗi CLB có 1 quỹ trong bảng funds)
        $fund = Fund::where('club_id', $clubId)->first();

        if (!$fund) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy quỹ cho CLB này.',
            ]);
        }

        // Trả về số dư
        return response()->json([
            'success' => true,
            'balance' => $fund->balance,
            'formatted' => number_format($fund->balance, 0, ',', '.') . ' VNĐ',
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Lỗi server: ' . $e->getMessage(),
        ]);
    }
}


}
