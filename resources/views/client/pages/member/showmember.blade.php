@extends('client.layouts.app')
@section('title', $club->name)

@section('content')
<div class="container mt-4">
    <h3>Danh sách thành viên CLB: {{ $club->name }}</h3>

    <div class="mb-3">
        <input type="text" id="search-member" class="form-control" placeholder="Tìm kiếm theo tên hoặc mã sinh viên...">
    </div>

    <div id="members-table">
        @include('client.pages.member.partials.members_table', ['members' => $members])
    </div>
</div>
@section('scripts')
<script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
<script>
$(document).ready(function() {
    $('#search-member').on('keyup', function() {
        let query = $(this).val();
        $.ajax({
            url: "{{ route('club_manager.members.search', ['club' => $club->id]) }}",
            type: "GET",
            data: { query: query },
            success: function(data) {
                $('#members-table').html(data);
            },
            error: function(xhr, status, error) {
                console.error(error); // xem lỗi nếu có
            }
        });
    });
});
</script>
@endsection

@endsection

