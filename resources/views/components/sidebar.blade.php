<div x-data="{ open: false }">
    <!-- Mobile sidebar -->
    <div x-show="open" class="relative z-50 lg:hidden" x-cloak>
        <!-- Backdrop -->
        <div x-show="open" 
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-900/80"
             @click="open = false"></div>

        <div class="fixed inset-0 flex">
            <div x-show="open"
                 x-transition:enter="transition ease-in-out duration-300 transform"
                 x-transition:enter-start="-translate-x-full"
                 x-transition:enter-end="translate-x-0"
                 x-transition:leave="transition ease-in-out duration-300 transform"
                 x-transition:leave-start="translate-x-0"
                 x-transition:leave-end="-translate-x-full"
                 class="relative mr-16 flex w-full max-w-xs flex-1">
                
                <!-- Close button -->
                <div class="absolute left-full top-0 flex w-16 justify-center pt-5">
                    <button type="button" @click="open = false" class="-m-2.5 p-2.5">
                        <span class="sr-only">Close sidebar</span>
                        <x-heroicon-o-x-mark class="h-6 w-6 text-white" />
                    </button>
                </div>

                <!-- Sidebar component -->
                <div class="flex grow flex-col gap-y-5 overflow-y-auto bg-white px-6 pb-4">
                    <div class="flex h-16 shrink-0 items-center gap-x-2">
                        @if(current_organization() && current_organization()->logo_path)
                            <img src="{{ current_organization()->getLogoUrl(36, 36) }}" 
                                 srcset="{{ current_organization()->getLogoSrcset(36) }}"
                                 alt="{{ current_organization()->name }} logo" 
                                 class="h-9 w-9 rounded-lg object-contain bg-gray-50">
                        @else
                            <div class="h-9 w-9 rounded-lg bg-gray-50 flex items-center justify-center">
                                <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                            </div>
                        @endif
                        <span class="text-lg font-semibold text-gray-900">{{ current_organization() ? current_organization()->name : config('app.name', 'Laravel') }}</span>
                    </div>
                    @include('components.sidebar-navigation')
                </div>
            </div>
        </div>
    </div>

    <!-- Static sidebar for desktop -->
    <div class="hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-72 lg:flex-col">
        <div class="flex grow flex-col gap-y-5 overflow-y-auto border-r border-gray-200 bg-white px-6 pb-4">
            <div class="flex h-16 shrink-0 items-center gap-x-2">
                @if(current_organization() && current_organization()->logo_path)
                    <img src="{{ current_organization()->getLogoUrl(36, 36) }}" 
                         srcset="{{ current_organization()->getLogoSrcset(36) }}"
                         alt="{{ current_organization()->name }} logo" 
                         class="h-9 w-9 rounded-lg object-contain bg-gray-50">
                @else
                    <div class="h-9 w-9 rounded-lg bg-gray-50 flex items-center justify-center">
                        <svg class="h-6 w-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                        </svg>
                    </div>
                @endif
                <span class="text-lg font-semibold text-gray-900">{{ current_organization() ? current_organization()->name : config('app.name', 'Laravel') }}</span>
            </div>
            @include('components.sidebar-navigation')
        </div>
    </div>

    <!-- Mobile menu button -->
    <div class="sticky top-0 z-40 flex items-center gap-x-6 bg-white px-4 py-4 shadow-sm sm:px-6 lg:hidden">
        <button type="button" @click="open = true" class="-m-2.5 p-2.5 text-gray-700 lg:hidden">
            <span class="sr-only">Open sidebar</span>
            <x-heroicon-o-bars-3 class="h-6 w-6" />
        </button>
        <div class="flex-1 text-sm font-semibold leading-6 text-gray-900">{{ current_organization() ? current_organization()->name : config('app.name', 'Laravel') }}</div>
        <a href="{{ route('profile.edit') }}">
            <span class="sr-only">Your profile</span>
            <x-user-avatar />
        </a>
    </div>
</div>