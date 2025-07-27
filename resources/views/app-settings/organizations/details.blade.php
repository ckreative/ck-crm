<x-app-layout>
    <x-slot name="navigation">
        <x-app-settings-navigation />
    </x-slot>
    
    <!-- Page header -->
    <div class="pb-8">
        <div class="pb-6">
            <h2 class="text-base font-semibold leading-7 text-gray-900">Organization Details</h2>
            <p class="mt-1 text-sm leading-6 text-gray-600">Manage organization settings and configuration.</p>
        </div>
    </div>
    
    <!-- Settings forms -->
    <div class="divide-y divide-gray-200 bg-white">
        <div class="grid max-w-7xl mx-auto grid-cols-1 gap-x-8 gap-y-10 px-4 py-16 sm:px-6 md:grid-cols-3 lg:px-8">
            <div>
                <h2 class="text-base/7 font-semibold text-gray-900">Organization Information</h2>
                <p class="mt-1 text-sm/6 text-gray-500">Basic details about your organization.</p>
            </div>

            <form class="md:col-span-2" method="POST" action="{{ route('app-settings.organizations.update', $organization) }}">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:max-w-xl sm:grid-cols-6">
                    <div class="col-span-full">
                        <label for="name" class="block text-sm/6 font-medium text-gray-900">Organization name</label>
                        <div class="mt-2">
                            <input type="text" name="name" id="name" autocomplete="organization" 
                                   value="{{ old('name', $organization->name) }}"
                                   class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm/6" />
                        </div>
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="col-span-full">
                        <label for="slug" class="block text-sm/6 font-medium text-gray-900">URL slug</label>
                        <div class="mt-2">
                            <div class="flex rounded-md shadow-sm ring-1 ring-inset ring-gray-300 focus-within:ring-2 focus-within:ring-inset focus-within:ring-indigo-600 sm:max-w-md">
                                <span class="flex select-none items-center pl-3 text-gray-500 sm:text-sm">{{ request()->getHost() }}/</span>
                                <input type="text" name="slug" id="slug" 
                                       value="{{ old('slug', $organization->slug) }}"
                                       class="block flex-1 border-0 bg-transparent py-1.5 pl-1 text-gray-900 placeholder:text-gray-400 focus:ring-0 sm:text-sm/6" />
                            </div>
                        </div>
                        @error('slug')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-8 flex">
                    <button type="submit" class="rounded-md bg-indigo-500 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500">Save</button>
                </div>
            </form>
        </div>

        <div class="grid max-w-7xl mx-auto grid-cols-1 gap-x-8 gap-y-10 px-4 py-16 sm:px-6 md:grid-cols-3 lg:px-8">
            <div>
                <h2 class="text-base/7 font-semibold text-gray-900">Cal.com Integration</h2>
                <p class="mt-1 text-sm/6 text-gray-500">Connect your Cal.com account to sync bookings and calendar events.</p>
            </div>

            <form class="md:col-span-2" method="POST" action="{{ route('app-settings.organizations.update', $organization) }}">
                @csrf
                @method('PUT')
                <input type="hidden" name="update_calcom" value="1">
                
                <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:max-w-xl sm:grid-cols-6">
                    @php
                        $calcomSettings = $organization->getCalcomSettings();
                        // Decrypt API key for display (masked)
                        $apiKeyMasked = '';
                        if (!empty($calcomSettings['api_key'])) {
                            $apiKey = $organization->getCalcomApiKey();
                            $apiKeyMasked = $apiKey ? substr($apiKey, 0, 10) . str_repeat('*', 20) : '';
                        }
                    @endphp

                    <!-- Enable Cal.com -->
                    <div class="col-span-full">
                        <label for="calcom_enabled" class="flex items-center">
                            <input type="hidden" name="calcom_enabled" value="0">
                            <input type="checkbox" 
                                   name="calcom_enabled" 
                                   id="calcom_enabled" 
                                   value="1"
                                   {{ old('calcom_enabled', $calcomSettings['enabled'] ?? false) ? 'checked' : '' }}
                                   class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                            <span class="ml-2 text-sm text-gray-900">Enable Cal.com Integration</span>
                        </label>
                    </div>

                    <!-- API Key -->
                    <div class="col-span-full">
                        <label for="calcom_api_key" class="block text-sm/6 font-medium text-gray-900">
                            Cal.com API Key
                        </label>
                        <div class="mt-2">
                            <input type="text" 
                                   name="calcom_api_key" 
                                   id="calcom_api_key"
                                   placeholder="{{ $apiKeyMasked ?: 'Enter your Cal.com API key' }}"
                                   class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm/6">
                        </div>
                        <p class="mt-2 text-sm text-gray-500">
                            Get your API key from 
                            <a href="https://app.cal.com/settings/developer/api-keys" target="_blank" class="text-indigo-600 hover:text-indigo-500">
                                Cal.com Settings
                            </a>
                        </p>
                        @error('calcom_api_key')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Hidden fields for automatic sync -->
                    <input type="hidden" name="calcom_sync_enabled" value="1">
                    <input type="hidden" name="calcom_sync_days" value="30">

                    <!-- Automatic Sync Info -->
                    <div class="col-span-full">
                        <div class="rounded-md bg-blue-50 p-4">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-blue-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-blue-700">
                                        Bookings are automatically synced every 15 minutes when Cal.com integration is enabled.
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-8 flex gap-x-3">
                    <button type="submit" class="rounded-md bg-indigo-500 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500">Save</button>
                    
                    @if($calcomSettings['enabled'] ?? false)
                        <button type="button" 
                                onclick="testCalcomConnection()"
                                class="rounded-md bg-gray-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-500">
                            Test Connection
                        </button>
                    @endif
                </div>

                @if($calcomSettings['enabled'] ?? false)
                    <div class="mt-8 pt-8 border-t border-gray-200">
                        <h3 class="text-sm font-medium text-gray-900 mb-2">Manual Sync</h3>
                        <p class="text-sm text-gray-500 mb-4">
                            Run a manual sync of Cal.com bookings for this organization.
                        </p>
                        <div class="bg-gray-50 rounded-md p-3 font-mono text-xs">
                            php artisan leads:sync-calcom --organization={{ $organization->slug }}
                        </div>
                    </div>
                @endif
            </form>
        </div>

        <div class="grid max-w-7xl mx-auto grid-cols-1 gap-x-8 gap-y-10 px-4 py-16 sm:px-6 md:grid-cols-3 lg:px-8">
            <div>
                <h2 class="text-base/7 font-semibold text-gray-900">Team members</h2>
                <p class="mt-1 text-sm/6 text-gray-500">Add or remove team members for this organization.</p>
            </div>

            <div class="md:col-span-2">
                <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:max-w-xl sm:grid-cols-6">
                    <div class="col-span-full">
                        <p class="text-sm text-gray-900">{{ $organization->users()->count() }} {{ Str::plural('member', $organization->users()->count()) }}</p>
                        @php
                            $owner = $organization->owner();
                        @endphp
                        <p class="mt-1 text-sm text-gray-500">
                            @if($owner)
                                Organization owner: {{ $owner->name }}
                            @else
                                No owner assigned
                            @endif
                        </p>
                    </div>
                </div>

                <div class="mt-8 flex">
                    <a href="{{ route('app-settings.organizations.members', $organization) }}" 
                       class="rounded-md bg-indigo-500 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-400 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500">
                        Manage members
                    </a>
                </div>
            </div>
        </div>

        @if(\App\Models\Organization::count() > 1)
            <div class="grid max-w-7xl mx-auto grid-cols-1 gap-x-8 gap-y-10 px-4 py-16 sm:px-6 md:grid-cols-3 lg:px-8">
                <div>
                    <h2 class="text-base/7 font-semibold text-gray-900">Delete organization</h2>
                    <p class="mt-1 text-sm/6 text-gray-500">No longer want to use this organization? You can delete it here. This action is not reversible. All information related to this organization will be deleted permanently.</p>
                </div>

                <form class="flex items-start md:col-span-2" 
                      method="POST" 
                      action="{{ route('app-settings.organizations.destroy', $organization) }}"
                      onsubmit="return confirm('Are you sure you want to delete this organization? This action cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="rounded-md bg-red-500 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-red-400">Yes, delete this organization</button>
                </form>
            </div>
        @endif
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

    <script>
        function testCalcomConnection() {
            fetch("{{ route('organizations.calcom.test', $organization) }}", {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('✅ ' + data.message + '\nConnected as: ' + data.user);
                } else {
                    alert('❌ ' + data.message);
                }
            })
            .catch(error => {
                alert('❌ Connection test failed: ' + error.message);
            });
        }
    </script>
</x-app-layout>