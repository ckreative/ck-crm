<x-app-layout>
    <div class="px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">
            <!-- Header with Breadcrumb -->
            <div class="py-6">
                <nav class="flex items-center space-x-2 text-sm text-gray-500 mb-4">
                    <a href="{{ route('leads.index') }}" class="hover:text-gray-700">Leads</a>
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 111.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                    <a href="{{ route('leads.show', $lead) }}" class="hover:text-gray-700">{{ $lead->name }}</a>
                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 111.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" />
                    </svg>
                    <span class="text-gray-900 font-medium">Edit</span>
                </nav>

                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">Edit Lead</h1>
                        <p class="mt-2 text-sm text-gray-600">Update {{ $lead->name }}'s information and contact details.</p>
                    </div>
                    @if($lead->isArchived())
                        <div class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            <x-heroicon-o-archive-box class="w-3 h-3 mr-1" />
                            Archived Lead
                        </div>
                    @endif
                </div>
            </div>

            <form method="POST" action="{{ route('leads.update', $lead) }}" class="space-y-8">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Main Information -->
                    <div class="lg:col-span-2">
                        <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-xl p-6">
                            <div class="pb-6 border-b border-gray-200">
                                <h2 class="text-lg font-semibold text-gray-900">Contact Information</h2>
                                <p class="mt-1 text-sm text-gray-600">Essential details for reaching this lead.</p>
                            </div>

                            <div class="mt-6 space-y-6">
                                <!-- Name (Required) -->
                                <div>
                                    <div class="flex items-center">
                                        <x-input-label for="name" value="Full Name" class="text-sm font-semibold text-gray-900" />
                                        <span class="ml-1 text-red-500">*</span>
                                    </div>
                                    <x-text-input id="name" 
                                                  name="name" 
                                                  type="text" 
                                                  class="mt-2 block w-full" 
                                                  :value="old('name', $lead->name)"
                                                  placeholder="Enter lead's full name"
                                                  required />
                                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                </div>

                                <!-- Email (Required) -->
                                <div>
                                    <div class="flex items-center">
                                        <x-input-label for="email" value="Email Address" class="text-sm font-semibold text-gray-900" />
                                        <span class="ml-1 text-red-500">*</span>
                                    </div>
                                    <x-text-input id="email" 
                                                  name="email" 
                                                  type="email" 
                                                  class="mt-2 block w-full" 
                                                  :value="old('email', $lead->email)"
                                                  placeholder="lead@company.com"
                                                  required />
                                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                </div>

                                <!-- Phone and Company Grid -->
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                                    <div>
                                        <x-input-label for="phone" value="Phone Number" class="text-sm font-semibold text-gray-900" />
                                        <x-text-input id="phone" 
                                                      name="phone" 
                                                      type="tel" 
                                                      class="mt-2 block w-full" 
                                                      :value="old('phone', $lead->phone)"
                                                      placeholder="(555) 123-4567" />
                                        <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                                    </div>

                                    <div>
                                        <x-input-label for="company" value="Company" class="text-sm font-semibold text-gray-900" />
                                        <x-text-input id="company" 
                                                      name="company" 
                                                      type="text" 
                                                      class="mt-2 block w-full" 
                                                      :value="old('company', $lead->company)"
                                                      placeholder="Company name" />
                                        <x-input-error :messages="$errors->get('company')" class="mt-2" />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Notes Section -->
                        <div class="mt-8 bg-white shadow-sm ring-1 ring-gray-900/5 rounded-xl p-6">
                            <div class="pb-6 border-b border-gray-200">
                                <h2 class="text-lg font-semibold text-gray-900">Additional Notes</h2>
                                <p class="mt-1 text-sm text-gray-600">Add any relevant information about this lead.</p>
                            </div>

                            <div class="mt-6">
                                <x-input-label for="notes" value="Notes" class="text-sm font-semibold text-gray-900" />
                                <textarea id="notes" 
                                          name="notes" 
                                          rows="4"
                                          class="mt-2 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm resize-none"
                                          placeholder="Add notes about conversations, preferences, or other relevant details...">{{ old('notes', $lead->notes) }}</textarea>
                                <x-input-error :messages="$errors->get('notes')" class="mt-2" />
                                <p class="mt-2 text-xs text-gray-500">These notes are private and only visible to your team.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="space-y-6">
                        <!-- Lead Status Card -->
                        <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-xl p-6">
                            <h3 class="text-lg font-semibold text-gray-900 mb-4">Lead Status</h3>
                            
                            @if($lead->isArchived())
                                <div class="flex items-center space-x-3 p-3 bg-yellow-50 rounded-lg">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 bg-yellow-100 rounded-lg flex items-center justify-center">
                                            <x-heroicon-o-archive-box class="w-4 h-4 text-yellow-600" />
                                        </div>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-yellow-800">Archived</p>
                                        <p class="text-xs text-yellow-600">{{ $lead->archived_at->diffForHumans() }}</p>
                                    </div>
                                </div>
                            @else
                                <div class="flex items-center space-x-3 p-3 bg-green-50 rounded-lg">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 bg-green-100 rounded-lg flex items-center justify-center">
                                            <div class="w-2 h-2 bg-green-500 rounded-full"></div>
                                        </div>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-green-800">Active</p>
                                        <p class="text-xs text-green-600">Ready for engagement</p>
                                    </div>
                                </div>
                            @endif
                        </div>

                        @if($lead->calcom_event_id)
                            <!-- Integration Info -->
                            <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-xl p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Integration</h3>
                                
                                <div class="flex items-start space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                                            <x-heroicon-o-calendar-days class="w-4 h-4 text-purple-600" />
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900">Cal.com</p>
                                        <p class="text-xs text-gray-500 mt-1">Imported from calendar booking</p>
                                        <p class="text-xs text-gray-600 font-mono bg-gray-50 px-2 py-1 rounded mt-2 break-all">{{ $lead->calcom_event_id }}</p>
                                    </div>
                                </div>
                            </div>
                        @endif

                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                    <a href="{{ route('leads.show', $lead) }}"
                       class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <x-heroicon-o-arrow-left class="w-4 h-4 mr-2" />
                        Cancel
                    </a>
                    
                    <button type="submit"
                            class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <x-heroicon-o-check class="w-4 h-4 mr-2" />
                        Update Lead
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout> 