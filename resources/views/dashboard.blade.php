<x-app-layout>
    <div>
        <h1 class="text-2xl font-semibold text-gray-900">Dashboard</h1>
    </div>

    <div class="mt-8">
        <!-- Stats Grid -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <x-stat-card 
                title="Total Users" 
                value="12,345" 
                change="12%" 
                changeType="increase">
                <x-slot name="icon">
                    <x-heroicon-o-users class="h-6 w-6 text-white" />
                </x-slot>
            </x-stat-card>

            <x-stat-card 
                title="Revenue" 
                value="$45,678" 
                change="3.2%" 
                changeType="increase">
                <x-slot name="icon">
                    <x-heroicon-o-currency-dollar class="h-6 w-6 text-white" />
                </x-slot>
            </x-stat-card>

            <x-stat-card 
                title="Active Projects" 
                value="89" 
                change="5%" 
                changeType="decrease">
                <x-slot name="icon">
                    <x-heroicon-o-folder class="h-6 w-6 text-white" />
                </x-slot>
            </x-stat-card>

            <x-stat-card 
                title="Completion Rate" 
                value="98.5%" 
                change="2.1%" 
                changeType="increase">
                <x-slot name="icon">
                    <x-heroicon-o-check-circle class="h-6 w-6 text-white" />
                </x-slot>
            </x-stat-card>
        </div>

        <!-- Main Content Grid -->
        <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-2">
            <!-- Recent Activity -->
            <x-recent-activity :activities="[
                [
                    'description' => 'Created a new project',
                    'user' => 'John Doe',
                    'time' => '1 hour ago',
                    'datetime' => now()->subHour()->toISOString(),
                    'color' => 'green'
                ],
                [
                    'description' => 'Updated user permissions for',
                    'user' => 'Jane Smith',
                    'time' => '3 hours ago',
                    'datetime' => now()->subHours(3)->toISOString(),
                    'color' => 'blue'
                ],
                [
                    'description' => 'Deleted old files from',
                    'user' => 'Project Alpha',
                    'time' => '5 hours ago',
                    'datetime' => now()->subHours(5)->toISOString(),
                    'color' => 'red'
                ],
                [
                    'description' => 'Completed task in',
                    'user' => 'Development Sprint',
                    'time' => '1 day ago',
                    'datetime' => now()->subDay()->toISOString(),
                    'color' => 'green'
                ]
            ]" />

            <!-- Quick Actions -->
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Quick Actions</h3>
                    <div class="mt-5 grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <button type="button" class="inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            <x-heroicon-o-plus class="-ml-1 mr-2 h-5 w-5" />
                            New Project
                        </button>
                        <button type="button" class="inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            <x-heroicon-o-user-plus class="-ml-1 mr-2 h-5 w-5" />
                            Invite User
                        </button>
                        <button type="button" class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            <x-heroicon-o-document-text class="-ml-1 mr-2 h-5 w-5" />
                            View Reports
                        </button>
                        <button type="button" class="inline-flex items-center justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            <x-heroicon-o-cog-6-tooth class="-ml-1 mr-2 h-5 w-5" />
                            Settings
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Content Area -->
        <div class="mt-8">
            <div class="bg-white shadow rounded-lg">
                <div class="px-4 py-5 sm:p-6">
                    <h3 class="text-lg font-medium leading-6 text-gray-900">Welcome back, {{ Auth::user()->name }}!</h3>
                    <div class="mt-2 max-w-xl text-sm text-gray-500">
                        <p>Your dashboard gives you an overview of your system's performance and recent activities. Use the sidebar to navigate to different sections of your application.</p>
                    </div>
                    <div class="mt-3 text-sm">
                        <a href="#" class="font-medium text-indigo-600 hover:text-indigo-500">
                            Learn more about the dashboard
                            <span aria-hidden="true"> &rarr;</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
