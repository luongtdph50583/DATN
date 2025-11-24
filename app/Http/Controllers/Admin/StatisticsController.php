<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Club;
use App\Models\Event;
use App\Models\EventFundRequest;
use App\Models\Fund;
use Carbon\Carbon;
use App\Models\Member;
use App\Models\Post;
use Carbon\CarbonPeriod;
use PDF;

class StatisticsController extends Controller
{
public function index(Request $request)
{
    // === 1️⃣ Xử lý nút "Đặt lại" ===
    if ($request->has('reset')) {
        return redirect()->route('admin.stats.index');
    }

    // === 2️⃣ Lọc theo thời gian ===
    $startDate = $request->input('start_date') ?: now()->startOfYear()->toDateString();
    $endDate   = $request->input('end_date') ?: now()->endOfYear()->toDateString();

    // === 3️⃣ Query tổng quan ===
    $clubCount   = Club::whereBetween('created_at', [$startDate, $endDate])->count();
    $memberCount = User::where('role', 'member')->whereBetween('created_at', [$startDate, $endDate])->count();
    $eventCount  = Event::whereBetween('created_at', [$startDate, $endDate])->count();
    $fundCount   = EventFundRequest::whereBetween('created_at', [$startDate, $endDate])->count();      // bảng quỹ
    $postCount   = Post::whereBetween('created_at', [$startDate, $endDate])->count();      // bảng bài viết
    $accountCount = User::whereBetween('created_at', [$startDate, $endDate])->count();     // tất cả user

    // === 4️⃣ Chuẩn bị dữ liệu theo tháng ===
    $labels = [];
    $clubsPerMonth = [];
    $membersPerMonth = [];
    $eventsPerMonth = [];
    $fundsPerMonth = [];
    $postsPerMonth = [];
    $accountsPerMonth = [];

    $period = \Carbon\CarbonPeriod::create($startDate, '1 month', $endDate);

    foreach ($period as $date) {
        $month = $date->month;
        $year  = $date->year;
        $labels[] = "Tháng {$month}/{$year}";

        $clubsPerMonth[]   = Club::whereYear('created_at', $year)->whereMonth('created_at', $month)->count();
        $membersPerMonth[] = User::where('role', 'member')->whereYear('created_at', $year)->whereMonth('created_at', $month)->count();
        $eventsPerMonth[]  = Event::whereYear('created_at', $year)->whereMonth('created_at', $month)->count();
        $fundsPerMonth[]   = EventFundRequest::whereYear('created_at', $year)->whereMonth('created_at', $month)->count();
        $postsPerMonth[]   = Post::whereYear('created_at', $year)->whereMonth('created_at', $month)->count();
        $accountsPerMonth[]= User::whereYear('created_at', $year)->whereMonth('created_at', $month)->count();
    }

    // === 5️⃣ Trả dữ liệu về view ===
    return view('admin.statistics-and-reports.statistics', compact(
        'clubCount', 'memberCount', 'eventCount', 'fundCount', 'postCount', 'accountCount',
        'clubsPerMonth', 'membersPerMonth', 'eventsPerMonth', 'fundsPerMonth', 'postsPerMonth', 'accountsPerMonth',
        'labels', 'startDate', 'endDate'
    ))->with([
        'sort'   => $request->input('sort', ''),
        'status' => $request->input('status', ''),
        'search' => $request->input('search', ''),
    ]);
}




public function clubs(Request $request)
{
    // 🧩 Nếu nhấn Reset → quay lại mặc định
    if ($request->has('reset')) {
        return redirect()->route('admin.stats.clubs');
    }

    // 1️⃣ Lấy khoảng thời gian lọc
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');

    if (!$startDate || !$endDate) {
        $start = now()->startOfYear()->startOfDay();
        $end = now()->endOfYear()->endOfDay();
    } else {
        try {
            $start = \Carbon\Carbon::parse($startDate)->startOfDay();
        } catch (\Exception $e) {
            $start = now()->startOfYear()->startOfDay();
        }
        try {
            $end = \Carbon\Carbon::parse($endDate)->endOfDay();
        } catch (\Exception $e) {
            $end = now()->endOfYear()->endOfDay();
        }
    }

    // chuẩn cho input type="date" ở view
    $startDateView = $start->toDateString();
    $endDateView = $end->toDateString();

    // 2️⃣ Lọc theo sắp xếp
    $sort = $request->query('sort', 'top_members');

    $query = \App\Models\Club::withCount(['members', 'events'])
        ->whereBetween('created_at', [$start, $end]);

    // 3️⃣ Lọc theo trạng thái (nếu có)
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    // 4️⃣ Lọc theo tên CLB (tìm kiếm)
    if ($request->filled('search')) {
        $query->where('name', 'like', '%' . $request->search . '%');
    }

    // 5️⃣ Sắp xếp
    switch ($sort) {
        case 'least_members':
            $query->orderBy('members_count', 'asc');
            break;
        case 'oldest':
            $query->orderBy('created_at', 'asc');
            break;
        case 'most_events':
            $query->orderBy('events_count', 'desc');
            break;
        default:
            $query->orderBy('members_count', 'desc');
    }

    $clubs = $query->paginate(10)->appends($request->query());

    // ---------------------------
    // 🔢 Tạo dữ liệu cho biểu đồ
    // ---------------------------
    $labels = [];
    $clubsPerMonth = [];
    $membersPerMonth = [];
    $eventsPerMonth = [];

    // tạo period từ tháng bắt đầu đến tháng kết thúc
    $periodStart = $start->copy()->startOfMonth();
    $periodEnd = $end->copy()->endOfMonth();

    $period = \Carbon\CarbonPeriod::create($periodStart, '1 month', $periodEnd);

    foreach ($period as $date) {
        $month = $date->month;
        $year = $date->year;
        $labels[] = "Tháng {$month}/{$year}";

        // NOTE: dùng whereYear + whereMonth để đếm theo tháng
        $clubsPerMonth[] = Club::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->count();

        $membersPerMonth[] = User::where('role', 'member')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->count();

        $eventsPerMonth[] = Event::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->count();
    }

    // 6️⃣ Nếu request là AJAX → trả về partial bảng thôi (không trả data chart)
    if ($request->ajax()) {
        return view('admin.statistics-and-reports.partials.club_table', compact('clubs'))->render();
    }

    // 7️⃣ Nếu request thường → load full trang, truyền cả dữ liệu biểu đồ
    return view('admin.statistics-and-reports.clubs', compact(
        'clubs', 'sort', 'startDateView', 'endDateView',
        'labels', 'clubsPerMonth', 'membersPerMonth', 'eventsPerMonth'
    ));
}
public function clubsPdf(Request $request)
{
    $startDate = $request->input('start_date') ?: now()->startOfYear()->toDateString();
    $endDate   = $request->input('end_date') ?: now()->endOfYear()->toDateString();
    $status    = $request->input('status');
    $sort      = $request->input('sort');
    $search    = $request->input('search');

    $clubsQuery = \App\Models\Club::whereBetween('created_at', [$startDate, $endDate]);

    if ($status) {
        $clubsQuery->where('status', $status);
    }

    if ($search) {
        $clubsQuery->where('name', 'like', "%$search%");
    }

    // ✅ Sắp xếp theo yêu cầu
    switch ($sort) {
        case 'top_members':
            $clubsQuery->withCount('members')->orderByDesc('members_count');
            break;
        case 'least_members':
            $clubsQuery->withCount('members')->orderBy('members_count');
            break;
        case 'oldest':
            $clubsQuery->orderBy('created_at');
            break;
        case 'most_events':
            $clubsQuery->withCount('events')->orderByDesc('events_count');
            break;
        default:
            $clubsQuery->orderBy('created_at', 'desc');
            break;
    }

    $clubs = $clubsQuery->get();

    // ✅ Thống kê cơ bản
    $activeCount = $clubs->where('status', 'active')->count();
    $pendingCount = $clubs->where('status', 'pending')->count();
    $inactiveCount = $clubs->where('status', 'inactive')->count();

    // ✅ Tạo PDF từ view
    $pdf = \PDF::loadView('admin.statistics-and-reports.partials.club_pdf', [
        'clubs' => $clubs,
        'activeCount' => $activeCount,
        'pendingCount' => $pendingCount,
        'inactiveCount' => $inactiveCount,
        'status' => $status,
        'sort' => $sort,
        'search' => $search,
        'startDate' => $startDate,
        'endDate' => $endDate,
    ]);

    // ⚡ Tải file về trực tiếp
    return $pdf->download('thongke_caulacbo_' . now()->format('Ymd_His') . '.pdf');
}





  // AdminStatsController.php
public function members(Request $request)
{
    $sort = $request->get('sort', 'newest');
    $status = $request->get('status', '');
    $selectedClubs = $request->get('clubs', []); // mảng ID CLB

    // --- Query bảng member ---
    $membersQuery = Member::query();

    // Filter trạng thái
    if ($status) {
        $membersQuery->where('status', $status);
    }

    // Filter theo CLB (nếu chọn)
$selectedClubs = $request->get('clubs', []);
if (!empty($selectedClubs)) {
    $membersQuery->whereHas('clubs', fn($q) => $q->whereIn('clubs.id', $selectedClubs));
}


    // Sắp xếp
    $membersQuery->orderBy('created_at', $sort === 'oldest' ? 'asc' : 'desc');

    // Pagination
    $members = $membersQuery->paginate(10);

    // --- Lấy tất cả CLB cho filter ---
    $allClubs = Club::orderBy('name')->get();

    // --- Tính tổng active / inactive ---
    $activeCount = Member::where('status', 'active')->count();
    $inactiveCount = Member::where('status', 'inactive')->count();

    // --- Dữ liệu biểu đồ theo tháng ---
    $startDate = $request->get('start_date');
    $endDate = $request->get('end_date');

    $start = $startDate ? Carbon::parse($startDate)->startOfMonth() : Carbon::now()->subMonths(11)->startOfMonth();
    $end = $endDate ? Carbon::parse($endDate)->endOfMonth() : Carbon::now()->endOfMonth();

    $period = new \DatePeriod($start, new \DateInterval('P1M'), (clone $end)->modify('+1 month'));

    $membersPerMonth = [];
    $labels = [];

    foreach ($period as $date) {
        $month = Carbon::instance($date);
        $key = $month->format('Y-m');

        $monthQuery = Member::whereYear('created_at', $month->year)
                            ->whereMonth('created_at', $month->month);

        // Nếu filter CLB, áp dụng cho biểu đồ luôn
        if (!empty($selectedClubs)) {
            $monthQuery->whereHas('clubs', function ($q) use ($selectedClubs) {
                $q->whereIn('clubs.id', $selectedClubs);
            });
        }

        $membersPerMonth[$key] = $monthQuery->count();
        $labels[] = 'Tháng ' . $month->month . '/' . $month->year;
    }

    return view('admin.statistics-and-reports.members', [
        'members' => $members,
        'sort' => $sort,
        'status' => $status,
        'activeCount' => $activeCount,
        'inactiveCount' => $inactiveCount,
        'allClubs' => $allClubs,
        'selectedClubs' => $selectedClubs,
        'labels' => $labels,
        'membersPerMonth' => array_values($membersPerMonth),
        'startDate' => $startDate,
        'endDate' => $endDate,
    ]);
}






public function events(Request $request)
{
    $sort = $request->get('sort','newest');
    $status = $request->get('status','');
    $selectedClub = $request->get('club');
    $startDate = $request->get('start_date');
    $endDate = $request->get('end_date');

    $query = Event::with('club');

    // Lọc trạng thái
    if($status){
        $query->where('status', $status);
    }

    // Lọc CLB
    if($selectedClub){
        $query->where('club_id', $selectedClub);
    }

    // Lọc ngày tháng theo start_time
    if($startDate){
        $query->whereDate('start_time', '>=', Carbon::parse($startDate)->startOfDay());
    }
    if($endDate){
        $query->whereDate('start_time', '<=', Carbon::parse($endDate)->endOfDay());
    }

    // Sắp xếp
    switch($sort){
        case 'oldest': $query->orderBy('created_at','asc'); break;
        case 'start_asc': $query->orderBy('start_time','asc'); break;
        case 'start_desc': $query->orderBy('start_time','desc'); break;
        default: $query->orderBy('created_at','desc'); break;
    }

    $events = $query->paginate(10)->appends($request->query());

    // Dữ liệu biểu đồ
    $labels = [];
    $eventsPerMonth = [];
    $start = $startDate ? Carbon::parse($startDate)->startOfMonth() : Carbon::now()->subMonths(11)->startOfMonth();
    $end = $endDate ? Carbon::parse($endDate)->endOfMonth() : Carbon::now()->endOfMonth();

    $period = new \DatePeriod(
        $start,
        new \DateInterval('P1M'),
        (clone $end)->modify('+1 day')
    );

    $monthlyEvents = Event::selectRaw('YEAR(start_time) as year, MONTH(start_time) as month, count(*) as count')
        ->when($status, fn($q)=>$q->where('status',$status))
        ->when($selectedClub, fn($q)=>$q->where('club_id',$selectedClub))
        ->whereBetween('start_time', [$start, $end])
        ->groupBy('year','month')
        ->get()
        ->keyBy(fn($item)=> $item->year.'-'.str_pad($item->month,2,'0',STR_PAD_LEFT));

    foreach($period as $date){
        $month = Carbon::instance($date);
        $key = $month->year.'-'.str_pad($month->month,2,'0',STR_PAD_LEFT);
        $labels[] = 'Tháng '.$month->month.'/'.$month->year;
        $eventsPerMonth[] = $monthlyEvents->get($key)->count ?? 0;
    }

    $allClubs = Club::orderBy('name')->get();

    return view('admin.statistics-and-reports.events', [
        'events' => $events,
        'sort' => $sort,
        'status' => $status,
        'selectedClub' => $selectedClub,
        'startDate' => $startDate,
        'endDate' => $endDate,
        'labels' => $labels,
        'eventsPerMonth' => $eventsPerMonth,
        'allClubs' => $allClubs,
    ]);
}

public function exportPdf(Request $request)
{
    $sort = $request->get('sort', 'newest');
    $status = $request->get('status', '');
    $selectedClub = $request->get('club');
    $startDate = $request->get('start_date');
    $endDate = $request->get('end_date');

    $query = Event::with('club');

    if ($status) {
        $query->where('status', $status);
    }

    if ($selectedClub) {
        $query->where('club_id', $selectedClub);
    }

    if ($startDate) {
        $query->whereDate('start_time', '>=', Carbon::parse($startDate)->startOfDay());
    }

    if ($endDate) {
        $query->whereDate('start_time', '<=', Carbon::parse($endDate)->endOfDay());
    }

    switch ($sort) {
        case 'oldest': $query->orderBy('created_at','asc'); break;
        case 'start_asc': $query->orderBy('start_time','asc'); break;
        case 'start_desc': $query->orderBy('start_time','desc'); break;
        default: $query->orderBy('created_at','desc'); break;
    }

    // Lấy tất cả dữ liệu (không phân trang)
    $events = $query->get();

    $allClubs = Club::orderBy('name')->get();

    // Tạo PDF
    $pdf = PDF::loadView('admin.statistics-and-reports.events-pdf', [
        'events' => $events,
        'sort' => $sort,
        'status' => $status,
        'selectedClub' => $selectedClub,
        'startDate' => $startDate,
        'endDate' => $endDate,
        'allClubs' => $allClubs,
    ]);

    return $pdf->download('events_' . now()->format('Ymd_His') . '.pdf');
}



     public function accounts(Request $request)
    {
        // 🔹 Reset filter
        if ($request->has('reset')) {
            return redirect()->route('admin.stats.accounts');
        }

        // 🔹 Lọc thời gian
        $startDate = $request->input('start_date') ?: now()->startOfYear()->toDateString();
        $endDate   = $request->input('end_date') ?: now()->endOfYear()->toDateString();

        // 🔹 Lọc trạng thái và vai trò
        $status = $request->input('status'); // active / inactive / null
        $role   = $request->input('role');   // admin / member / null

        // 🔹 Query filter cho bảng + chart + card
        $filteredQuery = User::whereBetween('created_at', [$startDate, $endDate]);

        if ($status) {
            $filteredQuery->where('status', $status);
        }

        if ($role) {
            $filteredQuery->where('role', $role);
        }

        // 🔹 Tính số liệu cho card (clone query để không ảnh hưởng phân trang)
        $activeCount   = (clone $filteredQuery)->where('status', 'active')->count();
        $inactiveCount = (clone $filteredQuery)->where('status', 'inactive')->count();

        // 🔹 Phân trang bảng
        $accounts = $filteredQuery->orderBy('created_at', 'desc')
            ->paginate(10)
            ->appends($request->all());

        // 🔹 Dữ liệu biểu đồ theo tháng
        $labels = [];
        $accountsPerMonth = [];

        $period = CarbonPeriod::create($startDate, '1 month', $endDate);
        foreach ($period as $date) {
            $month = $date->month;
            $year  = $date->year;
            $labels[] = "Tháng {$month}/{$year}";

            $accountsPerMonth[] = (clone $filteredQuery)
                ->whereYear('created_at', $year)
                ->whereMonth('created_at', $month)
                ->count();
        }

  return view('admin.statistics-and-reports.accounts', compact(
            'accounts', 'activeCount', 'inactiveCount',
            'labels', 'accountsPerMonth',
            'status', 'role', 'startDate', 'endDate'
        ));
    }


public function accountsPdf(Request $request)
{
    $startDate = $request->input('start_date') ?: now()->startOfYear()->toDateString();
    $endDate   = $request->input('end_date') ?: now()->endOfYear()->toDateString();
    $status    = $request->input('status');
    $role      = $request->input('role');

    $accountsQuery = User::whereBetween('created_at', [$startDate, $endDate]);
    if ($status) $accountsQuery->where('status', $status);
    if ($role) $accountsQuery->where('role', $role);
    $accounts = $accountsQuery->orderBy('created_at', 'desc')->get();

    $activeCount = $accounts->where('status', 'active')->count();
    $inactiveCount = $accounts->where('status', 'inactive')->count();

    $pdf = PDF::loadView('admin.statistics-and-reports.accounts-pdf', [
        'accounts' => $accounts,
        'activeCount' => $activeCount,
        'inactiveCount' => $inactiveCount,
        'status' => $status,
        'role' => $role,
        'startDate' => $startDate,
        'endDate' => $endDate,
    ]);

    return $pdf->download('thongke_taikhoan_' . now()->format('Ymd_His') . '.pdf');
}
public function fundRequests(Request $request)
{
    // Reset filter
    if ($request->has('reset')) {
        return redirect()->route('admin.stats.funds');
    }

    // Lấy input filter, mặc định từ đầu năm tới cuối năm
    $startDate = $request->input('start_date') ?: now()->startOfYear()->toDateString();
    $endDate   = $request->input('end_date') ?: now()->endOfYear()->toDateString();
    $status    = $request->input('status');        // pending_disbursement, disbursing, disbursed, rejected
    $selectedClub = $request->input('club');      // CLB lọc

    $allClubs = Club::orderBy('name')->get();

    // -------------------------
    // 1️⃣ Query cơ bản (build 1 lần)
    // -------------------------
    $baseQuery = EventFundRequest::with(['event.club', 'requestedBy'])
        ->whereBetween('created_at', [$startDate, $endDate])
        ->when($status, fn($q) => $q->where('status', $status))
        ->when($selectedClub, fn($q) =>
            $q->whereHas('event', fn($e) => $e->where('club_id', $selectedClub))
        );

    // Clone query để không ảnh hưởng paginate
    $statsQuery = clone $baseQuery;
    $chartQuery = clone $baseQuery;

    // -------------------------
    // 2️⃣ Pagination
    // -------------------------
    $fundRequests = $baseQuery
        ->orderByDesc('created_at')
        ->paginate(10)
        ->appends($request->all());

    // -------------------------
    // 3️⃣ Tổng quan
    // -------------------------
    $totalRequests       = $statsQuery->count();
    $totalRequestedAmount = $statsQuery->sum('amount_requested');
    $totalApprovedAmount  = $statsQuery->sum('approved_amount');
    $totalDisbursedAmount = $statsQuery->sum('amount_disbursed');
    $disbursingCount      = $statsQuery->where('status', 'disbursing')->count();

    // -------------------------
    // 4️⃣ Biểu đồ theo tháng
    // -------------------------
    $start = Carbon::parse($startDate)->startOfMonth();
    $end   = Carbon::parse($endDate)->endOfMonth();
    $period = CarbonPeriod::create($start, '1 month', $end);

    $labels = [];
    $requestsPerMonth = [];

    foreach ($period as $month) {
        $labels[] = "Tháng {$month->month}/{$month->year}";

        $requestsPerMonth[] = $chartQuery
            ->whereYear('created_at', $month->year)
            ->whereMonth('created_at', $month->month)
            ->count();
    }

    // -------------------------
    // 5️⃣ Trả về view
    // -------------------------
    return view('admin.statistics-and-reports.fund-requests', compact(
        'fundRequests',
        'totalRequests', 'totalRequestedAmount', 'totalApprovedAmount',
        'totalDisbursedAmount', 'disbursingCount',
        'startDate', 'endDate', 'status', 'allClubs', 'selectedClub',
        'labels', 'requestsPerMonth'
    ));
}




    /**
     * Xuất PDF
     */
public function fundsPdf(Request $request)
{
    $startDate = $request->get('start_date') ?: now()->startOfYear()->toDateString();
    $endDate   = $request->get('end_date') ?: now()->endOfYear()->toDateString();
    $status    = $request->get('status');
    $selectedClub = $request->get('club');

    // Build lại query giống trang chính
    $query = EventFundRequest::with(['event.club', 'requestedBy'])
        ->whereBetween('created_at', [$startDate, $endDate])
        ->when($status, fn($q) => $q->where('status', $status))
        ->when($selectedClub, fn($q) =>
            $q->whereHas('event', fn($e) => $e->where('club_id', $selectedClub))
        );

    $fundRequests = $query->orderByDesc('created_at')->get();

    // Tổng quan
    $totalRequests = $fundRequests->count();
    $totalRequestedAmount = $fundRequests->sum('amount_requested');
    $totalApprovedAmount  = $fundRequests->sum('approved_amount');
    $totalDisbursedAmount = $fundRequests->sum('amount_disbursed');
    $disbursingCount      = $fundRequests->where('status', 'disbursing')->count();

    // Biểu đồ theo tháng
    $start = Carbon::parse($startDate)->startOfMonth();
    $end   = Carbon::parse($endDate)->endOfMonth();
    $period = CarbonPeriod::create($start, '1 month', $end);

    $labels = [];
    $requestsPerMonth = [];

    foreach ($period as $month) {
        $labels[] = "Tháng {$month->month}/{$month->year}";
        $requestsPerMonth[] = $fundRequests->filter(function($item) use ($month){
            return Carbon::parse($item->created_at)->year == $month->year
                && Carbon::parse($item->created_at)->month == $month->month;
        })->count();
    }

    return view('admin.statistics-and-reports.fund-requests-pdf', compact(
        'fundRequests',
        'totalRequests', 'totalRequestedAmount', 'totalApprovedAmount',
        'totalDisbursedAmount', 'disbursingCount',
        'labels', 'requestsPerMonth',
        'startDate','endDate','status','selectedClub'
    ));
}



public function posts(Request $request)
{
    // === Bộ lọc ===
    $status = $request->input('status');
    $type = $request->input('type');
    $startDate = $request->input('start_date') ?: now()->startOfYear()->toDateString();
    $endDate = $request->input('end_date') ?: now()->endOfYear()->toDateString();

    // === Query cơ bản ===
    $query = Post::with(['user','club'])
        ->whereBetween('created_at', [$startDate, $endDate]);

    if ($status) $query->where('status', $status);
    if ($type) $query->where('type', $type);

    $posts = $query->latest()->paginate(10)->appends($request->all());

    // === Thống kê nhanh ===
    $pendingCount = Post::where('status','pending')->whereBetween('created_at',[$startDate,$endDate])->count();
    $approvedCount = Post::where('status','approved')->whereBetween('created_at',[$startDate,$endDate])->count();
    $rejectedCount = Post::where('status','rejected')->whereBetween('created_at',[$startDate,$endDate])->count();
    $featuredCount = Post::where('is_featured',true)->whereBetween('created_at',[$startDate,$endDate])->count();

    // === Biểu đồ số lượng bài viết theo tháng ===
    $labels = [];
    $postsPerMonth = [];
    $period = CarbonPeriod::create($startDate, '1 month', $endDate);
    foreach($period as $date){
        $labels[] = 'Tháng '.$date->month.'/'.$date->year;
        $postsPerMonth[] = Post::whereYear('created_at',$date->year)
            ->whereMonth('created_at',$date->month)
            ->count();
    }

    return view('admin.statistics-and-reports.posts', compact(
        'posts','pendingCount','approvedCount','rejectedCount','featuredCount',
        'labels','postsPerMonth','status','type','startDate','endDate'
    ));
}
}
