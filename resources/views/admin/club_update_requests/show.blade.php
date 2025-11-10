@extends('admin.layouts.blank')

@section('title', 'Chi tiết yêu cầu cập nhật CLB')

@section('card-body')
                            <div class="container mt-4">
                                <h3 class="mb-4">Chi tiết yêu cầu cập nhật CLB</h3>

                                {{-- 🔹 Thông tin yêu cầu --}}
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
                                                <th>Lý do đề xuất</th>
                                                <td>{{ $update->reason ?? 'Không có' }}</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>

                                {{-- 🔹 Thông tin CLB --}}
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

                                                        {{-- Hiện tại --}}
                                                        <td>
                                                            @if($field === 'logo' && $club->$field)
                                                                <img src="{{ Storage::url($club->$field) }}" class="rounded me-3"
                                                                    style="width:80px;height:80px;object-fit:cover;" alt="Logo hiện tại">
                                                            @else
                                                                {{ $club->$field ?? '—' }}
                                                            @endif
                                                        </td>

                                                        {{-- Đề xuất --}}
                                                        <td class="text-success fw-bold">
                                                            @if($field === 'logo' && $update->$field)
                                                                <img src="{{ Storage::url($update->$field) }}" class="rounded me-3"
                                                                    style="width:80px;height:80px;object-fit:cover;" alt="Logo đề xuất">
                                                            @else
                                                                {{ $update->$field ?? 'Không thay đổi' }}
                                                            @endif
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>



                                {{-- 🔹 Giảng viên đỡ đầu --}}
                                <div class="card mb-4 shadow-sm">
                                    <div class="card-body">
                                        <h5>Giảng viên đỡ đầu</h5>
                                        <table class="table table-bordered align-middle text-center">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Thông tin</th>
                                                    <th>Hiện tại</th>
                                                    <th>Đề xuất</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td><strong>Giảng viên đỡ đầu</strong></td>
                                                    {{-- Hiện tại --}}
                                                    <td class="text-start">
                                                        @if($club->advisorFaculty)
                                                            <div><strong>{{ $club->advisorFaculty->user->name ?? 'Không rõ' }}</strong></div>
                                                            <div>Mã viên chức: {{ $club->advisorFaculty->employee_code }}</div>
                                                            <div>Khoa: {{ $club->advisorFaculty->department }}</div>
                                                            <div>Chức danh: {{ $club->advisorFaculty->title }}</div>
                                                            <div>Vị trí: {{ $club->advisorFaculty->position }}</div>
                                                            <div>Điện thoại VP: {{ $club->advisorFaculty->office_phone }}</div>
                                                            <div>Email công vụ: {{ $club->advisorFaculty->email_official }}</div>
                                                            <div>Văn phòng: {{ $club->advisorFaculty->office_location }}</div>
                                                        @else
                                                            <em>Chưa có giảng viên đỡ đầu</em>
                                                        @endif
                                                    </td>
                                                    {{-- Đề xuất --}}
                                                    <td class="text-start text-success">
                                                        @if($update->advisorFacultyProposed)
                                                            <div><strong>{{ $update->advisorFacultyProposed->user->name ?? 'Không rõ' }}</strong></div>
                                                            <div>Mã viên chức: {{ $update->advisorFacultyProposed->employee_code }}</div>
                                                            <div>Khoa: {{ $update->advisorFacultyProposed->department }}</div>
                                                            <div>Chức danh: {{ $update->advisorFacultyProposed->title }}</div>
                                                            <div>Vị trí: {{ $update->advisorFacultyProposed->position }}</div>
                                                            <div>Email công vụ: {{ $update->advisorFacultyProposed->email_official }}</div>
                                                            <div>Văn phòng: {{ $update->advisorFacultyProposed->office_location }}</div>
                                                            <div>Trạng thái:
                                                                <span class="badge
                                                                    @if($update->advisor_status === 'approved') bg-success
                                                                    @elseif($update->advisor_status === 'rejected') bg-danger
                                                                    @else bg-warning text-dark
                                                                    @endif
                                                                ">
                                                                    {{ ucfirst($update->advisor_status) ?? 'Chưa duyệt' }}
                                                                </span>
                                                            </div>

                                                        @else
                                                            <em>Không thay đổi</em>
                                                        @endif
                                                    </td>
                                                </tr>
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
    $roles = ['club_manager', 'deputy_manager', 'event_manager', 'communication', 'secretary', 'treasurer'];
                                                @endphp

                                                @foreach($roles as $role)
                                                                                                                <tr>
                                                                                                                    <td>{{ ucfirst(str_replace('_', ' ', $role)) }}</td>

                                                                                                                    {{-- Thành viên hiện tại --}}
                                                                                                                <td>
                                                                                                                    @if($role === 'club_manager')
                                                                                                                        @php
            // Lấy record club_member có role là club_manager
            $clubManagerRecord = $currentData['members']->where('role', 'club_manager')->first();
                                                                                                                        @endphp

                                                                                                                        @if($clubManagerRecord)
                                                                                                                            <div><strong>{{ $clubManagerRecord->member->user->name ?? 'Không rõ' }}</strong></div>
                                                                                                                            <div>Email: {{ $clubManagerRecord->member->email ?? '—' }}</div>
                                                                                                                            <div>Ngày bổ nhiệm:
                                                                                                                                {{ $clubManagerRecord->appointed_at ? \Carbon\Carbon::parse($clubManagerRecord->appointed_at)->format('d/m/Y') : '—' }}
                                                                                                                            </div>
                                                                                                                        @else
                                                                                                                            <em>Chưa có</em>
                                                                                                                        @endif
                                                                                                                    @else
                                                                                                                        @php
            $currentMembers = $currentData['members']->where('role', $role);
                                                                                                                        @endphp
                                                                                                                        @forelse($currentMembers as $cm)
                                                                                                                            <div><strong>{{ $cm->member->user->name ?? 'Không rõ' }}</strong></div>
                                                                                                                            <div>MSSV: {{ $cm->member->student_code ?? '—' }}</div>
                                                                                                                            <div>Điện thoại: {{ $cm->member->phone ?? '—' }}</div>
                                                                                                                            <div>Email: {{ $cm->member->email ?? '—' }}</div>
                                                                                                                            <div>Khóa học: {{ $cm->member->course ?? '—' }}</div>
                                                                                                                            <div>Ngành học: {{ $cm->member->major ?? '—' }}</div>
                                                                                                                            <hr>
                                                                                                                        @empty
                                                                                                                            <em>Chưa có</em>
                                                                                                                        @endforelse
                                                                                                                    @endif
                                                                                                                </td>


                                                                                                                    {{-- Thành viên đề xuất --}}
                                                                                                            <td>
                                                                                                                @if($role === 'club_manager')
                                                                                                                    @php
            $clubManagerRecord = $currentData['members']->where('role', 'club_manager')->first();
                                                                                                                    @endphp

                                                                                                                    @if($clubManagerRecord)
                                                                                                                        <div><strong>{{ $clubManagerRecord->member->user->name ?? 'Không rõ' }}</strong></div>
                                                                                                                        <div>MSSV: {{ $clubManagerRecord->member->student_code ?? '—' }}</div>
                                                                                                                        <div>Điện thoại: {{ $clubManagerRecord->member->phone ?? '—' }}</div>
                                                                                                                        <div>Email: {{ $clubManagerRecord->member->email ?? '—' }}</div>
                                                                                                                        <div>Khóa học: {{ $clubManagerRecord->member->course ?? '—' }}</div>
                                                                                                                        <div>Ngành học: {{ $clubManagerRecord->member->major ?? '—' }}</div>
                                                                                                                        <div>Ngày bổ nhiệm:
                                                                                                                            {{ $clubManagerRecord->appointed_at ? \Carbon\Carbon::parse($clubManagerRecord->appointed_at)->format('d/m/Y') : '—' }}
                                                                                                                        </div>
                                                                                                                    @else
                                                                                                                        <em>Chưa có</em>
                                                                                                                    @endif
                                                                                                                @else
                                                                                                                    @php
            $currentMembers = $currentData['members']->where('role', $role);
                                                                                                                    @endphp
                                                                                                                    @forelse($currentMembers as $cm)
                                                                                                                        <div><strong>{{ $cm->member->user->name ?? 'Không rõ' }}</strong></div>
                                                                                                                        <div>MSSV: {{ $cm->member->student_code ?? '—' }}</div>
                                                                                                                        <div>Điện thoại: {{ $cm->member->phone ?? '—' }}</div>
                                                                                                                        <div>Email: {{ $cm->member->email ?? '—' }}</div>
                                                                                                                        <div>Khóa học: {{ $cm->member->course ?? '—' }}</div>
                                                                                                                        <div>Ngành học: {{ $cm->member->major ?? '—' }}</div>
                                                                                                                        <div>Ngày bổ nhiệm: {{ $cm->appointed_at ? \Carbon\Carbon::parse($cm->appointed_at)->format('d/m/Y') : '—' }}</div>
                                                                                                                        <hr>
                                                                                                                    @empty
                                                                                                                        <em>Chưa có</em>
                                                                                                                    @endforelse
                                                                                                                @endif
                                                                                                            </td>


                                                                                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                {{-- 🔹 Hành động duyệt/từ chối --}}
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
                            </div>
@endsection
