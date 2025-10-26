<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Club;
use App\Models\Event;
use Carbon\Carbon;
use App\Models\Member;

class StatisticsController extends Controller
{
 public function index(Request $request)
{
    // === 1️⃣ Xử lý nút "Đặt lại" ===
    if ($request->has('reset')) {
        return redirect()->route('admin.stats.index');
    }

    // === 2️⃣ Lọc theo thời gian ===
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');

    if (!$startDate || !$endDate) {
        $startDate = now()->startOfYear()->toDateString();
        $endDate = now()->endOfYear()->toDateString();
    }

    // === 3️⃣ Tạo query cơ bản cho CLB ===
    $query = Club::withCount(['members', 'events'])
        ->whereBetween('created_at', [$startDate, $endDate]);

    // 🔹 Lọc theo trạng thái
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    // 🔹 Lọc theo từ khóa tìm kiếm (theo tên CLB)
    if ($request->filled('search')) {
        $query->where('name', 'like', '%' . $request->search . '%');
    }

    // 🔹 Sắp xếp theo tùy chọn
    switch ($request->input('sort')) {
        case 'top_members':
            $query->orderByDesc('members_count');
            break;
        case 'least_members':
            $query->orderBy('members_count');
            break;
        case 'oldest':
            $query->orderBy('created_at', 'asc');
            break;
        case 'most_events':
            $query->orderByDesc('events_count');
            break;
        default:
            $query->latest();
            break;
    }

    // Lấy dữ liệu (có phân trang + giữ bộ lọc)
    $clubs = $query->paginate(10)->appends($request->all());

    // === 4️⃣ Tính toán số liệu tổng quan ===
    $clubCount = Club::whereBetween('created_at', [$startDate, $endDate])->count();
    $memberCount = User::where('role', 'member')
        ->whereBetween('created_at', [$startDate, $endDate])
        ->count();
    $eventCount = Event::whereBetween('created_at', [$startDate, $endDate])->count();

    // === 5️⃣ Chuẩn bị dữ liệu cho biểu đồ thống kê theo tháng ===
    $clubsPerMonth = [];
    $membersPerMonth = [];
    $eventsPerMonth = [];
    $labels = [];

    $period = \Carbon\CarbonPeriod::create($startDate, '1 month', $endDate);

    foreach ($period as $date) {
        $month = $date->month;
        $year = $date->year;
        $labels[] = "Tháng {$month}/{$year}";

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

    // === 6️⃣ Trả dữ liệu về view ===
    return view('admin.statistics-and-reports.statistics', compact(
        'clubs',
        'clubCount', 'memberCount', 'eventCount',
        'clubsPerMonth', 'membersPerMonth', 'eventsPerMonth',
        'labels', 'startDate', 'endDate'
    ))->with([
        'sort' => $request->input('sort', ''),
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

    // 1️⃣ Nhận thời gian lọc
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');

    // Nếu không có → mặc định là đầu năm đến cuối năm hiện tại
    if (!$startDate || !$endDate) {
        $startDate = now()->startOfYear()->toDateString();
        $endDate = now()->endOfYear()->toDateString();
    }

    // 2️⃣ Bộ lọc sắp xếp
    $sort = $request->query('sort', 'top_members');

    // 🔹 Lấy danh sách CLB kèm đếm thành viên & sự kiện
    // (Giả sử Club model có quan hệ: members() và events())
    $query = \App\Models\Club::withCount(['members', 'events'])
        ->whereBetween('created_at', [$startDate, $endDate]);

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
            break;
    }

    $clubs = $query->paginate(10);

    // 3️⃣ Dữ liệu cho biểu đồ
    $labels = [];
    $clubsPerMonth = [];
    $membersPerMonth = [];
    $eventsPerMonth = [];

    $period = \Carbon\CarbonPeriod::create($startDate, '1 month', $endDate);

    foreach ($period as $date) {
        $month = $date->month;
        $year = $date->year;
        $labels[] = "Tháng {$month}/{$year}";

        // Số CLB tạo trong tháng
        $clubsPerMonth[] = \App\Models\Club::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->count();

        // Số thành viên đăng ký trong tháng
        $membersPerMonth[] = \App\Models\User::where('role', 'member')
            ->whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->count();

        // Số sự kiện tạo trong tháng
        $eventsPerMonth[] = \App\Models\Event::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->count();
    }

    // 4️⃣ Trả về view
    return view('admin.statistics-and-reports.clubs', compact(
        'clubs', 'sort', 'startDate', 'endDate',
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




}

