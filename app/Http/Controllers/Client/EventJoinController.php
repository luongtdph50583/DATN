<?php

namespace App\Http\Controllers\Client;

use App\Models\Event;
use App\Http\Controllers\Controller;
use App\Models\ClubEvent;
use App\Models\EventRegistration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventJoinController extends Controller
{
    /**
     * Hiển thị tất cả sự kiện mà user được xem và có thể đăng ký
     */
    public function index()
    {
        $user = Auth::user();

        // Lấy member profile
        $member = $user->member;

        // Danh sách CLB mà user là thành viên
        $clubIds = $member
            ? $member->clubs()->pluck('clubs.id')->toArray()
            : [];

        // Lấy ID sự kiện user đã đăng ký
        $joinedEventIds = $user->eventRegistrations()->pluck('event_id')->toArray();

        /**
         * Lấy sự kiện user được phép xem:
         * - is_public = 1 (sự kiện công khai)
         * - OR thuộc CLB user đang là thành viên
         * - AND phải được duyệt (status = 'approved')
         */
        $events = Event::where('status', 'approved')
            ->where(function ($query) use ($clubIds) {
                $query->where('is_public', 1)
                      ->orWhereIn('club_id', $clubIds);
            })
            ->with('club')
            ->orderBy('start_time', 'asc')
            ->get();
$now = now();

        return view('client.pages.event.index', [
            'events' => $events,
            'joinedEventIds' => $joinedEventIds,
            'now' => now(),
        ]);
    }

    public function show($id)
{
    $event = Event::with('club')->findOrFail($id);

    // kiểm tra user đã đăng ký chưa
    $user = auth()->user();
    $joined = false;

    if ($user) {
        $joined = $user->eventRegistrations()
            ->where('event_id', $event->id)
            ->exists();
    }

    return view('client.pages.event.show', compact('event', 'joined'));
}

    /**
     * Xử lý user đăng ký tham gia sự kiện
     */
public function join($id)
{
    $event = ClubEvent::findOrFail($id);
    $user = Auth::user();

    // Không cho đăng ký sau khi sự kiện bắt đầu
    if (now()->gte($event->start_time)) {
        return back()->with('error', 'Sự kiện đã bắt đầu hoặc đã diễn ra, không thể đăng ký.');
    }

    // Kiểm tra đã đăng ký chưa
    $exists = EventRegistration::where('event_id', $id)
        ->where('user_id', $user->id)
        ->exists();

    if ($exists) {
        return back()->with('info', 'Bạn đã đăng ký sự kiện này.');
    }

    EventRegistration::create([
        'event_id' => $id,
        'user_id'  => $user->id,
        'status'   => 'pending',
        'attendance_status' => 'pending',
        'registered_at' => now(),
    ]);

    return back()->with('success', 'Đăng ký tham gia sự kiện thành công!');
}



}
