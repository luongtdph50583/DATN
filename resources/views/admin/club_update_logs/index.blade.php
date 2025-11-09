@extends('admin.layouts.app')

@section('title', 'Danh sách yêu cầu thay đổi CLB')
@section('card-title', 'Danh sách đề xuất CLB')
@section('card-header')
    Danh sách
@endsection

@section('card-body')
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($proposals->isEmpty())
        <p>Hiện tại không có đề xuất nào chờ duyệt.</p>
    @else
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>CLB</th>
                    <th>Người gửi</th>
                    <th>Ngày gửi</th>
                    <th>Trạng thái</th>
                    <th>Hành động</th>
                </tr>
            </thead>
            <tbody>
                @foreach($proposals as $proposal)
                    <tr>
                        <td>{{ $proposal->id }}</td>
                        <td>{{ $proposal->club->name }}</td>
                        <td>{{ $proposal->proposer->name }}</td>
                        <td>{{ $proposal->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ ucfirst($proposal->status) }}</td>
                        <td>
                            <a href="{{ route('admin.club_update_logs.show', $proposal) }}" class="btn btn-sm btn-primary">Xem chi
                                tiết</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@endsection
