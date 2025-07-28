<x-app-layout>
    @php
        $routePrefix = config('leads.routes.as', 'leads.');
        $baseParams = ['organization' => current_organization()->slug];
        $activesCount = $leads->filter(fn($lead) => !$lead->isArchived())->count();
        $archivedCount = $leads->filter(fn($lead) => $lead->isArchived())->count();
    @endphp
    
    <div class="divide-y divide-gray-200" x-data="{ 
        searchQuery: '',
        activeTab: '{{ request()->boolean('archived') ? 'archived' : 'active' }}',
        leads: {{ Js::from($leads->map(function($lead) {
            return [
                'id' => $lead->id,
                'name' => $lead->name,
                'company' => $lead->company,
                'email' => $lead->email,
                'phone' => $lead->phone,
                'created_at' => $lead->created_at,
                'is_archived' => $lead->isArchived(),
            ];
        })) }},
        filterLeads(leads, isArchived) {
            let filtered = leads.filter(lead => lead.is_archived === isArchived);
            if (!this.searchQuery) return filtered;
            const query = this.searchQuery.toLowerCase();
            return filtered.filter(lead => 
                lead.name.toLowerCase().includes(query) || 
                lead.email.toLowerCase().includes(query) ||
                (lead.company && lead.company.toLowerCase().includes(query)) ||
                (lead.phone && lead.phone.toLowerCase().includes(query))
            );
        }
    }">
        
        <!-- Section Header -->
        <div class="pb-5 sm:flex sm:items-center sm:justify-between">
            <h3 class="text-base font-semibold text-gray-900">Leads</h3>
            <div class="mt-3 sm:mt-0 sm:ml-4">
                <div class="relative rounded-md flex">
                    <div class="relative flex items-stretch flex-grow focus-within:z-10">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input 
                            type="text" 
                            name="search" 
                            id="search" 
                            x-model="searchQuery" 
                            class="focus:ring-indigo-500 focus:border-indigo-500 block w-full rounded-none rounded-l-md pl-10 sm:text-sm border-gray-300" 
                            placeholder="Search leads"
                        >
                    </div>
                    <a href="{{ route($routePrefix . 'create', ['organization' => current_organization()->slug]) }}" 
                       class="-ml-px relative inline-flex items-center space-x-2 px-4 py-2 border border-gray-300 text-sm font-medium rounded-r-md text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" />
                        </svg>
                        <span>Add Lead</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="mt-8">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8">
                    <button @click="activeTab = 'active'"
                            :class="activeTab === 'active' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Active Leads ({{ $activesCount }})
                    </button>
                    @if(config('leads.features.archive', true))
                    <button @click="activeTab = 'archived'"
                            :class="activeTab === 'archived' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                        Archived Leads ({{ $archivedCount }})
                    </button>
                    @endif
                </nav>
            </div>

            <!-- Active Leads Table -->
            <div x-show="activeTab === 'active'" class="mt-6">
                <div class="overflow-hidden bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Company</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <template x-for="lead in filterLeads(leads, false)" :key="lead.id">
                                <tr class="hover:bg-gray-50 cursor-pointer" @click="window.location.href = '{{ route($routePrefix . 'show', ['organization' => current_organization()->slug, 'lead' => '__ID__']) }}'.replace('__ID__', lead.id)">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900" x-text="lead.name"></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="lead.company || '-'"></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <a :href="'mailto:' + lead.email" @click.stop class="text-indigo-600 hover:text-indigo-900" x-text="lead.email"></a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="lead.phone || '-'"></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="new Date(lead.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })"></td>
                                </tr>
                            </template>
                            <tr x-show="filterLeads(leads, false).length === 0">
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">No active leads found</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Archived Leads Table -->
            @if(config('leads.features.archive', true))
            <div x-show="activeTab === 'archived'" class="mt-6">
                <div class="overflow-hidden bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Company</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Phone</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <template x-for="lead in filterLeads(leads, true)" :key="lead.id">
                                <tr class="hover:bg-gray-50 cursor-pointer" @click="window.location.href = '{{ route($routePrefix . 'show', ['organization' => current_organization()->slug, 'lead' => '__ID__']) }}'.replace('__ID__', lead.id)">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900" x-text="lead.name"></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="lead.company || '-'"></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <a :href="'mailto:' + lead.email" @click.stop class="text-indigo-600 hover:text-indigo-900" x-text="lead.email"></a>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="lead.phone || '-'"></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="new Date(lead.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })"></td>
                                </tr>
                            </template>
                            <tr x-show="filterLeads(leads, true).length === 0">
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">No archived leads found</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            @endif
        </div>
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