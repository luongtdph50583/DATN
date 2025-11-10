@extends('admin.layouts.blank')

@section('title', 'Chi tiết yêu cầu cập nhật CLB')

@section('card-body')
        <div class="container mt-4">
            <h3 class="mb-4">Chi tiết yêu cầu cập nhật CLB</h3>

            {{-- 🔹 Thông tin chung --}}
        <div class="card mb-4 shadow-sm">
            <div class="card-body">
                <h5 class="mb-3">Thông tin yêu cầu</h5>
                <table class="table table-bordered">
                    <tr>
                        <th>Tên CLB</th>
                        <td>{{ $club->name }}</td>
                    </tr>
                    <tr>
                        <th>Người đề xuất</th>
                        <td>{{ $update->proposer->name ?? 'Không rõ' }}</td>
                    </tr>
                    <tr>
                        <th>Ngày gửi</th>
                        <td>{{ $update->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    <tr>
                        <th>Trạng thái advisor</th>
                        <td>
                            @if($update->advisor_status === 'pending')
                                <span class="badge bg-warning text-dark">Chờ duyệt</span>
                            @elseif($update->advisor_status === 'approved')
                                <span class="badge bg-success">Đã duyệt</span>
                            @elseif($update->advisor_status === 'rejected')
                                <span class="badge bg-danger">Từ chối</span>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <th>Lý do đề xuất</th>
                        <td>{{ $update->reason ?? 'Không có' }}</td>
                    </tr>
                </table>
            </div>
        </div>


            {{-- 🔹 Thông tin CLB (so sánh cũ – mới) --}}
            <div class="card mb-4 shadow-sm">
                <div class="card-body">
                    <h5>Thông tin CLB</h5>
                    <table class="table table-bordered align-middle text-center">
                        <thead class="table-light">
                            <tr>
                                <th>Trường</th>
                                <th>Hiện tại</th>
                                <th>Đề xuất</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
    $fields = ['name', 'slogan', 'description', 'field', 'member_limit', 'email', 'phone', 'logo', 'rules', 'location'];
                            @endphp
                            @foreach($fields as $field)
                                <tr>
                                    <td>{{ ucfirst(str_replace('_', ' ', $field)) }}</td>
                                    <td>{{ $club->$field ?? '—' }}</td>
                                    <td class="text-success fw-bold">{{ $update->$field ?? 'Không thay đổi' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- 🔹 Ban quản lý CLB --}}
            <div class="card mb-4 shadow-sm">
                <div class="card-body">
                    <h5>Ban quản lý CLB</h5>
                    <table class="table table-bordered align-middle text-center">
                        <thead class="table-light">
                            <tr>
                                <th>Chức vụ</th>
                                <th>Hiện tại</th>
                                <th>Đề xuất</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
    $roles = ['club_manager', 'deputy_manager', 'event_manager', 'communication', 'secretary', 'treasurer', 'member'];
                            @endphp
                            @foreach($roles as $role)
                                <tr>
                                    <td>{{ ucfirst(str_replace('_', ' ', $role)) }}</td>

                                    {{-- Thành viên hiện tại --}}
                                    <td>
                                        @php
        $current = $club->clubMembers->where('role', $role);
                                        @endphp
                                        @forelse($current as $cm)
                                            <div>{{ $cm->user->name ?? 'Không rõ' }}</div>
                                            <small class="text-muted">
                                                {{ $cm->memberInfo->student_code ?? '' }} | {{ $cm->memberInfo->phone ?? '' }}
                                            </small>
                                        @empty
                                            <em>Chưa có</em>
                                        @endforelse
                                    </td>

                                    {{-- Thành viên đề xuất --}}
                                    <td>
                                        @php
        $proposed = $update->memberUpdates->where('role', $role);
                                        @endphp
                                        @if($proposed->isNotEmpty())
                                            @foreach($proposed as $pm)
                                                <div class="text-success fw-bold">{{ $pm->user->name ?? 'Không rõ' }}</div>
                                                <small class="text-muted">
                                                    {{ $pm->memberInfo->student_code ?? '' }} | {{ $pm->memberInfo->phone ?? '' }}
                                                </small>
                                            @endforeach
                                        @else
                                            <em>Không thay đổi</em>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- 🔹 Hành động duyệt/từ chối --}}
            @if($update->advisor_status === 'pending')
                <div class="card shadow-sm">
                    <div class="card-body">
                        <form method="POST" action="{{ route('admin.club_requests_update.handleUpdateRequest', $update->id) }}">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label fw-bold">Lý do (nếu từ chối)</label>
                                <textarea name="rejected_reason" class="form-control" rows="2"></textarea>
                            </div>
                            <div class="d-flex gap-2">
                                <button type="submit" name="status" value="approved" class="btn btn-success">Duyệt</button>
                                <button type="submit" name="status" value="rejected" class="btn btn-danger">Từ chối</button>
                            </div>
                        </form>
                    </div>
                </div>
            @endif
        </div>
@endsection
