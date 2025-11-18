@extends('admin.layouts.app')

@section('title', 'Chi tiết yêu cầu rời CLB')
@section('card-header')
    Chi tiết yêu cầu rời CLB
@endsection

@section('card-body')
    @include('admin.club_leave_requests.show', ['request' => $request])
@endsection