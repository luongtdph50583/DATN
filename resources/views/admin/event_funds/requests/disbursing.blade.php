@extends('admin.layouts.app')

@section('title', 'Đang giải ngân')

@section('card-body')
<div class="container py-4">
    <div class="card shadow">
        <div class="card-header bg-info text-white text-center">
            <h5><i class="fas fa-money-bill-wave me-2"></i>Đang giải ngân: {{ $request->event->name }}</h5>
        </div>

        <div class="card-body">

            <!-- Thông tin cơ bản -->
            <table class="table table-bordered mb-4">
                <tr>
                    <th>ID</th>
                    <td>{{ $request->id }}</td>
                </tr>
                <tr>
                    <th>Sự kiện</th>
                    <td>{{ $request->event->name }}</td>
                </tr>
                <tr>
                    <th>Người yêu cầu</th>
                    <td>{{ $request->requestedBy->name }}</td>
                </tr>
                <tr>
                    <th>Số tiền duyệt</th>
                    <td>{{ number_format($request->approved_amount) }} đ</td>
                </tr>
                <tr>
                    <th>Số tiền đã giải ngân</th>
                    <td>{{ number_format($request->amount_disbursed) }} đ</td>
                </tr>
                <tr>
                    <th>Trạng thái</th>
                    <td>
                        @switch($request->status)
                            @case('pending_disbursement')<span class="badge bg-warning text-dark">Chờ giải ngân</span>@break
                            @case('disbursing')<span class="badge bg-info text-white">Đang giải ngân</span>@break
                            @case('disbursed')<span class="badge bg-success text-white">Đã giải ngân</span>@break
                            @case('rejected')<span class="badge bg-danger text-white">Từ chối</span>@break
                        @endswitch
                    </td>
                </tr>
            </table>

            <!-- Form cập nhật giải ngân -->
          <form action="{{ route('admin.event_fund_requests.updateDisbursement', $request->id) }}" method="POST" enctype="multipart/form-data">
    @csrf
    <div class="mb-3">
        <label>Số tiền giải ngân lần này</label>
        <input type="number" name="disbursement_amount" class="form-control" min="0" max="{{ $request->approved_amount - $request->amount_disbursed }}" step="1000" required>
    </div>
    <div class="mb-3">
        <label>Minh chứng giải ngân</label>
        <input type="file" name="disbursement_proof[]" class="form-control" multiple>
    </div>
    <div class="d-flex gap-2">
        <!-- Cập nhật lần này -->
        <button type="submit" name="action" value="update" class="btn btn-warning">
            <i class="fas fa-save me-2"></i>Cập nhật giải ngân
        </button>

        <!-- Hoàn tất -->
        <button type="submit" name="action" value="complete" class="btn btn-success">
            <i class="fas fa-check me-2"></i>Hoàn thành giải ngân
        </button>

        <a href="{{ route('admin.event_fund_requests.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-2"></i>Quay lại
        </a>
    </div>
</form>


            <!-- Lịch sử giải ngân -->
            @if($request->disbursement_history)
                <h6 class="mt-4">Lịch sử giải ngân</h6>
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Lần</th>
                            <th>Số tiền</th>
                            <th>Người giải ngân</th>
                            <th>Ngày giải ngân</th>
                            <th>Minh chứng</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach(json_decode($request->disbursement_history, true) as $idx => $h)
                        <tr>
                            <td>{{ $idx + 1 }}</td>
                            <td>{{ number_format($h['amount']) }} đ</td>
                            <td>{{ $h['disbursed_by_name'] }}</td>
                            <td>{{ \Carbon\Carbon::parse($h['date'])->format('d/m/Y H:i') }}</td>
                            <td>
                                @if(!empty($h['proof']))
                                    @foreach($h['proof'] as $file)
                                        <a href="{{ asset('storage/'.$file) }}" target="_blank">Xem</a>@if(!$loop->last), @endif
                                    @endforeach
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif

        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endsection
