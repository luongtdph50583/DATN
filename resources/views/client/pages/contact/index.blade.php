@extends('client.layouts.app')
@section('content')
    <!--banner-->
    <!--end banner-->
    @include('client.pages.contact.banner')

    <!-- contact Info Section Start -->
    <!--end contact info-->
    @include('client.pages.contact.contact-info')

    <!-- contact Box Section Start -->
    <!--end contact box-->
    @include('client.pages.contact.contact-box')

@endsection
