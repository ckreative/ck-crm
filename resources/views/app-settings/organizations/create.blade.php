<x-app-layout>
    <x-slot name="navigation">
        <x-app-settings-navigation />
    </x-slot>
    
    <!-- Page header -->
    <div class="divide-y divide-gray-200">
        <div class="pb-6">
            <div class="sm:flex sm:items-center sm:justify-between">
                <div>
                    <h2 class="text-base font-semibold leading-7 text-gray-900">Create Organization</h2>
                    <p class="mt-1 text-sm leading-6 text-gray-600">Add a new organization to the system.</p>
                </div>
                <div class="mt-4 sm:mt-0">
                    <a href="{{ route('app-settings.organizations.index') }}"
                       class="inline-flex items-center px-3 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <x-heroicon-o-arrow-left class="-ml-0.5 mr-1.5 h-5 w-5" />
                        Back
                    </a>
                </div>
            </div>
        </div>

        <!-- Create Form -->
        <div class="mt-6">
            <form method="POST" action="{{ route('app-settings.organizations.store') }}" class="space-y-6">
                @csrf

                <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-lg">
                    <div class="px-6 py-6">
                        <div class="grid grid-cols-1 gap-y-6 gap-x-4 sm:grid-cols-6">
                            <div class="sm:col-span-4">
                                <x-input-label for="name" value="Organization Name" />
                                <x-text-input id="name" 
                                              name="name" 
                                              type="text" 
                                              class="mt-1 block w-full" 
                                              :value="old('name')"
                                              placeholder="Acme Corporation"
                                              required />
                                <x-input-error :messages="$errors->get('name')" class="mt-2" />
                            </div>


                            <div class="sm:col-span-4">
                                <x-input-label for="owner_email" value="Owner Email" />
                                <x-text-input id="owner_email" 
                                              name="owner_email" 
                                              type="email" 
                                              class="mt-1 block w-full" 
                                              :value="old('owner_email')"
                                              placeholder="owner@example.com"
                                              required />
                                <x-input-error :messages="$errors->get('owner_email')" class="mt-2" />
                                <p class="mt-1 text-xs text-gray-500">The email address of the user who will own this organization.</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-x-3">
                    <a href="{{ route('organizations.index') }}"
                       class="inline-flex justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        Cancel
                    </a>
                    <button type="submit"
                            class="inline-flex justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                        Create Organization
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if (session('error'))
        <x-notification 
            type="error"
            title="Error"
            :message="session('error')" />
    @endif
</x-app-layout>