@props(['title', 'value', 'change' => null, 'changeType' => 'increase', 'icon' => null])

<div class="bg-white overflow-hidden shadow rounded-lg">
    <div class="p-5">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                @if($icon)
                    <div class="rounded-md bg-indigo-500 p-3">
                        {{ $icon }}
                    </div>
                @endif
            </div>
            <div class="ml-5 w-0 flex-1">
                <dl>
                    <dt class="text-sm font-medium text-gray-500 truncate">
                        {{ $title }}
                    </dt>
                    <dd class="flex items-baseline">
                        <div class="text-2xl font-semibold text-gray-900">
                            {{ $value }}
                        </div>
                        @if($change)
                            <div class="ml-2 flex items-baseline text-sm font-semibold {{ $changeType === 'increase' ? 'text-green-600' : 'text-red-600' }}">
                                @if($changeType === 'increase')
                                    <x-heroicon-s-arrow-up class="self-center flex-shrink-0 h-5 w-5 text-green-500" />
                                @else
                                    <x-heroicon-s-arrow-down class="self-center flex-shrink-0 h-5 w-5 text-red-500" />
                                @endif
                                <span class="sr-only">{{ $changeType === 'increase' ? 'Increased' : 'Decreased' }} by</span>
                                {{ $change }}
                            </div>
                        @endif
                    </dd>
                </dl>
            </div>
        </div>
    </div>
</div>