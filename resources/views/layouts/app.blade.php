<!DOCTYPE html>
     <html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
     <head>
         <meta charset="utf-8">
         <meta name="viewport" content="width=device-width, initial-scale=1">
         <meta name="csrf-token" content="{{ csrf_token() }}">

         <title>{{ config('app.name', 'Laravel') }}</title>

         <!-- Fonts -->
         <link rel="stylesheet" href="https://fonts.bunny.net/css2?family=Nunito:wght@400;600;700&display=swap">

         <!-- Styles -->
         @vite(['resources/css/app.css', 'resources/js/app.js'])

         <!-- Scripts -->
         @livewireStyles
     </head>
     <body class="font-sans antialiased">
         <div class="min-h-screen bg-gray-100">
             <!-- Navigation -->
             <nav class="bg-white border-b border-gray-100">
                 <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                     <div class="flex justify-between h-16">
                         <div class="flex">
                             <div class="flex-shrink-0 flex items-center">
                                 <a href="{{ url('/') }}">
                                     <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                                 </a>
                             </div>
                         </div>

                         <div class="hidden sm:flex sm:items-center sm:ml-6">
                             <!-- User Dropdown -->
                             <x-dropdown align="right" width="48">
                                 <x-slot name="trigger">
                                     @if (Auth::check() && Auth::user()->avatar)
                                         <img src="{{ Storage::url(Auth::user()->avatar) }}" alt="Avatar" class="h-8 w-8 rounded-full object-cover">
                                     @else
                                         <span class="inline-flex rounded-md">
                                             <button type="button" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                                                 {{ Auth::check() ? Auth::user()->name : 'Tài khoản' }}
                                                 <svg class="ml-2 -mr-0.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                     <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                 </svg>
                                             </button>
                                         </span>
                                     @endif
                                 </x-slot>

                                 <x-slot name="content">
                                     @auth
                                         <x-dropdown-link href="{{ url('/admin/users') }}">
                                             {{ __('Quản lý người dùng') }}
                                         </x-dropdown-link>
                                         <form method="POST" action="{{ route('logout') }}">
                                             @csrf
                                             <x-dropdown-link :href="route('logout')" onclick="event.preventDefault(); this.closest('form').submit();">
                                                 {{ __('Đăng xuất') }}
                                             </x-dropdown-link>
                                         </form>
                                     @else
                                         <x-dropdown-link href="{{ route('login') }}">
                                             {{ __('Đăng nhập') }}
                                         </x-dropdown-link>
                                         @if (Route::has('register'))
                                             <x-dropdown-link href="{{ route('register') }}">
                                                 {{ __('Đăng ký') }}
                                             </x-dropdown-link>
                                         @endif
                                     @endauth
                                 </x-slot>
                             </x-dropdown>
                         </div>
                     </div>
                 </div>
             </nav>

             <!-- Page Content -->
             <main>
                 {{ $slot }}
             </main>
         </div>

         @livewireScripts
         <!-- Bootstrap JS and jQuery (for accordion) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

<!-- Custom scripts for SB Admin 2 -->
<script src="{{ asset('/vendor/jquery-easing/jquery.easing.min.js') }}"></script>
<script src="{{ asset('/js/sb-admin-2.min.js') }}"></script>
     </body>
     </html>