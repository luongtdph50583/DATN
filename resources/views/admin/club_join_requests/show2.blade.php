@extends('admin.layouts.app')

@section('title', 'Chi tiết yêu cầu tham gia CLB')
@section('card-title', 'Chi tiết yêu cầu tham gia CLB')
@section('card-header', 'Thông tin xử lý')

@section('card-body')
    @include('admin.club_join_requests.partials.detail', [
        'request' => $request,
        'timeline' => $timeline,
        'interviewers' => $interviewers,
        'membership' => $membership,
    ])
@endsection
