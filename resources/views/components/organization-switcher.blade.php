@php
    $currentOrg = current_organization();
    $user = auth()->user();
    
    // Admins see all organizations
    if ($user->isAdmin()) {
        $organizations = \App\Models\Organization::orderBy('name')->get();
    } else {
        $organizations = user_organizations();
    }
@endphp

@if($organizations->count() > 0)
    <div class="relative" x-data="{ open: false }" @click.away="open = false">
        <button type="button" 
                @click="open = !open"
                class="flex items-center gap-x-2 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
            </svg>
            <span class="truncate max-w-[150px]">
                @if($currentOrg)
                    {{ $currentOrg->name }}
                @elseif($user->isAdmin())
                    All Organizations
                @else
                    Select Organization
                @endif
            </span>
            <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
            </svg>
        </button>

        <div x-show="open"
             x-transition:enter="transition ease-out duration-100"
             x-transition:enter-start="transform opacity-0 scale-95"
             x-transition:enter-end="transform opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-75"
             x-transition:leave-start="transform opacity-100 scale-100"
             x-transition:leave-end="transform opacity-0 scale-95"
             class="absolute right-0 z-10 mt-2 w-64 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
             style="display: none;">
            <div class="py-1">
                <div class="px-3 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider">
                    Your Organizations
                </div>
                
                @foreach($organizations as $org)
                    <form method="POST" action="{{ route('organization.switch', $org) }}" class="block">
                        @csrf
                        <button type="submit" 
                                class="group flex w-full items-center px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900 {{ $currentOrg && $currentOrg->id === $org->id ? 'bg-gray-50' : '' }}">
                            <span class="flex-1 text-left">
                                <span class="font-medium">{{ $org->name }}</span>
                                @if($user->isAdmin())
                                    <span class="text-xs text-gray-500 block">Admin Access</span>
                                @endif
                            </span>
                            @if($currentOrg && $currentOrg->id === $org->id)
                                <svg class="ml-2 h-4 w-4 text-indigo-600" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.704 4.153a.75.75 0 01.143 1.052l-8 10.5a.75.75 0 01-1.127.075l-4.5-4.5a.75.75 0 011.06-1.06l3.894 3.893 7.48-9.817a.75.75 0 011.05-.143z" clip-rule="evenodd" />
                                </svg>
                            @endif
                        </button>
                    </form>
                @endforeach
                
                <div class="border-t border-gray-100">
                    <a href="{{ route('organizations.index') }}" 
                       class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900">
                        <div class="flex items-center">
                            <svg class="mr-2 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                            </svg>
                            See All Organizations
                        </div>
                    </a>
                    
                    @if(auth()->user()->isAdmin())
                        <a href="{{ route('app-settings.organizations.create') }}" 
                           class="block px-3 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900">
                            <div class="flex items-center">
                                <svg class="mr-2 h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Create Organization
                            </div>
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
@elseif($currentOrg)
    <div class="flex items-center gap-x-2 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300">
        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
        </svg>
        <span class="truncate max-w-[150px]">{{ $currentOrg->name }}</span>
    </div>
@endif