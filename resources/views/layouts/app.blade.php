<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-white">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Favicons -->
        @php
            $currentOrg = current_organization();
        @endphp
        @if($currentOrg && $currentOrg->logo_path)
            <!-- Organization-specific favicons -->
            <link rel="shortcut icon" href="{{ route('organization.favicon', $currentOrg->slug) }}">
            <link rel="icon" type="image/png" sizes="16x16" href="{{ route('organization.favicon.sized', [$currentOrg->slug, 'size' => 16]) }}">
            <link rel="icon" type="image/png" sizes="32x32" href="{{ route('organization.favicon.sized', [$currentOrg->slug, 'size' => 32]) }}">
            <link rel="icon" type="image/png" sizes="48x48" href="{{ route('organization.favicon.sized', [$currentOrg->slug, 'size' => 48]) }}">
            
            <!-- Apple Touch Icons -->
            <link rel="apple-touch-icon" sizes="180x180" href="{{ route('organization.favicon.sized', [$currentOrg->slug, 'size' => 180]) }}">
            
            <!-- Android/Chrome -->
            <link rel="icon" type="image/png" sizes="192x192" href="{{ route('organization.favicon.sized', [$currentOrg->slug, 'size' => 192]) }}">
            <link rel="icon" type="image/png" sizes="512x512" href="{{ route('organization.favicon.sized', [$currentOrg->slug, 'size' => 512]) }}">
        @else
            <!-- Default favicon -->
            <link rel="icon" type="image/x-icon" href="/favicon.ico">
        @endif

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="h-full">
        <div class="min-h-screen bg-gray-100">
            @if(!isset($hideSidebar) || !$hideSidebar)
                <x-sidebar />
            @endif

            <div class="{{ (!isset($hideSidebar) || !$hideSidebar) ? 'lg:pl-72' : '' }}">
                <div class="sticky top-0 z-40 flex h-16 shrink-0 items-center gap-x-4 border-b border-gray-200 bg-gray-100 px-4 sm:gap-x-6 sm:px-6 lg:px-8">
                    @if(isset($hideSidebar) && $hideSidebar)
                        <!-- Logo -->
                        <div class="flex items-center gap-x-2">
                            <svg class="h-8 w-8 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            <span class="text-xl font-semibold text-gray-900">{{ config('app.name', 'Laravel') }}</span>
                        </div>
                    @else
                        <!-- Separator -->
                        <div aria-hidden="true" class="h-6 w-px bg-gray-200 lg:hidden"></div>
                    @endif

                    <div class="flex flex-1 gap-x-4 self-stretch lg:gap-x-6">
                        <div class="flex-1"></div>
                        <div class="flex items-center gap-x-4 lg:gap-x-6">
                            @if(!isset($hideSidebar) || !$hideSidebar)
                                <!-- Organization Switcher -->
                                <x-organization-switcher />
                            @endif

                            <button type="button" class="-m-2.5 p-2.5 text-gray-400 hover:text-gray-500">
                                <span class="sr-only">View notifications</span>
                                <x-heroicon-o-bell class="h-6 w-6" />
                            </button>

                            <!-- Separator -->
                            <div aria-hidden="true" class="hidden lg:block lg:h-6 lg:w-px lg:bg-gray-200"></div>

                            <!-- Profile dropdown -->
                            <div class="relative" x-data="{ open: false }">
                                <button type="button" 
                                        @click="open = !open"
                                        class="-m-1.5 flex items-center p-1.5">
                                    <span class="sr-only">Open user menu</span>
                                    <x-user-avatar />
                                    <span class="hidden lg:flex lg:items-center">
                                        <span aria-hidden="true" class="ml-4 text-sm font-semibold leading-6 text-gray-900">{{ Auth::user()->name }}</span>
                                        <x-heroicon-m-chevron-down class="ml-2 h-5 w-5 text-gray-400" />
                                    </span>
                                </button>

                                <!-- Dropdown menu -->
                                <div x-show="open" 
                                     x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="transform opacity-0 scale-95"
                                     x-transition:enter-end="transform opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="transform opacity-100 scale-100"
                                     x-transition:leave-end="transform opacity-0 scale-95"
                                     @click.away="open = false"
                                     x-cloak
                                     class="absolute right-0 z-10 mt-2.5 w-32 origin-top-right rounded-md bg-white py-2 shadow-lg ring-1 ring-gray-900/5 focus:outline-none">
                                    <a href="{{ route('profile.edit') }}" class="block px-3 py-1 text-sm leading-6 text-gray-900 hover:bg-gray-50">Your profile</a>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="block w-full text-left px-3 py-1 text-sm leading-6 text-gray-900 hover:bg-gray-50">
                                            Sign out
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                @if(isset($navigation))
                    {{ $navigation }}
                @endif

                <main class="py-10">
                    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                        @if (session('success'))
                            <div class="mb-4 rounded-md bg-green-50 p-4">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-green-400" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-green-800">
                                            {{ session('success') }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        @endif
                        {{ $slot }}
                    </div>
                </main>
            </div>
        </div>
    </body>
</html>
