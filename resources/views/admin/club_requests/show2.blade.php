@extends('admin.layouts.app')

@section('title', 'Chi tiết yêu cầu CLB')
@section('card-title', 'Chi tiết yêu cầu thành lập CLB')

@section('card-body')
    <div class="card">
        <div class="card-body">
            <h5 class="card-title mb-4">Chi tiết yêu cầu thành lập CLB</h5>

            <div class="row">
                <!-- Logo bên trái -->
                @if($request->logo)
                    <div class="col-md-3 text-center mb-3">
                        <img src="{{ asset('storage/' . $request->logo) }}" class="img-fluid rounded" style="max-height:150px;">
                    </div>
                @endif

                <!-- Thông tin CLB bên phải -->
                <div class="col-md-{{ $request->logo ? '9' : '12' }}">
                    <div class="mb-2"><strong>Tên CLB:</strong> {{ $request->name }}</div>
                    <div class="mb-2"><strong>Lĩnh vực:</strong> {{ $request->field }}</div>
                    <div class="mb-2"><strong>Mô tả:</strong> {!! $request->description !!}</div>
                    <div class="mb-2"><strong>Email:</strong> {{ $request->email }}</div>
                    <div class="mb-2"><strong>Điện thoại:</strong> {{ $request->phone }}</div>
                    <div class="mb-2"><strong>Người tạo:</strong> {{ $request->user->name ?? '—' }}
                        ({{ $request->user->email ?? '—' }})</div>
                    <div class="mb-2"><strong>Trạng thái:</strong>
                        @if($request->status === 'pending')
                            <span class="badge bg-warning">Chờ duyệt</span>
                        @elseif($request->status === 'approved')
                            <span class="badge bg-success">Đã duyệt</span>
                        @elseif($request->status === 'rejected')
                            <span class="badge bg-danger">Đã từ chối</span>
                        @endif
                    </div>

                    <div class="mb-2"><strong>Ghi chú:</strong>
                        @if(!empty($request->note))
                            {{ $request->note }}
                        @else
                            <span class="text-muted">Không có ghi chú</span>
                        @endif
                    </div>


                <div class="mb-2"><strong>Ghi chú:</strong></div>
                <textarea class="form-control" rows="4" readonly>{{ $request->note ?? 'Không có ghi chú' }}</textarea>

                    <div class="mb-2"><strong>Người xử lý:</strong>
                        {{ $request->handler->name ?? '—' }}
                        @if($request->handler)
                            ({{ $request->handler->email }})
                        @endif
                    </div>

                    <div class="mb-2"><strong>Ngày tạo:</strong> {{ $request->created_at->format('d/m/Y H:i') }}</div>
                    <div class="mb-2"><strong>Ngày cập nhật:</strong> {{ $request->updated_at->format('d/m/Y H:i') }}</div>
                </div>
            </div>
        </div>
    </div>
@endsection