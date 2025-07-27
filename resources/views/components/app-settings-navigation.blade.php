@php
    $currentRoute = request()->route()->getName();
@endphp

<div class="border-b border-gray-200">
    <nav class="flex overflow-x-auto py-4" aria-label="Tabs">
        <ul role="list" class="flex min-w-full flex-none gap-x-6 px-4 text-sm font-semibold text-gray-400 sm:px-6 lg:px-8">
            <li>
                <a href="{{ route('app-settings.index') }}" 
                   class="{{ $currentRoute === 'app-settings.index' ? 'text-indigo-600' : 'text-gray-500 hover:text-gray-700' }}">
                    General
                </a>
            </li>
            <li>
                <a href="{{ route('app-settings.users.index') }}" 
                   class="{{ str_starts_with($currentRoute, 'app-settings.users') ? 'text-indigo-600' : 'text-gray-500 hover:text-gray-700' }}">
                    Users
                </a>
            </li>
            <li>
                <a href="{{ route('app-settings.integrations.index') }}" 
                   class="{{ $currentRoute === 'app-settings.integrations.index' ? 'text-indigo-600' : 'text-gray-500 hover:text-gray-700' }}">
                    Integrations
                </a>
            </li>
        </ul>
    </nav>
</div>