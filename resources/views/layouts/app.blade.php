<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-white">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script>
            // Ensure Alpine is available globally
            document.addEventListener('alpine:init', () => {
                console.log('Alpine initialized');
            });
        </script>
    </head>
    <body class="h-full">
        <div class="min-h-screen bg-gray-100">
            <x-sidebar />

            <div class="lg:pl-72">
                <div class="sticky top-0 z-40 flex h-16 shrink-0 items-center gap-x-4 border-b border-gray-200 bg-gray-100 px-4 sm:gap-x-6 sm:px-6 lg:px-8">
                    <!-- Separator -->
                    <div aria-hidden="true" class="h-6 w-px bg-gray-200 lg:hidden"></div>

                    <div class="flex flex-1 gap-x-4 self-stretch lg:gap-x-6">
                        <form action="#" method="GET" class="relative flex flex-1">
                            <label for="search-field" class="sr-only">Search</label>
                            <x-heroicon-o-magnifying-glass class="pointer-events-none absolute inset-y-0 left-0 h-full w-5 text-gray-400" />
                            <input id="search-field" 
                                   class="block h-full w-full border-0 bg-gray-100 py-0 pl-8 pr-0 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm" 
                                   placeholder="Search..." 
                                   type="search" 
                                   name="search">
                        </form>
                        <div class="flex items-center gap-x-4 lg:gap-x-6">
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

                <main class="py-10">
                    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                        {{ $slot }}
                    </div>
                </main>
            </div>
        </div>
    </body>
</html>
