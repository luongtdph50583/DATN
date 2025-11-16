@extends('admin.layouts.app')

@section('title', 'Chi tiết yêu cầu cấp kinh phí')
@section('card-title', 'Chi tiết yêu cầu cấp kinh phí')

@section('card-body')
<div class="container mt-3">

    {{-- Thông tin yêu cầu --}}
    <div class="card mb-4 shadow-sm">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">Thông tin yêu cầu cấp kinh phí</h5>
        </div>
        <div class="card-body">
            <table class="table table-bordered mb-0">
                <tr>
                    <th width="30%">Sự kiện</th>
                    <td>{{ $request->event->name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Câu lạc bộ</th>
                    <td>{{ $request->event->club->name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Người yêu cầu</th>
                    <td>{{ $request->requestedBy->name ?? 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Số tiền yêu cầu</th>
                    <td>{{ number_format($request->amount_requested, 0, ',', '.') }} VNĐ</td>
                </tr>
                <tr>
                    <th>Số tiền đã duyệt</th>
                    <td>{{ isset($request->approved_amount) ? number_format($request->approved_amount, 0, ',', '.') . ' VNĐ' : 'Chưa duyệt' }}</td>
                </tr>
                <tr>
                    <th>Số tiền đã giải ngân</th>
                    <td>{{ number_format($request->amount_disbursed ?? 0, 0, ',', '.') }} VNĐ</td>
                </tr>
                <tr>
                    <th>Thời gian giải ngân</th>
                    <td>
                        @if($request->disbursement_start && $request->disbursement_end)
                            {{ \Carbon\Carbon::parse($request->disbursement_start)->format('d/m/Y') }} 
                            - 
                            {{ \Carbon\Carbon::parse($request->disbursement_end)->format('d/m/Y') }}
                        @else
                            -
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Trạng thái</th>
                    <td>
                        @switch($request->status)
                            @case('pending_disbursement')
                                <span class="badge bg-warning text-dark">Chờ giải ngân</span>
                                @break
                            @case('disbursing')
                                <span class="badge bg-info text-dark">Đang giải ngân</span>
                                @break
                            @case('disbursed')
                                <span class="badge bg-success">Đã giải ngân</span>
                                @break
                            @case('rejected')
                                <span class="badge bg-danger">Từ chối</span>
                                @break
                            @default
                                <span class="badge bg-secondary">N/A</span>
                        @endswitch
                    </td>
                </tr>
                <tr>
                    <th>Minh chứng / Lịch sử giải ngân</th>
                    <td>
                        @if($request->disbursement_history)
                            @php $history = json_decode($request->disbursement_history, true); @endphp
                            @foreach($history as $idx => $h)
                                <div class="mb-2 p-2 border rounded">
                                    <strong>Lần {{ $idx + 1 }}:</strong><br>
                                    Số tiền: {{ number_format($h['amount'], 0, ',', '.') }} VNĐ<br>
                                    Người giải ngân: {{ $h['disbursed_by_name'] }}<br>
                                    Ngày: {{ \Carbon\Carbon::parse($h['date'])->format('d/m/Y H:i') }}<br>
                                    @if(!empty($h['proof']))
                                        Minh chứng: 
                                        @foreach($h['proof'] as $pidx => $file)
                                            <a href="{{ asset('storage/' . $file) }}" target="_blank">Xem {{ $pidx + 1 }}</a>@if(!$loop->last), @endif
                                        @endforeach
                                    @endif
                                </div>
                            @endforeach
                        @elseif($request->disbursement_proof)
                            @php $files = json_decode($request->disbursement_proof, true); @endphp
                            @foreach($files as $fidx => $file)
                                <a href="{{ asset('storage/' . $file) }}" target="_blank">Xem minh chứng {{ $fidx + 1 }}</a><br>
                            @endforeach
                        @else
                            -
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Ghi chú</th>
                    <td>{{ $request->note ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Ngày tạo</th>
                    <td>{{ $request->created_at->format('d/m/Y H:i') }}</td>
                </tr>
                <tr>
                    <th>Cập nhật lần cuối</th>
                    <td>{{ $request->updated_at->format('d/m/Y H:i') }}</td>
                </tr>
            </table>
        </div>
    </div>

    {{-- Thông tin chi tiết sự kiện --}}
    @if($request->event)
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0">Thông tin chi tiết sự kiện</h5>
            </div>
            <div class="card-body">
                <table class="table table-bordered mb-0">
                    <tr>
                        <th width="30%">Tên sự kiện</th>
                        <td>{{ $request->event->name }}</td>
                    </tr>
                    <tr>
                        <th>Mô tả</th>
                        <td>{{ $request->event->description ?? 'Không có' }}</td>
                    </tr>
                    <tr>
                        <th>Câu lạc bộ tổ chức</th>
                        <td>{{ $request->event->club->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Thời gian</th>
                        <td>
                            {{ \Carbon\Carbon::parse($request->event->start_time)->format('d/m/Y H:i') }}
                            -
                            {{ \Carbon\Carbon::parse($request->event->end_time)->format('d/m/Y H:i') }}
                        </td>
                    </tr>
                    <tr>
                        <th>Địa điểm</th>
                        <td>{{ $request->event->location ?? '-' }}</td>
                    </tr>
                    <tr>
                        <th>Ngân sách dự kiến</th>
                        <td>{{ number_format($request->event->budget_estimated, 0, ',', '.') }} VNĐ</td>
                    </tr>
                    <tr>
                        <th>Người tạo</th>
                        <td>{{ $request->event->createdBy->name ?? 'N/A' }}</td>
                    </tr>
                    <tr>
                        <th>Trạng thái sự kiện</th>
                        <td>
                            @switch($request->event->status)
                                @case('pending')
                                    <span class="badge bg-warning text-dark">Chờ duyệt</span>
                                    @break
                                @case('approved')
                                    <span class="badge bg-success">Đã duyệt</span>
                                    @break
                                @case('rejected')
                                    <span class="badge bg-danger">Từ chối</span>
                                    @break
                                @default
                                    <span class="badge bg-secondary">N/A</span>
                            @endswitch
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    @endif

    <div class="mt-4">
        <a href="{{ route('admin.event_fund_requests.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Quay lại
        </a>
    </div>
</div>
@endsection
