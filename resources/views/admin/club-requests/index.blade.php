@extends('admin.layouts.app')

@section('title', 'Danh sách yêu cầu CLB')

@section('card-body')
@php
    use Illuminate\Support\Str;
@endphp

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Danh sách yêu cầu CLB</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <table class="table table-hover table-bordered align-middle">
        <thead class="thead-dark">
            <tr class="text-center">
                <th>#</th>
                <th>Logo</th>
                <th>Người tạo</th>
                <th>Tên CLB</th>
                <th>Mô tả</th>
                <th>Lĩnh vực</th>
                <th>Trạng thái</th>
                <th>Ngày tạo</th>
                <th>Hành động</th>
            </tr>
        </thead>
        <tbody>
            @foreach($requests as $request)
            <tr class="text-center">
                <td>{{ $request->id }}</td>

                <!-- Thumbnail -->
                <td>
                    @if($request->logo)
                        <img src="{{ asset('storage/' . $request->logo) }}" 
                            alt="Logo"
                            style="width: 45px; height: 45px; object-fit: cover;"
                            class="rounded shadow">
                    @else
                        <span class="text-muted"><em>Không logo</em></span>
                    @endif
                </td>

                <td>{{ $request->user->name ?? 'N/A' }}</td>
                <td><strong>{{ $request->name }}</strong></td>

                <!-- Mô tả rút gọn -->
                <td>{{ Str::limit($request->description, 40) }}</td>

                <td>{{ $request->field }}</td>

                <!-- Badge trạng thái -->
                <td>
                   @php
    $class = [
        'pending' => 'badge-custom badge-pending',
        'approved' => 'badge-custom badge-approved',
        'rejected' => 'badge-custom badge-rejected'
    ][$request->status] ?? 'badge-custom';
    $label = [
        'pending' => '⏳ Chờ duyệt',
        'approved' => '✅ Đã duyệt',
        'rejected' => '❌ Bị từ chối'
    ][$request->status] ?? 'Không xác định';
@endphp

<span class="{{ $class }}">
    {{ $label }}
</span>
                </td>

                <td>{{ $request->created_at->format('d/m/Y H:i') }}</td>

                <!-- Hành động -->
                <td style="min-width: 150px;">
                    <a href="{{ route('admin.club-requests.show', $request->id) }}" 
                       class="btn btn-info btn-sm mb-1 w-100">
                        👁 Xem
                    </a>

                    <form action="{{ route('admin.club-requests.updateStatus', $request->id) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        <select name="status" class="form-control mb-1">
                            <option value="pending" @selected($request->status=='pending') >Pending</option>
                            <option value="approved" @selected($request->status=='approved')>Approved</option>
                            <option value="rejected" @selected($request->status=='rejected')>Rejected</option>
                        </select>
                        <button type="submit" class="btn btn-primary btn-sm w-100">
                            ✅ Cập nhật
                        </button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Phân trang -->
    <div class="mt-3">
        {{ $requests->links() }}
    </div>

</div>
@endsection
<style>
    .badge-custom {
    display: inline-block;
    font-size: 12px;
    font-weight: bold;
    padding: 6px 10px;
    border-radius: 8px;
    text-transform: capitalize;
}

/* Chờ duyệt */
.badge-pending {
    background-color: #ffd08a; /* cam nhạt */
    color: #7a4600;
    border: 1px solid #ffb44d;
}

/* Đã duyệt */
.badge-approved {
    background-color: #b4f0d0; /* xanh mint */
    color: #085c34;
    border: 1px solid #2ecc71;
}

/* Bị từ chối */
.badge-rejected {
    background-color: #ffb3b8; /* đỏ pastel */
    color: #7a1a1a;
    border: 1px solid #e74c3c;
}

</style>