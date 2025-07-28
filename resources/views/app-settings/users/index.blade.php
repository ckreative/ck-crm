<x-app-layout>
    <x-slot name="navigation">
        <x-app-settings-navigation />
    </x-slot>
    
    <div class="divide-y divide-gray-200" x-data="{ 
        showInviteModal: false,
        searchQuery: '',
        activeTab: '{{ old('active_tab', session('active_tab', 'users')) }}',
        users: {{ Js::from($users) }},
        invitations: {{ Js::from($invitations) }},
        filterUsers(users) {
            if (!this.searchQuery) return users;
            const query = this.searchQuery.toLowerCase();
            return users.filter(user => 
                user.name.toLowerCase().includes(query) || 
                user.email.toLowerCase().includes(query)
            );
        }
    }">

    <!-- Section Header -->
    <div class="border-b border-gray-200 pb-5 sm:flex sm:items-center sm:justify-between">
        <h3 class="text-base font-semibold text-gray-900">Users</h3>
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
                        placeholder="Search users"
                    >
                </div>
                <button type="button" @click="showInviteModal = true" class="-ml-px relative inline-flex items-center space-x-2 px-4 py-2 border border-gray-300 text-sm font-medium rounded-r-md text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500">
                    <svg class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" />
                    </svg>
                    <span>Invite User</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="mt-8">
        <div class="border-b border-gray-200">
            <nav class="-mb-px flex space-x-8">
                <button @click="activeTab = 'users'"
                        :class="activeTab === 'users' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Active Users ({{ $users->count() }})
                </button>
                <button @click="activeTab = 'invitations'"
                        :class="activeTab === 'invitations' ? 'border-indigo-500 text-indigo-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">
                    Pending Invitations ({{ $invitations->filter(fn($i) => $i->isPending())->count() }})
                </button>
            </nav>
        </div>

        <!-- Active Users Table -->
        <div x-show="activeTab === 'users'" class="mt-6">
            <div class="overflow-hidden bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-300">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <template x-for="user in filterUsers(users)" :key="user.id">
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                <span class="text-gray-600 font-medium" x-text="user.name.substring(0, 1)"></span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900" x-text="user.name"></div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="user.email"></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full"
                                          :class="{
                                              'bg-purple-100 text-purple-800': user.pivot.role === 'org_owner',
                                              'bg-blue-100 text-blue-800': user.pivot.role === 'org_admin',
                                              'bg-green-100 text-green-800': user.pivot.role === 'org_member',
                                              'bg-gray-100 text-gray-800': !['org_owner', 'org_admin', 'org_member'].includes(user.pivot.role)
                                          }">
                                        <span x-text="user.pivot.role === 'org_owner' ? 'Owner' : user.pivot.role === 'org_admin' ? 'Admin' : user.pivot.role === 'org_member' ? 'Member' : user.pivot.role.replace('org_', '').charAt(0).toUpperCase() + user.pivot.role.replace('org_', '').slice(1)"></span>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500" x-text="new Date(user.pivot.joined_at || user.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })"></td>
                            </tr>
                        </template>
                        <tr x-show="filterUsers(users).length === 0">
                            <td colspan="4" class="px-6 py-4 text-center text-gray-500">No users found</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pending Invitations Table -->
        <div x-show="activeTab === 'invitations'" class="mt-6">
            <div class="overflow-hidden bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-300">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invited By</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expires</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($invitations as $invitation)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $invitation->email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $invitation->invitedBy->name }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($invitation->isExpired())
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Expired</span>
                                    @elseif($invitation->isAccepted())
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Accepted</span>
                                    @else
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">Pending</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $invitation->expires_at->format('M d, Y') }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    @if($invitation->isPending())
                                        <div class="relative inline-block text-left" x-data="{ open: false }">
                                            <button type="button"
                                                    @click="open = !open"
                                                    @click.away="open = false"
                                                    class="text-gray-400 hover:text-gray-500 focus:outline-none rounded-full p-1">
                                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path d="M10 3a1.5 1.5 0 110 3 1.5 1.5 0 010-3zM10 8.5a1.5 1.5 0 110 3 1.5 1.5 0 010-3zM11.5 15.5a1.5 1.5 0 10-3 0 1.5 1.5 0 003 0z" />
                                                </svg>
                                            </button>
                                            
                                            <div x-show="open"
                                                 x-transition:enter="transition ease-out duration-100"
                                                 x-transition:enter-start="transform opacity-0 scale-95"
                                                 x-transition:enter-end="transform opacity-100 scale-100"
                                                 x-transition:leave="transition ease-in duration-75"
                                                 x-transition:leave-start="transform opacity-100 scale-100"
                                                 x-transition:leave-end="transform opacity-0 scale-95"
                                                 class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10">
                                                <div class="py-1" role="menu" aria-orientation="vertical" aria-labelledby="options-menu">
                                                    <form action="{{ route('app-settings.users.resend', ['organization' => current_organization()->slug, 'invitation' => $invitation]) }}" method="POST">
                                                        @csrf
                                                        <input type="hidden" name="active_tab" value="invitations">
                                                        <button type="submit" 
                                                                class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900 flex items-center"
                                                                role="menuitem">
                                                            <svg class="mr-3 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                                                            </svg>
                                                            Resend Invitation
                                                        </button>
                                                    </form>
                                                    
                                                    <form action="{{ route('app-settings.users.cancel', ['organization' => current_organization()->slug, 'invitation' => $invitation]) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <input type="hidden" name="active_tab" value="invitations">
                                                        <button type="submit" 
                                                                class="w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900 flex items-center"
                                                                onclick="return confirm('Are you sure you want to cancel this invitation?')"
                                                                role="menuitem">
                                                            <svg class="mr-3 h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                                                            </svg>
                                                            Cancel Invitation
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">No invitations found</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Invite User Modal -->
    <div x-show="showInviteModal" class="relative z-50">
        
        <!-- Modal backdrop -->
        <div 
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
             @click="showInviteModal = false"></div>
        
        <!-- Modal panel -->
        <div x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg"
                     @click.away="showInviteModal = false">
        <form method="POST" action="{{ route('app-settings.users.invite', ['organization' => current_organization()->slug]) }}" class="p-6">
            @csrf

            <h2 class="text-lg font-medium text-gray-900">Invite New User</h2>
            <p class="mt-1 text-sm text-gray-600">Send an invitation email to add a new administrator.</p>

            <div class="mt-6">
                <x-input-label for="email" value="Email Address" />
                <x-text-input id="email" 
                              name="email" 
                              type="email" 
                              class="mt-1 block w-full" 
                              placeholder="user@example.com"
                              required />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>

                    <div class="mt-6 flex justify-end gap-x-3">
                        <button type="button"
                                @click="showInviteModal = false"
                                class="inline-flex justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            Cancel
                        </button>
                        <button type="submit"
                                class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            Send Invitation
                        </button>
                    </div>
                </form>
                </div>
            </div>
        </div>
    </div>
    </div>

    @if (session('success'))
        <x-notification 
            type="success"
            title="Success!"
            :message="session('success')" />
    @endif

    @if (session('error'))
        <x-notification 
            type="error"
            title="Error"
            :message="session('error')" />
    @endif
</x-app-layout>