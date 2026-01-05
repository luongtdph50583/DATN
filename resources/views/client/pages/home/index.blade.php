@extends('client.layouts.app')
@section('content')
    <!-- Hero Section Start (banner)-->
    <!--end banner-->
    @include('client.pages.home.banner')


    <!-- Feature Progress Section Start (video) -->
    <!--end video-->
    @include('client.pages.home.video')





    <!-- Team Section Start -->
    <!--team-->
    @include('client.pages.home.team')


    <!-- Basketball Leagues Section Start -->
    <!--end basketball leagues-->
    @include('client.pages.home.basketball_leagues')


    <!-- Sponsor Section Start -->
    <!--end sponor-->
    @include('client.pages.home.sponsor')


    <!-- Hottest Hoops Section Start -->
    <!--end the hotteset hoops headlines-->
    @include('client.pages.home.the_hottest_hoops_headlines')

@endsection
