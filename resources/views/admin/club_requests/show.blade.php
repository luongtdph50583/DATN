@extends('admin.layouts.blank')

@section('title', 'Chi tiết yêu cầu CLB')

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
                    <div class="mb-2"><strong>Người tạo:</strong> {{ $request->user->name }} ({{ $request->user->email }})
                    </div>
                </div>
            </div>

            <hr>

            <!-- Form duyệt/từ chối -->
            <form action="{{ route('admin.club_requests.handle', $request->id) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Ghi chú của người xử lí (nếu có)</label>
                    <textarea name="note" class="form-control" rows="2">{{ old('note', $request->note) }}</textarea>
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <button type="submit" name="status" value="approved" class="btn btn-success">Duyệt</button>
                    <button type="submit" name="status" value="rejected" class="btn btn-danger">Từ chối</button>
                </div>
            </form>
        </div>
    </div>
@endsection