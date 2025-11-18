<?php

namespace App\Http\Controllers\ClubManager;

use App\Http\Controllers\Controller;
use App\Models\ClubEvent;
use App\Models\EventRegistration;
use App\Models\EventAttendance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class EventController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            $user = Auth::user();
            if (!$user || $user->role !== 'club_manager') {
                abort(403, 'Không có quyền truy cập');
            }

            $club = $user->managedClub; // Quan hệ: User hasOne Club
            if (!$club) {
                abort(403, 'Bạn chưa được gán quản lý CLB nào');
            }

            $request->merge(['club' => $club]);
            return $next($request);
        });
    }

    // Danh sách sự kiện
    public function index(Request $request)
    {
        $club = $request->club;
        $events = ClubEvent::where('club_id', $club->id)
            ->withCount('registrations')
            ->latest()
            ->paginate(10);

        return view('club.events.index', compact('events', 'club'));
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
            'title' => 'required|string|max:255',
            'type' => 'required|in:offline,lien_hoan,hop',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'location' => 'nullable|string|max:255',
            'max_participants' => 'nullable|integer|min:1',
            'description' => 'nullable|string'
        ]);

        $validated['club_id'] = $request->club->id;
        $validated['created_by'] = Auth::id();

        ClubEvent::create($validated);

        return redirect()->route('club.events.index')
            ->with('success', 'Tạo sự kiện thành công!');
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