<x-guest-layout>
    <div class="text-center">
        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
            <x-heroicon-o-x-mark class="h-6 w-6 text-red-600" />
        </div>
        <h2 class="mt-4 text-2xl font-bold text-gray-900">Invitation Expired</h2>
        <p class="mt-2 text-sm text-gray-600">This invitation has expired and is no longer valid.</p>
        <p class="mt-1 text-sm text-gray-600">Please contact the administrator who sent you the invitation to request a new one.</p>
        
        <div class="mt-6">
            <a href="{{ route('login') }}" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                Go to Login
            </a>
        </div>
    </div>
</x-guest-layout>