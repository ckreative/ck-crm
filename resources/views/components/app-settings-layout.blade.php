@props(['title' => null])

<x-app-layout>
    <main>
        <h1 class="sr-only">App Settings</h1>

        <header class="border-b border-gray-200 bg-gray-100">
            <!-- Secondary navigation -->
            <nav class="flex overflow-x-auto py-4">
                <ul role="list" class="flex min-w-full flex-none gap-x-6 px-4 text-sm font-semibold sm:px-6 lg:px-8">
                    <li>
                        <a href="{{ route('app-settings.users.index') }}" 
                           class="{{ request()->routeIs('app-settings.users.*') ? 'text-indigo-600 border-b-2 border-indigo-600 pb-3' : 'text-gray-500 hover:text-gray-700 pb-3' }}">
                            Users
                        </a>
                    </li>
                    <!-- Add more navigation items here as needed -->
                </ul>
            </nav>
        </header>

        <!-- Settings content -->
        <div class="mx-auto max-w-7xl px-4 py-8 sm:px-6 lg:px-8">
            {{ $slot }}
        </div>
    </main>
</x-app-layout>