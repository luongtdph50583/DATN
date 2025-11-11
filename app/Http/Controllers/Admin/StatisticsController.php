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






  // AdminStatsController.php
public function members(Request $request)
{
    $sort = $request->get('sort', 'newest');
    $status = $request->get('status', '');

    // --- Query bảng (không lọc theo ngày) ---
    $membersQuery = Member::query();
    if ($status) {
        $membersQuery->where('status', $status);
    }
    $membersQuery->orderBy('created_at', $sort === 'oldest' ? 'asc' : 'desc');
    $members = $membersQuery->paginate(10);

    // --- Query dữ liệu biểu đồ (lọc theo thời gian nếu có) ---
    $startDate = $request->get('start_date');
    $endDate = $request->get('end_date');

    $start = $startDate ? Carbon::parse($startDate)->startOfMonth() : Carbon::now()->subMonths(11)->startOfMonth();
    $end = $endDate ? Carbon::parse($endDate)->endOfMonth() : Carbon::now()->endOfMonth();

    $period = new \DatePeriod($start, new \DateInterval('P1M'), (clone $end)->modify('+1 month'));

    $membersPerMonth = [];
    foreach ($period as $date) {
        $month = Carbon::instance($date);
        $key = $month->format('Y-m');
        $membersPerMonth[$key] = Member::whereYear('created_at', $month->year)
                                        ->whereMonth('created_at', $month->month)
                                        ->count();
        $labels[] = 'Tháng ' . $month->month . '/' . $month->year;
    }

    return view('admin.statistics-and-reports.members', [
        'members' => $members,
        'sort' => $sort,
        'status' => $status,
        'activeCount' => Member::where('status', 'active')->count(),
        'inactiveCount' => Member::where('status', 'inactive')->count(),
        'labels' => $labels,
        'membersPerMonth' => array_values($membersPerMonth), // dùng cho Chart.js
        'startDate' => $startDate,
        'endDate' => $endDate,
    ]);
}





public function events(Request $request)
{
    // Tham số lọc bảng
    $sort = $request->get('sort', 'newest');
    $status = $request->get('status', '');

    // Kiểm tra nếu bấm nút Đặt lại (trong trường hợp người dùng bấm nút Đặt lại của biểu đồ)
    // Nếu bạn muốn reset toàn bộ, nên dùng một tham số reset khác hoặc xử lý reset trong Blade
    if ($request->has('reset')) {
        // Giữ lại các filter của bảng nếu có trong URL
        $queryParams = $request->only(['sort', 'status']);
        return redirect()->route('admin.stats.events', $queryParams);
    }

    // Tham số lọc cho biểu đồ
    $startDate = $request->get('start_date');
    $endDate = $request->get('end_date');

    // --- DỮ LIỆU BẢNG (Events Table) ---
    $query = Event::query()->with('club');

    // 1. Lọc theo Trạng thái (Status)
    if ($status) {
        $query->where('status', $status);
    }

    // 2. Sắp xếp (Sort)
    // Sửa lỗi 'event_date' và thêm logic cho start_asc, start_desc
    switch ($sort) {
        case 'oldest': // Cũ nhất theo created_at (thời gian tạo)
            $query->orderBy('created_at', 'asc');
            break;
        case 'start_asc': // Sắp xếp theo Thời gian bắt đầu (Sớm nhất)
            $query->orderBy('start_time', 'asc');
            break;
        case 'start_desc': // Sắp xếp theo Thời gian bắt đầu (Muộn nhất)
            $query->orderBy('start_time', 'desc');
            break;
        case 'newest': // Mặc định: Mới nhất theo created_at (thời gian tạo)
        default:
            $query->orderBy('created_at', 'desc');
            break;
    }

    $events = $query->paginate(10)->appends(['sort' => $sort, 'status' => $status, 'start_date' => $startDate, 'end_date' => $endDate]);

    // --- DỮ LIỆU BIỂU ĐỒ (Chart Data) ---
    $labels = [];
    $eventsPerMonth = [];

    // Nếu không có filter, mặc định 12 tháng gần nhất
    // Lưu ý: Biểu đồ này đếm theo thời gian tạo sự kiện (created_at)
    $start = $startDate ? Carbon::parse($startDate)->startOfMonth() : Carbon::now()->subMonths(11)->startOfMonth();
    $end = $endDate ? Carbon::parse($endDate)->endOfMonth() : Carbon::now()->endOfMonth();

    // Tạo các mốc tháng từ start → end
    $period = new \DatePeriod(
        $start,
        new \DateInterval('P1M'),
        (clone $end)->modify('+1 day') // Dùng +1 day để bao gồm cả tháng cuối cùng
    );

    // Lấy tất cả sự kiện đã tạo trong khoảng thời gian để tối ưu truy vấn
    $monthlyEvents = Event::selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, count(*) as count')
        ->whereBetween('created_at', [$start, $end])
        ->groupBy('year', 'month')
        ->get()
        ->keyBy(function ($item) {
            return $item->year . '-' . str_pad($item->month, 2, '0', STR_PAD_LEFT);
        });

    foreach ($period as $date) {
        $month = Carbon::instance($date);
        $key = $month->year . '-' . str_pad($month->month, 2, '0', STR_PAD_LEFT);

        // 🔥 Hiển thị tiếng Việt: "Tháng 1/2025"
        $labels[] = 'Tháng ' . $month->month . '/' . $month->year;

        // Lấy số lượng từ collection đã query, nếu không có thì là 0
        $eventsPerMonth[] = $monthlyEvents->get($key)->count ?? 0;
    }


    return view('admin.statistics-and-reports.events', [
        'events' => $events,
        'sort' => $sort,
        'status' => $status,
        // Chuyển lại giá trị cho input type="date"
        'startDate' => $startDate, 
        'endDate' => $endDate, 
        'labels' => $labels,
        'eventsPerMonth' => $eventsPerMonth,
    ]);
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

    $startDate = $request->input('start_date') ?: now()->startOfYear()->toDateString();
    $endDate   = $request->input('end_date') ?: now()->endOfYear()->toDateString();
    $status    = $request->input('status'); // pending_disbursement, disbursing, disbursed, rejected

    $query = EventFundRequest::with(['event', 'requestedBy', 'approvedBy', 'disbursedBy', 'rejectedBy'])
        ->whereBetween('created_at', [$startDate, $endDate]);

    if ($status) {
        $query->where('status', $status);
    }

    $fundRequests = $query->orderByDesc('created_at')->paginate(10)->appends($request->all());

    // Tổng quan
    $totalRequests = $query->count();
    $totalRequestedAmount = $query->sum('amount_requested');
    $totalApprovedAmount  = $query->sum('approved_amount');

    // Biểu đồ theo tháng
    $labels = [];
    $requestsPerMonth = [];
    $period = CarbonPeriod::create($startDate, '1 month', $endDate);

    foreach ($period as $date) {
        $labels[] = "Tháng {$date->month}/{$date->year}";
        $requestsPerMonth[] = EventFundRequest::whereYear('created_at', $date->year)
            ->whereMonth('created_at', $date->month)
            ->when($status, fn($q) => $q->where('status', $status))
            ->count();
    }

    return view('admin.statistics-and-reports.fund-requests', compact(
        'fundRequests', 'totalRequests', 'totalRequestedAmount', 'totalApprovedAmount',
        'startDate', 'endDate', 'status', 'labels', 'requestsPerMonth'
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
