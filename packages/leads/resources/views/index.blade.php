<x-app-layout>
    @php
        $routePrefix = config('leads.routes.as', 'leads.');
    @endphp
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="sm:flex sm:items-center sm:justify-between">
            <div class="sm:flex-auto">
                <h1 class="text-base font-semibold text-gray-900">Leads</h1>
                <p class="mt-2 text-sm text-gray-700">A list of all the leads in your account including their name, email, phone and company information.</p>
            </div>
            <div class="mt-4 sm:mt-0 sm:flex-none">
                <a href="{{ route($routePrefix . 'create') }}"
                   class="block rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Add lead</a>
            </div>
        </div>

        <!-- Filters Section -->
        <div class="mt-6 flex flex-wrap items-center gap-6">
            <div class="flex gap-x-6 text-sm font-medium leading-5">
                @php
                    $currentPeriod = request('period', 'all');
                    $baseParams = request()->except(['period', 'page']);
                @endphp
                <a href="{{ route($routePrefix . 'index', array_merge($baseParams, ['period' => '7days'])) }}" 
                   class="{{ $currentPeriod === '7days' ? 'text-indigo-600' : 'text-gray-500 hover:text-gray-700' }}">Last 7 days</a>
                <a href="{{ route($routePrefix . 'index', array_merge($baseParams, ['period' => '30days'])) }}" 
                   class="{{ $currentPeriod === '30days' ? 'text-indigo-600' : 'text-gray-500 hover:text-gray-700' }}">Last 30 days</a>
                <a href="{{ route($routePrefix . 'index', array_merge($baseParams, ['period' => 'all'])) }}" 
                   class="{{ $currentPeriod === 'all' ? 'text-indigo-600' : 'text-gray-500 hover:text-gray-700' }}">All-time</a>
            </div>
            @if(config('leads.features.archive', true))
            <div class="flex items-center text-sm font-medium leading-5">
                <a href="{{ route($routePrefix . 'index', array_merge($baseParams, ['archived' => !request()->boolean('archived')])) }}" 
                   class="{{ request()->boolean('archived') ? 'text-indigo-600' : 'text-gray-500 hover:text-gray-700' }}">Archived</a>
            </div>
            @endif
        </div>

        <!-- Search Form -->
        <div class="mt-4">
            <form method="GET" action="{{ route($routePrefix . 'index') }}" class="flex items-center justify-end">
                <div class="relative flex-1 max-w-md">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
                    </div>
                    <input type="text" 
                           name="search" 
                           value="{{ request('search') }}"
                           placeholder="Search leads..."
                           class="pl-10 w-full rounded-md border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
                <button type="submit"
                        class="ml-3 inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Search
                </button>
                @if(request('search'))
                    <a href="{{ route($routePrefix . 'index', request()->except('search')) }}"
                       class="ml-2 inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </a>
                @endif
            </form>
        </div>

        <div class="mt-8">
            @forelse($leads as $lead)
                @if($loop->first)
                    <ul role="list" class="divide-y divide-gray-100 overflow-hidden bg-white shadow-xs ring-1 ring-gray-900/5 sm:rounded-xl">
                @endif
                
                <li class="relative flex justify-between gap-x-6 px-4 py-5 hover:bg-gray-50 sm:px-6 {{ $lead->isArchived() ? 'bg-gray-50/50' : '' }}">
                    <div class="flex min-w-0 gap-x-4">
                        <div class="min-w-0 flex-auto">
                            <p class="text-sm/6 font-semibold text-gray-900">
                                <a href="{{ route($routePrefix . 'show', $lead) }}">
                                    <span class="absolute inset-x-0 -top-px bottom-0"></span>
                                    {{ $lead->name }}
                                    @if($lead->isArchived())
                                        <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                            Archived
                                        </span>
                                    @endif
                                </a>
                            </p>
                            @if($lead->company)
                                <p class="mt-1 text-xs/5 text-gray-500">{{ $lead->company }}</p>
                            @endif
                            <p class="mt-1 flex text-xs/5 text-gray-500">
                                <a href="mailto:{{ $lead->email }}" class="relative truncate hover:underline">{{ $lead->email }}</a>
                            </p>
                            @if($lead->phone)
                                <p class="mt-1 text-xs/5 text-gray-500">{{ $lead->phone }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="flex shrink-0 items-center gap-x-4">
                        <div class="hidden sm:flex sm:flex-col sm:items-end">
                            <p class="mt-1 text-xs/5 text-gray-500">
                                Created <time datetime="{{ $lead->created_at->toISOString() }}">{{ $lead->created_at->diffForHumans() }}</time>
                            </p>
                        </div>
                        <a href="{{ route($routePrefix . 'show', $lead) }}" class="relative z-10">
                            <svg viewBox="0 0 20 20" fill="currentColor" data-slot="icon" aria-hidden="true" class="size-5 flex-none text-gray-400 hover:text-gray-600">
                                <path d="M8.22 5.22a.75.75 0 0 1 1.06 0l4.25 4.25a.75.75 0 0 1 0 1.06l-4.25 4.25a.75.75 0 0 1-1.06-1.06L11.94 10 8.22 6.28a.75.75 0 0 1 0-1.06Z" clip-rule="evenodd" fill-rule="evenodd" />
                            </svg>
                        </a>
                    </div>
                </li>

                @if($loop->last)
                    </ul>
                @endif
            @empty
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                        <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 13h6m-3-3v6m-9 1V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">No leads found</h3>
                    <p class="mt-1 text-sm text-gray-500">Get started by creating a new lead.</p>
                    <div class="mt-6">
                        <a href="{{ route($routePrefix . 'create') }}" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                            <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                            </svg>
                            New Lead
                        </a>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($leads->hasPages())
            <div class="mt-6">
                {{ $leads->links() }}
            </div>
        @endif
    </div>

    @if (session('success'))
        <div class="rounded-md bg-green-50 p-4 fixed bottom-0 right-0 m-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-green-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if (session('error'))
        <div class="rounded-md bg-red-50 p-4 fixed bottom-0 right-0 m-6">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-red-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif
</x-app-layout>