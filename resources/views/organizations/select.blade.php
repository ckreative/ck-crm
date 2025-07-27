@php
    // Hide sidebar for organization selection
    View::share('hideSidebar', true);
@endphp

<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="border-b border-gray-200 pb-8 sm:flex sm:items-center sm:justify-between mb-6">
                <h3 class="text-base font-semibold text-gray-900">Organizations</h3>
                <div class="mt-3 sm:mt-0 sm:ml-4">
                    <div class="relative rounded-md flex">
                        <div class="relative flex items-stretch flex-grow focus-within:z-10">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <input type="text" name="search" id="search" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full rounded-none rounded-l-md pl-10 sm:text-sm border-gray-300" placeholder="Search organizations">
                        </div>
                        <button type="button" class="-ml-px relative inline-flex items-center space-x-2 px-4 py-2 border border-gray-300 text-sm font-medium rounded-r-md text-gray-700 bg-gray-50 hover:bg-gray-100 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                            <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path d="M3 3a1 1 0 000 2h11a1 1 0 100-2H3zM3 7a1 1 0 000 2h5a1 1 0 000-2H3zM3 11a1 1 0 100 2h4a1 1 0 100-2H3zM13 16a1 1 0 102 0v-5.586l1.293 1.293a1 1 0 001.414-1.414l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 101.414 1.414L13 10.414V16z" />
                            </svg>
                            <span>Sort</span>
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="mt-6"></div>
            
            <!-- Organizations Grid -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                @foreach($organizations as $organization)
                    <form method="POST" action="{{ route('organization.switch', $organization) }}">
                        @csrf
                        <button type="submit" class="relative flex items-center space-x-3 rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-xs focus-within:ring-2 focus-within:ring-indigo-500 focus-within:ring-offset-2 hover:border-gray-400 w-full text-left">
                            <div class="shrink-0">
                                <div class="size-10 rounded-full bg-gray-200 flex items-center justify-center">
                                    <svg class="h-6 w-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                </div>
                            </div>
                            <div class="min-w-0 flex-1">
                                <span aria-hidden="true" class="absolute inset-0"></span>
                                <p class="text-sm font-medium text-gray-900">{{ $organization->name }}</p>
                                <p class="truncate text-sm text-gray-500">
                                    @if(auth()->user()->isAdmin())
                                        Admin Access - {{ $organization->upcoming_appointments_count ?? 0 }} upcoming {{ Str::plural('appointment', $organization->upcoming_appointments_count ?? 0) }}
                                    @else
                                        {{ $organization->upcoming_appointments_count ?? 0 }} upcoming {{ Str::plural('appointment', $organization->upcoming_appointments_count ?? 0) }}
                                    @endif
                                </p>
                            </div>
                        </button>
                    </form>
                @endforeach
            </div>

            @if($organizations->hasPages())
                <div class="mt-6">
                    {{ $organizations->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>