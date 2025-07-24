<nav class="flex flex-1 flex-col">
    <ul role="list" class="flex flex-1 flex-col gap-y-7">
        <li>
            <ul role="list" class="-mx-2 space-y-1">
                <li>
                    <a href="{{ route('dashboard') }}" 
                       class="{{ request()->routeIs('dashboard') ? 'bg-gray-50 text-indigo-600' : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50' }} group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold">
                        <x-heroicon-o-home class="{{ request()->routeIs('dashboard') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-indigo-600' }} h-6 w-6 shrink-0" />
                        Dashboard
                    </a>
                </li>
                @if(auth()->user()?->isAdmin())
                <li>
                    <a href="{{ route('leads.index') }}" 
                       class="{{ request()->routeIs('leads.*') ? 'bg-gray-50 text-indigo-600' : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50' }} group flex gap-x-3 rounded-md p-2 text-sm leading-6 font-semibold">
                        <x-heroicon-o-users class="{{ request()->routeIs('leads.*') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-indigo-600' }} h-6 w-6 shrink-0" />
                        Leads
                    </a>
                </li>
                @endif
            </ul>
        </li>
        <li class="mt-auto">
            @if(auth()->user()?->isAdmin())
            <a href="{{ route('app-settings.index') }}" 
               class="{{ request()->routeIs('app-settings.*') ? 'text-indigo-600' : 'text-gray-700 hover:text-indigo-600 hover:bg-gray-50' }} group -mx-2 flex gap-x-3 rounded-md p-2 text-sm font-semibold leading-6">
                <x-heroicon-o-cog-8-tooth class="{{ request()->routeIs('app-settings.*') ? 'text-indigo-600' : 'text-gray-400 group-hover:text-indigo-600' }} h-6 w-6 shrink-0" />
                Settings
            </a>
            @endif
        </li>
    </ul>
</nav>