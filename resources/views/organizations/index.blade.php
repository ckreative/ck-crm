@php
    // Hide sidebar for organization selection
    View::share('hideSidebar', true);
@endphp

<x-app-layout>
    <div class="py-12" x-data="{ showCreateDrawer: {{ session('error') && old('from_drawer') ? 'true' : 'false' }} }">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8" x-data="organizationSearch()">
            @if (session('error'))
                <div class="mb-4 rounded-md bg-red-50 p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-red-800">
                                {{ session('error') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endif
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
                            <input type="text" name="search" id="search" x-model="searchQuery" class="focus:ring-indigo-500 focus:border-indigo-500 block w-full rounded-none rounded-l-md pl-10 sm:text-sm border-gray-300" placeholder="Search organizations">
                            <!-- Loading indicator -->
                            <div x-show="loading" class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <svg class="animate-spin h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="relative inline-block text-left" x-data="{ open: false }">
                            <button type="button" @click="open = !open" @click.away="open = false" class="-ml-px relative inline-flex items-center space-x-2 px-4 py-2 border border-gray-300 text-sm font-medium rounded-r-md text-gray-700 bg-gray-50 hover:bg-gray-100 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                                <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path d="M3 3a1 1 0 000 2h11a1 1 0 100-2H3zM3 7a1 1 0 000 2h5a1 1 0 000-2H3zM3 11a1 1 0 100 2h4a1 1 0 100-2H3zM13 16a1 1 0 102 0v-5.586l1.293 1.293a1 1 0 001.414-1.414l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 101.414 1.414L13 10.414V16z" />
                                </svg>
                                <span>
                                    @php
                                        $sortLabels = [
                                            'name_asc' => 'Name (A-Z)',
                                            'name_desc' => 'Name (Z-A)',
                                            'appointments_desc' => 'Most appointments',
                                            'appointments_asc' => 'Fewest appointments',
                                        ];
                                    @endphp
                                    {{ $sortLabels[$sort ?? 'name_asc'] ?? 'Sort' }}
                                </span>
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            
                            <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="origin-top-right absolute right-0 mt-2 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-10">
                                <div class="py-1">
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'name_asc']) }}" class="group flex items-center px-4 py-2 text-sm {{ ($sort ?? 'name_asc') === 'name_asc' ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                                        @if(($sort ?? 'name_asc') === 'name_asc')
                                            <svg class="mr-3 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                        @else
                                            <span class="mr-3 h-5 w-5"></span>
                                        @endif
                                        Name (A-Z)
                                    </a>
                                    
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'name_desc']) }}" class="group flex items-center px-4 py-2 text-sm {{ ($sort ?? 'name_asc') === 'name_desc' ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                                        @if(($sort ?? 'name_asc') === 'name_desc')
                                            <svg class="mr-3 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                        @else
                                            <span class="mr-3 h-5 w-5"></span>
                                        @endif
                                        Name (Z-A)
                                    </a>
                                    
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'appointments_desc']) }}" class="group flex items-center px-4 py-2 text-sm {{ ($sort ?? 'name_asc') === 'appointments_desc' ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                                        @if(($sort ?? 'name_asc') === 'appointments_desc')
                                            <svg class="mr-3 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                        @else
                                            <span class="mr-3 h-5 w-5"></span>
                                        @endif
                                        Most appointments first
                                    </a>
                                    
                                    <a href="{{ request()->fullUrlWithQuery(['sort' => 'appointments_asc']) }}" class="group flex items-center px-4 py-2 text-sm {{ ($sort ?? 'name_asc') === 'appointments_asc' ? 'bg-gray-100 text-gray-900' : 'text-gray-700 hover:bg-gray-100 hover:text-gray-900' }}">
                                        @if(($sort ?? 'name_asc') === 'appointments_asc')
                                            <svg class="mr-3 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                            </svg>
                                        @else
                                            <span class="mr-3 h-5 w-5"></span>
                                        @endif
                                        Fewest appointments first
                                    </a>
                                </div>
                            </div>
                        </div>
                        @if(auth()->user()->isAdmin())
                            <button @click="showCreateDrawer = true" type="button" class="ml-3 relative inline-flex items-center space-x-2 px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-1 focus:ring-indigo-500">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                <span>Create</span>
                            </button>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="mt-6"></div>
            
            <!-- Organizations Grid -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                <template x-if="!searchActive">
                    <div class="col-span-full grid grid-cols-1 gap-4 sm:grid-cols-2">
                        @forelse($organizations as $organization)
                            <form method="POST" action="{{ route('organization.switch', $organization) }}">
                                @csrf
                                <button type="submit" class="relative flex items-center space-x-3 rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-xs focus-within:ring-2 focus-within:ring-indigo-500 focus-within:ring-offset-2 hover:border-gray-400 w-full text-left">
                                    <div class="shrink-0">
                                        @if($organization->logo_path)
                                            <img src="{{ $organization->getLogoUrl(40, 40) }}" 
                                                 srcset="{{ $organization->getLogoSrcset(40) }}"
                                                 alt="{{ $organization->name }} logo" 
                                                 class="size-10 rounded-full object-contain bg-gray-50">
                                        @else
                                            <div class="size-10 rounded-full bg-gray-200 flex items-center justify-center">
                                                <svg class="h-6 w-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <span aria-hidden="true" class="absolute inset-0"></span>
                                        <p class="text-sm font-medium text-gray-900">{{ $organization->name }}</p>
                                        <p class="truncate text-sm text-gray-500">
                                            {{ $organization->upcoming_appointments_count ?? 0 }} upcoming {{ Str::plural('appointment', $organization->upcoming_appointments_count ?? 0) }}
                                        </p>
                                    </div>
                                </button>
                            </form>
                        @empty
                            <div class="col-span-full text-center py-12">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No organizations</h3>
                                <p class="mt-1 text-sm text-gray-500">You are not a member of any organizations.</p>
                            </div>
                        @endforelse
                    </div>
                </template>
                
                <!-- Search Results -->
                <template x-if="searchActive">
                    <div class="col-span-full">
                        <template x-if="searchResults.length > 0">
                            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <template x-for="organization in searchResults" :key="organization.id">
                                    <form method="POST" :action="`{{ url('/organization/switch') }}/${organization.id}`">
                                        @csrf
                                        <button type="submit" class="relative flex items-center space-x-3 rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-xs focus-within:ring-2 focus-within:ring-indigo-500 focus-within:ring-offset-2 hover:border-gray-400 w-full text-left">
                                            <div class="shrink-0">
                                                <template x-if="organization.logo_path">
                                                    <img :src="`/image-transform/width=40,height=40/${organization.logo_path}`" 
                                                         :srcset="`/image-transform/width=40,height=40/${organization.logo_path} 1x, /image-transform/width=80,height=80/${organization.logo_path} 2x`"
                                                         :alt="`${organization.name} logo`" 
                                                         class="size-10 rounded-full object-contain bg-gray-50">
                                                </template>
                                                <template x-if="!organization.logo_path">
                                                    <div class="size-10 rounded-full bg-gray-200 flex items-center justify-center">
                                                        <svg class="h-6 w-6 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                                        </svg>
                                                    </div>
                                                </template>
                                            </div>
                                            <div class="min-w-0 flex-1">
                                                <span aria-hidden="true" class="absolute inset-0"></span>
                                                <p class="text-sm font-medium text-gray-900" x-text="organization.name"></p>
                                                <p class="truncate text-sm text-gray-500">
                                                    <span x-text="organization.upcoming_appointments_count || 0"></span> upcoming 
                                                    <span x-text="(organization.upcoming_appointments_count === 1) ? 'appointment' : 'appointments'"></span>
                                                </p>
                                            </div>
                                        </button>
                                    </form>
                                </template>
                            </div>
                        </template>
                        
                        <template x-if="searchResults.length === 0 && !loading">
                            <div class="text-center py-12">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No organizations found</h3>
                                <p class="mt-1 text-sm text-gray-500">Try adjusting your search terms.</p>
                            </div>
                        </template>
                    </div>
                </template>
            </div>

            <!-- Pagination (only show when not searching) -->
            <template x-if="!searchActive">
                <div>
                    @if($organizations->hasPages())
                        <div class="mt-6">
                            {{ $organizations->links() }}
                        </div>
                    @endif
                </div>
            </template>
            
        </div>
        
        @if(auth()->user()->isAdmin())
            <!-- Create Organization Drawer -->
            <div x-show="showCreateDrawer" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-50 overflow-hidden" 
                 aria-labelledby="drawer-title" 
                 role="dialog" 
                 aria-modal="true">
                <!-- Background backdrop -->
                <div class="absolute inset-0 bg-gray-500 bg-opacity-75" @click="showCreateDrawer = false"></div>
                
                <div class="fixed inset-0 overflow-hidden">
                    <div class="absolute inset-0 overflow-hidden">
                        <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10 sm:pl-16">
                            <div x-show="showCreateDrawer"
                                 x-transition:enter="transform transition ease-in-out duration-500 sm:duration-700"
                                 x-transition:enter-start="translate-x-full"
                                 x-transition:enter-end="translate-x-0"
                                 x-transition:leave="transform transition ease-in-out duration-500 sm:duration-700"
                                 x-transition:leave-start="translate-x-0"
                                 x-transition:leave-end="translate-x-full"
                                 class="pointer-events-auto w-screen max-w-2xl">
                            <form method="POST" action="{{ route('app-settings.organizations.store') }}" 
                                  class="flex h-full flex-col overflow-y-auto bg-white shadow-xl"
                                  x-on:submit="setTimeout(() => { showCreateDrawer = false }, 100)">
                                @csrf
                                <input type="hidden" name="from_drawer" value="1">
                                <div class="flex-1">
                                    <!-- Header -->
                                    <div class="bg-gray-50 px-4 py-6 sm:px-6">
                                        <div class="flex items-start justify-between space-x-3">
                                            <div class="space-y-1">
                                                <h2 id="drawer-title" class="text-base font-semibold text-gray-900">New Organization</h2>
                                                <p class="text-sm text-gray-500">Create a new organization and assign an owner.</p>
                                            </div>
                                            <div class="flex h-7 items-center">
                                                <button type="button" @click="showCreateDrawer = false" class="relative text-gray-400 hover:text-gray-500">
                                                    <span class="absolute -inset-2.5"></span>
                                                    <span class="sr-only">Close panel</span>
                                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true" class="size-6">
                                                        <path d="M6 18 18 6M6 6l12 12" stroke-linecap="round" stroke-linejoin="round" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Form fields -->
                                    <div class="space-y-6 py-6 sm:space-y-0 sm:divide-y sm:divide-gray-200 sm:py-0">
                                        <!-- Organization name -->
                                        <div class="space-y-2 px-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:space-y-0 sm:px-6 sm:py-5">
                                            <div>
                                                <label for="organization-name" class="block text-sm/6 font-medium text-gray-900 sm:mt-1.5">Organization name</label>
                                            </div>
                                            <div class="sm:col-span-2">
                                                <input id="organization-name" type="text" name="name" value="{{ old('name') }}" required 
                                                       class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-indigo-600 sm:text-sm/6" />
                                                @error('name')
                                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>

                                        <!-- Owner email -->
                                        <div class="space-y-2 px-4 sm:grid sm:grid-cols-3 sm:gap-4 sm:space-y-0 sm:px-6 sm:py-5">
                                            <div>
                                                <label for="owner-email" class="block text-sm/6 font-medium text-gray-900 sm:mt-1.5">Owner email</label>
                                            </div>
                                            <div class="sm:col-span-2">
                                                <input id="owner-email" type="email" name="owner_email" value="{{ old('owner_email') }}" required 
                                                       class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus-visible:outline-2 focus-visible:-outline-offset-2 focus-visible:outline-indigo-600 sm:text-sm/6" />
                                                <p class="mt-2 text-sm text-gray-500">Enter the email address of the user who will own this organization. An invitation will be sent if they don't have an account.</p>
                                                @error('owner_email')
                                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Action buttons -->
                                <div class="shrink-0 border-t border-gray-200 px-4 py-5 sm:px-6">
                                    <div class="flex justify-end space-x-3">
                                        <button type="button" @click="showCreateDrawer = false" class="rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-xs ring-1 ring-gray-300 ring-inset hover:bg-gray-50">Cancel</button>
                                        <button type="submit" class="inline-flex justify-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Create</button>
                                    </div>
                                </div>
                            </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <script>
        function organizationSearch() {
            return {
                searchQuery: '',
                searchResults: [],
                loading: false,
                searchActive: false,
                searchTimeout: null,
                
                init() {
                    this.$watch('searchQuery', (value) => {
                        clearTimeout(this.searchTimeout);
                        
                        if (value.length === 0) {
                            this.searchActive = false;
                            this.searchResults = [];
                            return;
                        }
                        
                        this.searchTimeout = setTimeout(() => {
                            this.performSearch(value);
                        }, 300);
                    });
                },
                
                async performSearch(query) {
                    this.loading = true;
                    this.searchActive = true;
                    
                    try {
                        const response = await fetch(`{{ route('organizations.search') }}?search=${encodeURIComponent(query)}`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest',
                            }
                        });
                        
                        if (!response.ok) {
                            throw new Error('Search failed');
                        }
                        
                        const data = await response.json();
                        this.searchResults = data.organizations;
                    } catch (error) {
                        console.error('Search error:', error);
                        this.searchResults = [];
                    } finally {
                        this.loading = false;
                    }
                }
            }
        }
    </script>
</x-app-layout>