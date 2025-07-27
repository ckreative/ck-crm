<x-app-layout>
    <x-slot name="navigation">
        <x-app-settings-navigation />
    </x-slot>
    
    <!-- Page header -->
    <div class="divide-y divide-gray-200" x-data="{ showAddModal: false }">
        <div class="pb-6">
            <div class="sm:flex sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-base font-semibold leading-7 text-gray-900">{{ $organization->name }} Members</h2>
                    <p class="mt-1 text-sm leading-6 text-gray-600">Manage users who have access to this organization.</p>
                </div>
                <div class="mt-4 sm:mt-0 sm:flex sm:gap-x-3">
                    <a href="{{ route('app-settings.organizations.details', $organization) }}"
                       class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <x-heroicon-o-arrow-left class="-ml-0.5 mr-1.5 h-5 w-5" />
                        Back
                    </a>
                    <button type="button" 
                            @click="showAddModal = true"
                            class="inline-flex items-center px-3 py-2 border border-transparent text-sm font-semibold rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-600">
                        <x-heroicon-o-plus class="-ml-1 mr-2 h-5 w-5" />
                        Add Member
                    </button>
                </div>
            </div>
        </div>

        <!-- Members Table -->
        <div class="mt-6">
            <div class="overflow-hidden bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-300">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Joined</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($members as $member)
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0 h-10 w-10">
                                            <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                                <span class="text-gray-600 font-medium">{{ substr($member->name, 0, 1) }}</span>
                                            </div>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-sm font-medium text-gray-900">{{ $member->name }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $member->email }}</td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <form method="POST" action="{{ route('app-settings.organizations.members.update', [$organization, $member]) }}" class="inline">
                                        @csrf
                                        @method('PUT')
                                        <select name="role" 
                                                onchange="this.form.submit()"
                                                class="text-sm border-gray-300 rounded-md focus:ring-indigo-500 focus:border-indigo-500">
                                            <option value="org_owner" {{ $member->pivot->role === 'org_owner' ? 'selected' : '' }}>Owner</option>
                                            <option value="org_admin" {{ $member->pivot->role === 'org_admin' ? 'selected' : '' }}>Admin</option>
                                            <option value="org_manager" {{ $member->pivot->role === 'org_manager' ? 'selected' : '' }}>Manager</option>
                                            <option value="org_member" {{ $member->pivot->role === 'org_member' ? 'selected' : '' }}>Member</option>
                                            <option value="org_guest" {{ $member->pivot->role === 'org_guest' ? 'selected' : '' }}>Guest</option>
                                        </select>
                                    </form>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $member->pivot->joined_at ? \Carbon\Carbon::parse($member->pivot->joined_at)->format('M d, Y') : 'N/A' }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <form method="POST" 
                                          action="{{ route('app-settings.organizations.members.remove', [$organization, $member]) }}"
                                          class="inline"
                                          onsubmit="return confirm('Are you sure you want to remove this member?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">
                                            Remove
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                    No members found in this organization.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        @if($members->hasPages())
            <div class="mt-4">
                {{ $members->links() }}
            </div>
        @endif

        <!-- Add Member Modal -->
        <div x-show="showAddModal" class="relative z-50">
            <!-- Modal backdrop -->
            <div x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                 @click="showAddModal = false"></div>
            
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
                         @click.away="showAddModal = false">
                        <form method="POST" action="{{ route('app-settings.organizations.members.add', $organization) }}" class="p-6">
                            @csrf

                            <h2 class="text-lg font-medium text-gray-900">Add Member to {{ $organization->name }}</h2>
                            <p class="mt-1 text-sm text-gray-600">Enter the email address of an existing user to add them to this organization.</p>

                            <div class="mt-6 space-y-4">
                                <div>
                                    <x-input-label for="user_email" value="User Email" />
                                    <x-text-input id="user_email" 
                                                  name="user_email" 
                                                  type="email" 
                                                  class="mt-1 block w-full" 
                                                  placeholder="user@example.com"
                                                  required />
                                    <x-input-error :messages="$errors->get('user_email')" class="mt-2" />
                                </div>

                                <div>
                                    <x-input-label for="role" value="Role" />
                                    <select id="role" 
                                            name="role" 
                                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"
                                            required>
                                        <option value="org_member">Member</option>
                                        <option value="org_manager">Manager</option>
                                        <option value="org_admin">Admin</option>
                                        <option value="org_owner">Owner</option>
                                        <option value="org_guest">Guest</option>
                                    </select>
                                    <x-input-error :messages="$errors->get('role')" class="mt-2" />
                                </div>
                            </div>

                            <div class="mt-6 flex justify-end gap-x-3">
                                <button type="button"
                                        @click="showAddModal = false"
                                        class="inline-flex justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                    Cancel
                                </button>
                                <button type="submit"
                                        class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                                    Add Member
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