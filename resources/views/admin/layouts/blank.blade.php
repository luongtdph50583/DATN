{{-- resources/views/admin/layouts/blank-app.blade.php --}}
@extends('admin.layouts.app')


@php
// Disable các phần không cần
$noSidebar = true;
$noHeader = true;
$noFooter = true;
@endphp

@section('title', 'Trang trắng (Blank)')

{{-- Nội dung chính --}}
@section('card-body')
    @yield('card-body')
@endsection

{{-- Chèn JS riêng của trang --}}
@push('scripts')
@endpush
