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
        if ($request->has('reset')) {
        return redirect()->route('admin.stats.index');
    }
    // 1️⃣ Nhận khoảng thời gian lọc từ request (nếu có)
    $startDate = $request->input('start_date');
    $endDate = $request->input('end_date');

    // Nếu chưa chọn ngày, mặc định là từ đầu năm đến hiện tại
    if (!$startDate || !$endDate) {
        $startDate = now()->startOfYear()->toDateString();
        $endDate = now()->endOfYear()->toDateString();
    }

    // 2️⃣ Tổng số CLB, Thành viên, Sự kiện trong khoảng thời gian đó
    $clubCount = Club::whereBetween('created_at', [$startDate, $endDate])->count();
    $memberCount = User::where('role', 'member')
                        ->whereBetween('created_at', [$startDate, $endDate])
                        ->count();
    $eventCount = Event::whereBetween('created_at', [$startDate, $endDate])->count();

    // 3️⃣ Lấy dữ liệu theo tháng trong khoảng chọn
    $clubsPerMonth = [];
    $membersPerMonth = [];
    $eventsPerMonth = [];
    $labels = [];

    // Tạo danh sách tháng trong khoảng ngày đã chọn
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

    // 4️⃣ Truyền dữ liệu sang view
    return view('admin.statistics-and-reports.statistics', compact(
        'clubCount', 'memberCount', 'eventCount',
        'clubsPerMonth', 'membersPerMonth', 'eventsPerMonth',
        'labels', 'startDate', 'endDate'
    ));
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

    // Kiểm tra nếu bấm nút Đặt lại
    if ($request->has('reset')) {
        return redirect()->route('admin.stats.events');
    }

    // Tham số lọc cho biểu đồ
    $startDate = $request->get('start_date');
    $endDate = $request->get('end_date');

    // --- DỮ LIỆU BẢNG ---
    $query = Event::query()->with('club');

    if ($status) {
        $query->where('status', $status);
    }

     // Sắp xếp
    if ($sort === 'oldest') {
        $query->orderBy('event_date', 'asc');
    } else {
        $query->orderBy('event_date', 'desc');
    }

    $events = $query->paginate(10);

    // --- DỮ LIỆU BIỂU ĐỒ ---
    $labels = [];
    $eventsPerMonth = [];

    // Nếu không có filter, mặc định 12 tháng gần nhất
    $start = $startDate ? Carbon::parse($startDate)->startOfMonth() : Carbon::now()->subMonths(11)->startOfMonth();
    $end = $endDate ? Carbon::parse($endDate)->endOfMonth() : Carbon::now()->endOfMonth();

    // Tạo các mốc tháng từ start → end
    $period = new \DatePeriod(
        $start,
        new \DateInterval('P1M'),
        (clone $end)->modify('+1 month') // bao gồm cả tháng cuối
    );

   
    foreach ($period as $date) {
        $month = Carbon::instance($date);

        // 🔥 Hiển thị tiếng Việt: "Tháng 1/2025"
        $labels[] = 'Tháng ' . $month->month . '/' . $month->year;

        $eventsPerMonth[] = Event::whereYear('created_at', $month->year)
            ->whereMonth('created_at', $month->month)
            ->count();
    }


    return view('admin.statistics-and-reports.events', [
        'events' => $events,
        'sort' => $sort,
        'status' => $status,
        'startDate' => $startDate,
        'endDate' => $endDate,
        'labels' => $labels,
        'eventsPerMonth' => $eventsPerMonth,
    ]);
}




}

