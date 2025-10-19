<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Club;
use App\Models\Event;
use App\Models\Member;

class StatisticsController extends Controller
{
    public function index()
    {
        // 1. Tổng số CLB
        $clubCount = Club::count();

        // 2. Tổng số Thành viên
        $memberCount = User::where('role', 'member')->count();

        // 3. Tổng số Sự kiện
        $eventCount = Event::count();

        // 4. Số lượng theo tháng (ví dụ 12 tháng gần nhất)
        $currentYear = now()->year;

        $clubsPerMonth = [];
        $membersPerMonth = [];
        $eventsPerMonth = [];

        for ($month = 1; $month <= 12; $month++) {
            $clubsPerMonth[] = Club::whereYear('created_at', $currentYear)
                                    ->whereMonth('created_at', $month)
                                    ->count();

            $membersPerMonth[] = User::where('role', 'member')
                                     ->whereYear('created_at', $currentYear)
                                     ->whereMonth('created_at', $month)
                                     ->count();

            $eventsPerMonth[] = Event::whereYear('created_at', $currentYear)
                                     ->whereMonth('created_at', $month)
                                     ->count();
        }

        // 5. Truyền dữ liệu sang view
        return view('admin.statistics-and-reports.statistics', compact(
            'clubCount', 'memberCount', 'eventCount',
            'clubsPerMonth', 'membersPerMonth', 'eventsPerMonth'
        ));
    }
  public function clubs(Request $request)
{
    $sort = $request->query('sort', 'top_members'); // default

    $query = Club::withCount(['members', 'events']); // giả sử relationship 'members', 'events'

    switch($sort) {
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
    }

       $clubs = $query->paginate(10);

    return view('admin.statistics-and-reports.clubs', compact('clubs', 'sort',));
}

  // AdminStatsController.php
public function members(Request $request)
{
    $sort = $request->get('sort', 'newest');
    $status = $request->get('status', '');

    $query = Member::query();

    if ($status) {
        $query->where('status', $status);
    }

    if ($sort === 'oldest') {
        $query->orderBy('created_at', 'asc');
    } else {
        $query->orderBy('created_at', 'desc');
    }

    $members = $query->paginate(10);

    return view('admin.statistics-and-reports.members', [
        'members' => $members,
        'sort' => $sort,
        'status' => $status,
        'activeCount' => Member::where('status', 'active')->count(),
        'inactiveCount' => Member::where('status', 'inactive')->count(),
    ]);
}


   public function events(Request $request)
{
    $sort = $request->get('sort', 'newest');
    $status = $request->get('status', '');

    $query = Event::with('club');

    // Lọc theo trạng thái (nếu có)
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

    return view('admin.statistics-and-reports.events', compact('events', 'sort', 'status'));
}

}

