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
                    <span class="text-gray-900 font-medium">{{ $lead->name }}</span>
                </nav>

                <div class="flex items-center justify-between">
                    <h1 class="text-2xl font-bold text-gray-900">{{ $lead->name }}</h1>
                    
                    <div class="flex items-center gap-3">
                        <a href="{{ route('leads.edit', $lead) }}"
                           class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            <x-heroicon-o-pencil class="w-4 h-4 mr-2" />
                            Edit
                        </a>

                        @if(!$lead->isArchived())
                            <form action="{{ route('leads.archive', $lead) }}" method="POST" class="inline">
                                @csrf
                                <button type="submit" 
                                        onclick="return confirm('Are you sure you want to archive this lead?')"
                                        class="inline-flex items-center px-4 py-2 bg-white border border-red-300 rounded-md text-sm font-medium text-red-700 hover:bg-red-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                    <x-heroicon-o-archive-box class="w-4 h-4 mr-2" />
                                    Archive
                                </button>
                            </form>
                        @endif
                    </div>
                </div>

                @if($lead->company)
                    <p class="text-lg text-gray-600 mt-2">{{ $lead->company }}</p>
                @endif
                @if($lead->isArchived())
                    <div class="mt-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                        <x-heroicon-o-archive-box class="w-3 h-3 mr-1" />
                        Archived {{ $lead->archived_at->diffForHumans() }}
                    </div>
                @endif
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Contact Information Card -->
                <div class="lg:col-span-2 bg-white shadow-sm ring-1 ring-gray-900/5 rounded-xl">
                    <div class="p-6">
                        <h2 class="text-lg font-semibold text-gray-900 mb-6">Contact Information</h2>
                        
                        <div class="space-y-6">
                            <!-- Email -->
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 bg-indigo-100 rounded-lg flex items-center justify-center">
                                            <x-heroicon-o-envelope class="w-5 h-5 text-indigo-600" />
                                        </div>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium text-gray-900">Email</p>
                                        <p class="text-sm text-gray-600">{{ $lead->email }}</p>
                                    </div>
                                </div>
                                <a href="mailto:{{ $lead->email }}" 
                                   class="inline-flex items-center px-3 py-2 bg-indigo-600 text-white text-sm font-medium rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    <x-heroicon-o-paper-airplane class="w-4 h-4 mr-1" />
                                    Send Email
                                </a>
                            </div>

                            <!-- Phone -->
                            @if($lead->phone)
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center space-x-3">
                                        <div class="flex-shrink-0">
                                            <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
                                                <x-heroicon-o-phone class="w-5 h-5 text-green-600" />
                                            </div>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">Phone</p>
                                            <p class="text-sm text-gray-600">{{ $lead->phone }}</p>
                                        </div>
                                    </div>
                                    <a href="tel:{{ $lead->phone }}" 
                                       class="inline-flex items-center px-3 py-2 bg-green-600 text-white text-sm font-medium rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                        <x-heroicon-o-phone class="w-4 h-4 mr-1" />
                                        Call
                                    </a>
                                </div>
                            @endif

                            <!-- Notes -->
                            @if($lead->notes)
                                <div class="border-t border-gray-200 pt-6">
                                    <div class="flex items-start space-x-3">
                                        <div class="flex-shrink-0">
                                            <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
                                                <x-heroicon-o-document-text class="w-5 h-5 text-gray-600" />
                                            </div>
                                        </div>
                                        <div class="flex-1">
                                            <p class="text-sm font-medium text-gray-900 mb-2">Notes</p>
                                            <div class="text-sm text-gray-600 whitespace-pre-wrap bg-gray-50 rounded-lg p-3">{{ $lead->notes }}</div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Sidebar with Metadata -->
                <div class="space-y-6">
                    <!-- Lead Details Card -->
                    <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-xl p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-6">Lead Details</h3>
                        
                        <div class="space-y-6">
                            <!-- Created Date -->
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                        <x-heroicon-o-plus-circle class="w-4 h-4 text-blue-600" />
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900">Created</p>
                                    <p class="text-sm text-gray-600">{{ $lead->created_at->diffForHumans() }}</p>
                                    <p class="text-xs text-gray-500 mt-1">{{ $lead->created_at->format('M d, Y \a\t g:i A') }}</p>
                                </div>
                            </div>

                            <!-- Last Updated -->
                            <div class="flex items-start space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 bg-orange-100 rounded-lg flex items-center justify-center">
                                        <x-heroicon-o-clock class="w-4 h-4 text-orange-600" />
                                    </div>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900">Last Updated</p>
                                    <p class="text-sm text-gray-600">{{ $lead->updated_at->diffForHumans() }}</p>
                                    <p class="text-xs text-gray-500 mt-1">{{ $lead->updated_at->format('M d, Y \a\t g:i A') }}</p>
                                </div>
                            </div>

                            @if($lead->calcom_event_id)
                                <!-- Cal.com Integration -->
                                <div class="flex items-start space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center">
                                            <x-heroicon-o-calendar-days class="w-4 h-4 text-purple-600" />
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900">Cal.com Event</p>
                                        <p class="text-xs text-gray-600 font-mono bg-gray-50 px-2 py-1 rounded mt-1 break-all">{{ $lead->calcom_event_id }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout> 