@props(['activities' => []])

<div class="bg-white shadow rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        <h3 class="text-lg font-medium leading-6 text-gray-900">Recent Activity</h3>
        <div class="mt-6 flow-root">
            <ul role="list" class="-mb-8">
                @forelse($activities as $index => $activity)
                    <li>
                        <div class="relative pb-8">
                            @if(!$loop->last)
                                <span class="absolute left-4 top-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                            @endif
                            <div class="relative flex space-x-3">
                                <div>
                                    <span class="h-8 w-8 rounded-full bg-{{ $activity['color'] ?? 'gray' }}-400 flex items-center justify-center ring-8 ring-white">
                                        @if(isset($activity['icon']))
                                            {{ $activity['icon'] }}
                                        @else
                                            <x-heroicon-s-user class="h-5 w-5 text-white" />
                                        @endif
                                    </span>
                                </div>
                                <div class="flex min-w-0 flex-1 justify-between space-x-4 pt-1.5">
                                    <div>
                                        <p class="text-sm text-gray-500">
                                            {{ $activity['description'] }}
                                            @if(isset($activity['user']))
                                                <a href="#" class="font-medium text-gray-900">{{ $activity['user'] }}</a>
                                            @endif
                                        </p>
                                    </div>
                                    <div class="whitespace-nowrap text-right text-sm text-gray-500">
                                        <time datetime="{{ $activity['datetime'] ?? '' }}">{{ $activity['time'] ?? '' }}</time>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </li>
                @empty
                    <li class="text-sm text-gray-500">No recent activity</li>
                @endforelse
            </ul>
        </div>
    </div>
</div>