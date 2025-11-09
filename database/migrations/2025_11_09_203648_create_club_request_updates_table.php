<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function handleUpdateRequest(Request $request, $id)
    {
        $status = $request->input('status'); // 'approved' hoặc 'rejected'
        $rejectedReason = $request->input('rejected_reason');

        $update = ClubRequestUpdate::with(['memberUpdates', 'club.clubMembers'])->findOrFail($id);
        $club = $update->club;

        if ($status === 'approved') {

            // =============================
            // 1️⃣ Xử lý advisor
            // =============================
            if ($update->advisor_id && $update->advisor_id != $club->advisor_id) {
                if ($update->advisor_status === 'approved') {
                    $club->advisor_id = $update->advisor_id;
                    $club->advisor_status = 'approved';
                }
                // Nếu advisor chưa duyệt → không thay đổi gì
            }
            // Nếu giống với club->advisor_id → giữ nguyên

            // =============================
            // 2️⃣ Cập nhật thông tin CLB khác
            // =============================
            $fields = ['name', 'slogan', 'description', 'field', 'member_limit', 'email', 'phone', 'logo', 'rules', 'location'];
            foreach ($fields as $field) {
                if (!is_null($update->$field)) {

                    // Kiểm tra trùng tên nếu đang update name
                    if ($field === 'name') {
                        $exists = \App\Models\Club::where('name', $update->name)
                            ->where('id', '!=', $club->id)
                            ->exists();
                        if ($exists) {
                            return redirect()->back()
                                ->withErrors(['name' => 'Tên CLB đã tồn tại. Vui lòng chọn tên khác.']);
                        }
                    }

                    $club->$field = $update->$field;
                }
            }

            // Manager_id: lưu trực tiếp user_id
            if (!is_null($update->manager_id)) {
                $club->manager_id = $update->manager_id;
            }

            $club->save();

            // =============================
            // 3️⃣ Cập nhật ban quản lý
            // =============================
            foreach ($update->memberUpdates as $memberUpdate) {

                // Map user_id → member_id
                $member = \App\Models\Member::where('user_id', $memberUpdate->user_id)->first();
                if (!$member) {
                    continue; // nếu không có member tương ứng → bỏ qua
                }

                // Role 'member' cần check trùng trong các CLB khác
                if ($memberUpdate->role === 'member') {
                    $alreadyInOtherClub = \App\Models\ClubMember::where('role', 'member')
                        ->where('member_id', $member->id)
                        ->where('club_id', '!=', $club->id)
                        ->exists();

                    if ($alreadyInOtherClub) {
                        continue; // bỏ qua nếu trùng
                    }
                }

                if ($memberUpdate->role !== 'member') {
                    $existing = $club->clubMembers->where('role', $memberUpdate->role)->first();
                    if ($existing) {
                        $existing->member_id = $member->id;
                        $existing->save();
                    } else {
                        $club->clubMembers()->create([
                            'member_id' => $member->id,
                            'role' => $memberUpdate->role,
                        ]);
                    }
                } else {
                    $existing = $club->clubMembers()
                        ->where('role', 'member')
                        ->where('member_id', $member->id)
                        ->first();
                    if (!$existing) {
                        $club->clubMembers()->create([
                            'member_id' => $member->id,
                            'role' => 'member',
                        ]);
                    }
                }
            }

            // =============================
            // 4️⃣ Cập nhật trạng thái yêu cầu
            // =============================
            $update->status = 'approved';
            $update->save();

        } elseif ($status === 'rejected') {
            $update->status = 'rejected';
            $update->note = $rejectedReason;
            $update->save();
        }

        return redirect()->route('admin.club_requests_update.index')
            ->with('success', 'Xử lý yêu cầu cập nhật CLB thành công!');
    }


    public function down(): void
    {
        Schema::dropIfExists('club_request_updates');
    }
};
