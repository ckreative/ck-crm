<x-app-layout>
    <x-slot name="navigation">
        <x-app-settings-navigation />
    </x-slot>
    
    <!-- Page header -->
    <div class="divide-y divide-gray-200">
        <div class="pb-6">
            <h2 class="text-base font-semibold leading-7 text-gray-900">General Settings</h2>
            <p class="mt-1 text-sm leading-6 text-gray-600">Manage your organization settings and preferences.</p>
        </div>
    </div>
    
    <!-- Settings forms -->
    <div class="divide-y divide-gray-200">
        <div class="grid max-w-7xl grid-cols-1 gap-x-8 gap-y-10 px-4 py-16 sm:px-6 md:grid-cols-3 lg:px-8">
            <div>
                <h2 class="text-base font-semibold leading-7 text-gray-900">Organization Information</h2>
                <p class="mt-1 text-sm leading-6 text-gray-600">Basic details about your organization.</p>
            </div>

            <form class="md:col-span-2" method="POST" action="{{ route('app-settings.organizations.update', $organization) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:max-w-xl sm:grid-cols-6">
                    <div class="col-span-full">
                        <label for="name" class="block text-sm font-medium leading-6 text-gray-900">Organization name</label>
                        <div class="mt-2">
                            <input type="text" name="name" id="name" autocomplete="organization" 
                                   value="{{ old('name', $organization->name) }}"
                                   class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" />
                        </div>
                        @error('name')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="col-span-full">
                        <label for="slug" class="block text-sm/6 font-medium text-gray-900">URL slug</label>
                        <div class="mt-2 flex">
                            <div class="flex shrink-0 items-center rounded-l-md bg-white px-3 text-base text-gray-500 outline-1 -outline-offset-1 outline-gray-300 sm:text-sm/6">{{ request()->getHost() }}/</div>
                            <div class="-ml-px block w-full grow rounded-r-md bg-gray-50 px-3 py-1.5 text-base text-gray-500 outline-1 -outline-offset-1 outline-gray-300 sm:text-sm/6">{{ $organization->slug }}</div>
                        </div>
                        <p class="mt-2 text-sm text-gray-500">The URL slug is automatically generated from the organization name.</p>
                    </div>

                    <!-- Logo -->
                    <div class="col-span-full" x-data="{ 
                        imagePreview: null,
                        removeExisting: false 
                    }">
                        <label for="logo" class="block text-sm font-medium leading-6 text-gray-900">Organization logo</label>
                        <div class="mt-2 flex items-center gap-x-3">
                            <div x-show="!imagePreview && !removeExisting">
                                @if($organization->logo_path)
                                    <img src="{{ $organization->logo_thumbnail_url }}" 
                                         srcset="{{ $organization->getLogoSrcset(48) }}"
                                         alt="{{ $organization->name }} logo" 
                                         class="h-12 w-12 rounded-full object-contain bg-gray-50">
                                @else
                                    <div class="h-12 w-12 rounded-full bg-gray-200 flex items-center justify-center">
                                        <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <img x-show="imagePreview" :src="imagePreview" alt="Logo preview" class="h-12 w-12 rounded-full object-cover" style="display: none;">
                            <div x-show="removeExisting && !imagePreview" class="h-12 w-12 rounded-full bg-gray-200 flex items-center justify-center" style="display: none;">
                                <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </div>
                            
                            <input type="file" 
                                   name="logo" 
                                   id="logo" 
                                   accept="image/jpeg,image/jpg,image/png,image/svg+xml" 
                                   class="sr-only" 
                                   x-ref="logoInput" 
                                   @change="
                                       const file = $event.target.files[0];
                                       if (file) {
                                           imagePreview = URL.createObjectURL(file);
                                           removeExisting = false;
                                       }
                                   ">
                            <button type="button" 
                                    @click="$refs.logoInput.click()" 
                                    class="rounded-md bg-white px-2.5 py-1.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                                Change
                            </button>
                            
                            @if($organization->logo_path)
                                <label class="flex items-center">
                                    <input type="checkbox" 
                                           name="remove_logo" 
                                           value="1" 
                                           class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500"
                                           @change="
                                               removeExisting = $event.target.checked;
                                               if (removeExisting) {
                                                   imagePreview = null;
                                                   $refs.logoInput.value = '';
                                               }
                                           ">
                                    <span class="ml-2 text-sm text-gray-600">Remove logo</span>
                                </label>
                            @endif
                        </div>
                        <p class="mt-2 text-sm text-gray-600">JPG, PNG or SVG. Max file size 2MB.</p>
                        @error('logo')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-8 flex">
                    <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Save</button>
                </div>
            </form>
        </div>

        <div class="grid max-w-7xl grid-cols-1 gap-x-8 gap-y-10 px-4 py-16 sm:px-6 md:grid-cols-3 lg:px-8">
            <div>
                <h2 class="text-base font-semibold leading-7 text-gray-900">Team members</h2>
                <p class="mt-1 text-sm leading-6 text-gray-600">Add or remove team members for this organization.</p>
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
                       class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                        Manage members
                    </a>
                </div>
            </div>
        </div>

        @if(\App\Models\Organization::count() > 1)
            <div class="grid max-w-7xl grid-cols-1 gap-x-8 gap-y-10 px-4 py-16 sm:px-6 md:grid-cols-3 lg:px-8">
                <div>
                    <h2 class="text-base font-semibold leading-7 text-gray-900">Delete organization</h2>
                    <p class="mt-1 text-sm leading-6 text-gray-600">No longer want to use this organization? You can delete it here. This action is not reversible. All information related to this organization will be deleted permanently.</p>
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
</x-app-layout>