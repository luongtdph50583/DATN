<?php

namespace App\Http\Controllers\Admin;

use App\Models\Club;
use App\Models\ClubMember;
use Illuminate\Http\Request;
use App\Models\DocumentUpdateLog;
use App\Http\Controllers\Controller;

class DocumentUpdateLogController extends Controller
{
    //
    public function index(Request $request)
    {
        $query = DocumentUpdateLog::with([
            'document' => function ($q) {
                $q->withTrashed()->with('club'); // lấy cả document đã xóa mềm + club
            },
            'changedBy'
        ]);

        // ✅ Lọc theo từ khóa
        if ($request->filled('keyword')) {
            $query->whereHas('document', function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->keyword}%");
            });
        }

        // ✅ Lọc theo CLB
        if ($request->filled('club_id')) {
            $query->whereHas('document', function ($q) use ($request) {
                $q->where('clb_id', $request->club_id);
            });
        }

        // ✅ Lọc theo role
       
        $logs = $query->latest()->paginate(20);
        $logs->appends($request->query());

        // Tạo mảng role cho từng log
        $logRoles = [];
        foreach ($logs as $log) {
            $user = $log->changedBy;
            $roleDisplay = 'Không rõ';

            if ($user) {
                if ($user->role === 'admin') {
                    $roleDisplay = 'Admin';
                } else {
                    $clubId = $log->document?->clb_id;

                    if ($clubId) {
                        $clubMember = ClubMember::with('member.user')
                            ->where('club_id', $clubId)
                            ->whereHas('member.user', function ($q) use ($user) {
                                $q->where('id', $user->id);
                            })
                            ->first();

                        $roleDisplay = $clubMember->role ?? 'Member';
                    }
                }
            }

            $logRoles[$log->id] = $roleDisplay;
        }

        // ✅ Nạp danh sách CLB để truyền vào view
        $clubs = Club::orderBy('name')->get();

        return view('admin.document_update_logs.index', compact('logs', 'logRoles', 'clubs'));
    }


    public function show($id)
    {
        $log = DocumentUpdateLog::with([
            'document' => function ($q) {
                $q->withTrashed()->with('club'); // lấy cả document đã xóa mềm + club
            },
            'changedBy'
        ])->findOrFail($id);

        $user = $log->changedBy;
        $roleDisplay = 'Không rõ';

        if ($user) {
            if ($user->role === 'admin') {
                $roleDisplay = 'Admin';
            } else {
                $clubId = $log->document?->clb_id;

                if ($clubId) {
                    $clubMember = ClubMember::with('member.user')
                        ->where('club_id', $clubId)
                        ->whereHas('member.user', function ($q) use ($user) {
                            $q->where('id', $user->id);
                        })
                        ->first();

                    $roleDisplay = $clubMember->role ?? 'Member';
                }
            }
        }

        // Người sửa (editor)
        $editor = $user ? $user->name : 'Không rõ';

        return view('admin.document_update_logs.show', compact('log', 'roleDisplay', 'editor'));
    }



}
