<?php

namespace App\Http\Controllers\Client;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Club;
use App\Models\ClubMember;
use App\Models\Fund;
use App\Models\FundTransaction;
use App\Models\Member;
use App\Models\Event;
use Illuminate\Support\Facades\Auth;
use App\Exports\FundTransactionsExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Notification; // nếu bạn có model notifications
use Illuminate\Support\Facades\DB;
use App\Notifications\IncomeTransactionApprovedNotification;
class ClubFundController extends Controller
{
    public function index(Request $request, $club_id)
    {
        $user = Auth::user();
        $club = Club::findOrFail($club_id);
        $fund = $club->fund;

        $transactionsQuery = FundTransaction::where('club_id', $club_id)->orderBy('created_at','desc');

        if ($request->filled('from')) {
            $transactionsQuery->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $transactionsQuery->whereDate('created_at', '<=', $request->to);
        }

        $transactions = $transactionsQuery->get();

        // Sự kiện để chi
$events = Event::where('club_id', $club->id)
               ->where('status', 'approved')
               ->get();


        // Danh mục cố định cho thu
        $income_categories = ['Đóng góp', 'Quyên góp', 'Khác'];

        return view('client.pages.fund.fund', compact('club', 'fund', 'transactions', 'events', 'income_categories'));
    }

    public function storeTransaction(Request $request, $club_id)
    {
        $user = Auth::user();

        $request->validate([
            'type' => 'required|in:income,expense',
            'amount' => 'required|numeric|min:0.01',
            'description' => 'required|string',
            'category' => 'nullable|string',
            'custom_category' => 'nullable|string',
            'event_id' => 'nullable|exists:events,id',
            'receipt' => 'nullable|image|max:2048', 
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $category = $request->custom_category ?: $request->category;

        // Upload chứng từ
       $receiptPath = null;
if ($request->hasFile('receipt')) {
    $receiptPath = $request->file('receipt')->store('receipts', 'public');
}

        $transaction = FundTransaction::create([
            'club_id' => $club_id,
            'type' => $request->type,
            'amount' => $request->amount,
            'description' => $request->description,
            'category' => $category,
            'event_id' => $request->type === 'expense' ? $request->event_id : null,
            'status' => 'pending',
            'created_by' => $user->id,
            'receipt' => $receiptPath,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ]);

        return redirect()->back()->with('success','Giao dịch đã được tạo và đang chờ duyệt.');
    }


public function approveTransaction($club_id, FundTransaction $transaction)
{
    $user = auth()->user();

    // Chỉ duyệt khoản thu
    if ($transaction->type !== 'income') {
        return back()->with('error', 'Chỉ có thể duyệt khoản thu!');
    }

    if ($transaction->status === 'approved') {
        return back()->with('info', 'Giao dịch đã được duyệt trước đó.');
    }

    // Duyệt + cộng quỹ
    $transaction->update([
        'status'       => 'approved',
        'approved_by'  => $user->id,
        'approved_at'  => now(),
    ]);

 


    $recipients = \App\Models\ClubMember::query()
        ->where('club_id', $club_id)
        ->where('status', 'active')
        ->with('user')                   
        ->get()                           
        ->pluck('user')                  
        ->filter(fn($user) => $user?->email && $user?->email_verified_at)
        ->values();

   

    if ($recipients->isNotEmpty()) {
        Notification::send($recipients, new IncomeTransactionApprovedNotification($transaction));
        
        \Log::info('Sent income approval notification', [
            'transaction_id' => $transaction->id,
            'recipients' => $recipients->pluck('email')->toArray()
        ]);
    }

    return back()->with('success', 'Khoản thu đã được duyệt và thông báo đã gửi!');
}

public function approveExpense($club_id, FundTransaction $transaction)
{
    $user = auth()->user();

    // Chỉ duyệt khoản chi
    if ($transaction->type !== 'expense') {
        return back()->with('error', 'Đây không phải khoản chi!');
    }

    // Không duyệt nếu đã duyệt
    if ($transaction->status === 'approved') {
        return back()->with('info', 'Khoản chi này đã được duyệt trước đó.');
    }

    // Cập nhật trạng thái duyệt
    $transaction->update([
        'status'       => 'approved',
        'approved_by'  => $user->id,
        'approved_at'  => now(),
    ]);

    // Trừ quỹ CLB
    $fund = Fund::firstOrCreate(['club_id' => $club_id], ['balance' => 0]);
    if ($fund->balance < $transaction->amount) {
        return back()->with('error', 'Quỹ CLB không đủ để thực hiện khoản chi này!');
    }
    $fund->decrement('balance', $transaction->amount);



    return back()->with('success', 'Khoản chi đã được duyệt, trừ quỹ và thông báo đã gửi!');
}


    public function edit($club_id, FundTransaction $transaction)
    {
        abort_if(!in_array($transaction->status, ['approved', 'in_progress']), 403, 'Giao dịch chưa được duyệt hoặc đã hoàn tất.');

        $events = Event::where('club_id', $club_id)->orderBy('name')->get();

        return view('client.pages.fund.update', compact('transaction', 'club_id', 'events'));
    }

public function update(Request $request, $club_id, FundTransaction $transaction)
{
    // Chỉ xử lý giao dịch thu
    if($transaction->type !== 'income'){
        abort(403, 'Chỉ có thể cập nhật giao dịch thu tiền.');
    }

    $request->validate([
        'collected_amount' => 'required|numeric|min:0',
        'excel_file' => 'nullable|file|mimes:xlsx,xls,csv,jpg,jpeg,png,pdf|max:2048',
    ]);

    // Upload file nếu có
    if($request->hasFile('excel_file')){
        $path = $request->file('excel_file')->store('receipts', 'public');
        $transaction->excel_file = $path;
    }

    // Cập nhật số tiền thực tế
    $transaction->collected_amount = $request->collected_amount;

    // Cập nhật trạng thái hoàn tất
    $transaction->status = 'completed';
    $transaction->save();

    // Cập nhật quỹ CLB
    $fund = Fund::firstOrCreate(['club_id' => $club_id]);
    $fund->balance += $transaction->collected_amount;
    $fund->save();

    return redirect()->route('club_manager.fund.index', $club_id)
                     ->with('success', 'Giao dịch thu tiền đã được cập nhật và cộng vào quỹ.');
}




    public function export(Request $request, $club_id)
    {
        $club = Club::findOrFail($club_id);
        $fund = $club->fund;

        $transactionsQuery = FundTransaction::where('club_id', $club_id)->orderBy('created_at','desc');
        if ($request->filled('from')) {
            $transactionsQuery->whereDate('created_at', '>=', $request->from);
        }
        if ($request->filled('to')) {
            $transactionsQuery->whereDate('created_at', '<=', $request->to);
        }

        $transactions = $transactionsQuery->get();

        $fundBalance = $fund?->balance ?? 0;

        return Excel::download(new FundTransactionsExport($transactions, $fundBalance), "quy_{$club->name}.xlsx");
    }
}
