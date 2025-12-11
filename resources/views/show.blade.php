@extends('client.layouts.app')

@section('title', 'Chi tiết đơn thành lập CLB')

@section('content')
    <div class="container py-4">
        <div class="row">
            <div class="col-lg-10 mx-auto">

                {{-- Header --}}
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h3 class="mb-2">{{ $clubRequest->name }}</h3>
                                <p class="text-muted mb-0">
                                    <i class="fas fa-quote-left me-2"></i>{{ $clubRequest->slogan }}
                                </p>
                            </div>
                            <div>
                                @if($clubRequest->status === 'pending')
                                    <span class="badge bg-warning">Chờ xác nhận</span>
                                @elseif($clubRequest->status === 'confirmed')
                                    <span class="badge bg-info">Đã xác nhận</span>
                                @elseif($clubRequest->status === 'approved')
                                    <span class="badge bg-success">Đã phê duyệt</span>
                                @elseif($clubRequest->status === 'rejected')
                                    <span class="badge bg-danger">Đã từ chối</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Thông tin cơ bản --}}
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <i class="fas fa-info-circle me-2"></i>Thông tin cơ bản
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <strong>Lĩnh vực:</strong><br>
                                {{ $clubRequest->field }}
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Giới hạn thành viên:</strong><br>
                                {{ $clubRequest->member_limit }} người
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Email:</strong><br>
                                {{ $clubRequest->email }}
                            </div>
                            <div class="col-md-6 mb-3">
                                <strong>Số điện thoại:</strong><br>
                                {{ $clubRequest->phone }}
                            </div>
                            <div class="col-12 mb-3">
                                <strong>Mô tả:</strong><br>
                                {{ $clubRequest->description }}
                            </div>
                            <div class="col-12 mb-3">
                                <strong>Mục đích:</strong><br>
                                {{ $clubRequest->purpose }}
                            </div>
                            <div class="col-12">
                                <strong>Quy tắc:</strong><br>
                                {{ $clubRequest->rule }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Ban chủ nhiệm --}}
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-header bg-warning">
                        <i class="fas fa-user-tie me-2"></i>Ban chủ nhiệm
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-2">
                                <strong>Chủ nhiệm:</strong><br>
                                {{ $clubRequest->clubManager->name ?? 'Chưa có' }}
                            </div>
                            <div class="col-md-6 mb-2">
                                <strong>Phó chủ nhiệm:</strong><br>
                                {{ $clubRequest->deputyManager->name ?? 'Chưa có' }}
                            </div>
                            <div class="col-md-6 mb-2">
                                <strong>Thư ký:</strong><br>
                                {{ $clubRequest->secretary->name ?? 'Chưa có' }}
                            </div>
                            <div class="col-md-6 mb-2">
                                <strong>Thủ quỹ:</strong><br>
                                {{ $clubRequest->treasurer->name ?? 'Chưa có' }}
                            </div>
                            <div class="col-md-6 mb-2">
                                <strong>Quản lý sự kiện:</strong><br>
                                {{ $clubRequest->eventManager->name ?? 'Chưa có' }}
                            </div>
                            <div class="col-md-6 mb-2">
                                <strong>Phụ trách truyền thông:</strong><br>
                                {{ $clubRequest->communication->name ?? 'Chưa có' }}
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Thành viên --}}
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-header bg-info text-white">
                        <i class="fas fa-users me-2"></i>
                        Thành viên ban đầu ({{ $clubRequest->members->count() }})
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover">
                                <thead>
                                    <tr>
                                        <th width="5%">STT</th>
                                        <th width="40%">Họ tên</th>
                                        <th width="25%">MSSV</th>
                                        <th width="30%">Trạng thái</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($clubRequest->members as $index => $member)
                                        @php
                                            $confirmation = $clubRequest->confirmations->where('user_id', $member->id)->first();
                                        @endphp
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $member->name }}</td>
                                            <td>{{ $member->student->student_code ?? 'N/A' }}</td>
                                            <td>
                                                @if($confirmation && $confirmation->status)
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check me-1"></i>Đã xác nhận
                                                    </span>
                                                    <br>
                                                    <small
                                                        class="text-muted">{{ $confirmation->confirmed_at->format('d/m/Y H:i') }}</small>
                                                @else
                                                    <span class="badge bg-warning">
                                                        <i class="fas fa-clock me-1"></i>Chờ xác nhận
                                                    </span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- Phê duyệt --}}
                @if($clubRequest->approvals->isNotEmpty())
                    <div class="card mb-4 border-0 shadow-sm">
                        <div class="card-header bg-success text-white">
                            <i class="fas fa-check-circle me-2"></i>Lịch sử phê duyệt
                        </div>
                        <div class="card-body">
                            @foreach($clubRequest->approvals as $approval)
                                <div class="mb-3 pb-3 border-bottom">
                                    <div class="d-flex justify-content-between">
                                        <div>
                                            <strong>{{ $approval->admin->name }}</strong>
                                            @if($approval->status === 'approved')
                                                <span class="badge bg-success ms-2">Đã phê duyệt</span>
                                            @else
                                                <span class="badge bg-danger ms-2">Từ chối</span>
                                            @endif
                                        </div>
                                        <small class="text-muted">{{ $approval->approved_at->format('d/m/Y H:i') }}</small>
                                    </div>
                                    @if($approval->comment)
                                        <p class="mt-2 mb-0 text-muted">
                                            <i class="fas fa-comment me-2"></i>{{ $approval->comment }}
                                        </p>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Actions --}}
                <div class="d-flex gap-2">
                    <a href="{{ url()->previous() }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left me-2"></i>Quay lại
                    </a>

                    @if($clubRequest->status === 'approved' && $clubRequest->pdf_file)
                        <a href="{{ route('club_requests.download_pdf', $clubRequest) }}" class="btn btn-primary">
                            <i class="fas fa-download me-2"></i>Tải PDF
                        </a>
                        <a href="{{ route('club_requests.view_pdf', $clubRequest) }}" class="btn btn-outline-primary"
                            target="_blank">
                            <i class="fas fa-eye me-2"></i>Xem PDF
                        </a>
                    @endif
                </div>

            </div>
        </div>
    </div>
@endsection
