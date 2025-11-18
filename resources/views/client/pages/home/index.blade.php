@extends('client.layouts.app')
@section('content')
    <!-- Hero Section Start (banner)-->
    <!--end banner-->
        @include('client.page.home.banner')


    <!-- Feature Progress Section Start (video) -->
    <!--end video-->
        @include('client.page.home.video')


    <!-- News Section Start -->
    <!--end news-->
        @include('client.page.home.news')


    <!-- Playing Ranking Section Start -->
    <!--end play ranking-->
        @include('client.page.home.news')


    <!-- Team Section Start -->
    <!--team-->
            @include('client.page.home.team')


    <!-- Basketball Leagues Section Start -->
    <!--end basketball leagues-->
                @include('client.page.home.basketball_leagues')


    <!-- Sponsor Section Start -->
    <!--end sponor-->
                    @include('client.page.home.sponsor')


    <!-- Hottest Hoops Section Start -->
    <!--end the hotteset hoops headlines-->
                        @include('client.page.home.the_hottest_hoops_headlines')

@endsection
