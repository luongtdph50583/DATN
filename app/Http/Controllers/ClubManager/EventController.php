<?php

namespace App\Http\Controllers\ClubManager;

use App\Http\Controllers\Controller;
use App\Models\ClubEvent;
use App\Models\Event;
use App\Models\EventRegistration;
use App\Models\EventAttendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\DB;
class EventController extends Controller
{
    // Danh sách sự kiện
public function index()
{
    // Lấy CLB mà user quản lý
    $club = Auth::user()->managedClubs()->first();

    if (!$club) {
        abort(404, 'Bạn không quản lý CLB nào.');
    }

    // Chỉ lấy các sự kiện đã duyệt
    $events = Event::where('club_id', $club->id)
                    ->where('status', 'approved')
                    ->withCount('registrations')
                    ->orderBy('start_time', 'desc')
                    ->paginate(10);

    return view('club.events.index', compact('club', 'events'));
}




    // Form tạo sự kiện
    public function create(Request $request)
    {
        return view('club.events.create', ['club' => $request->club]);
    }

    // Lưu sự kiện
public function store(Request $request)
{

    $validated = $request->validate([
    'name' => 'required|string|max:255',
        'description' => 'nullable|string',
    'start_time' => ['required', 'date', 'after_or_equal:now'],
    'end_time' => ['required', 'date', 'after:start_time'],
    'location' => 'required|string|max:255',
    'max_participants' => 'nullable|integer|min:1',
     'is_public' => 'nullable|boolean',
    'budget_items' => 'nullable|array',
    'budget_items.*.item_name' => 'required|string|max:255',
    'budget_items.*.estimated_cost' => 'required|numeric|min:0',
    'budget_items.*.type' => 'required|in:club_fund,school_fund,other',
]);


    $validated['is_public'] = $request->has('is_public');

    DB::transaction(function () use ($validated) {
        $member = Auth::user()->member;
        $club = $member->clubs()->first();

        $event = Event::create([
            'club_id' => $club->id,
            'created_by' => Auth::id(),
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'start_time' => $validated['start_time'],
            'end_time' => $validated['end_time'],
            'location' => $validated['location'],
            'max_participants' => $validated['max_participants'] ?? null,
            'is_public' => $validated['is_public'] ? 1 : 0,
            'status' => 'pending', // member chỉ gửi yêu cầu
        ]);

        // Tạo chi tiết ngân sách
        foreach ($validated['budget_items'] as $index => $item) {
            $event->budgetItems()->create([
                'item_name' => $item['item_name'],
                'estimated_cost' => $item['estimated_cost'],
                'type' => $item['type'],
                'order' => $index,
            ]);
        }
    });

    return redirect()
        ->route('club_manager.events.index')
        ->with('success', 'Gửi yêu cầu tạo sự kiện và ngân sách thành công!');
}

public function requests()
{
    $club = Auth::user()->managedClubs()->first();
    if (!$club) abort(404, 'Bạn không quản lý CLB nào.');

    // Lấy các sự kiện đang chờ duyệt
      $events = Event::where('club_id', $club->id)
    ->whereIn('status', ['pending', 'rejected'])
    ->withCount('registrations')
    ->orderBy('created_at', 'desc')
    ->paginate(10);


    return view('club.events.requests', compact('events', 'club'));
}

public function show(Event $event)
{
    $club = Auth::user()->managedClubs()->first();

  

    $event->load('budgetItems');

    return view('club.events.show', compact('event', 'club'));
}


    // Danh sách đăng ký
    public function registrations($id)
    {
        $event = ClubEvent::where('club_id', Auth::user()->managedClub->id)
            ->with('registrations.user')
            ->findOrFail($id);

        return view('club.events.registrations', compact('event'));
    }

    // Duyệt đăng ký
    public function approveRegistration($registrationId)
    {
        $reg = EventRegistration::whereHas('event', function($q) {
            $q->where('club_id', Auth::user()->managedClub->id);
        })->findOrFail($registrationId);

        $reg->update(['status' => 'approved']);

        return back()->with('success', 'Đã duyệt đăng ký!');
    }

    // Điểm danh
    public function attendance($id)
    {
        $event = ClubEvent::where('club_id', Auth::user()->managedClub->id)
            ->with('registrations.user')
            ->findOrFail($id);

        $qrCode = base64_encode(QrCode::format('png')->size(300)->generate(
            route('club.events.checkin', $event->id)
        ));

        return view('club.events.attendance', compact('event', 'qrCode'));
    }

    // Xử lý điểm danh bằng QR
    public function checkin($eventId)
    {
        $event = ClubEvent::findOrFail($eventId);
        $user = Auth::user();

        EventAttendance::updateOrCreate(
            ['event_id' => $event->id, 'user_id' => $user->id],
            ['checked_in_at' => now(), 'check_in_method' => 'qr']
        );

        return view('club.events.checkin_success');
    }
}