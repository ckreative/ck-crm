@props(['user' => null, 'size' => 'h-8 w-8', 'textSize' => 'text-sm'])

@php
    $user = $user ?? Auth::user();
    $name = $user->name ?? 'User';
    
    // Extract initials
    $words = explode(' ', trim($name));
    $initials = '';
    
    if (count($words) === 1) {
        // Single name - take first letter only
        $initials = strtoupper(substr($words[0], 0, 1));
    } else {
        // Multiple names - take first letter of first and last word
        $initials = strtoupper(substr($words[0], 0, 1) . substr($words[count($words) - 1], 0, 1));
    }
    
    // Generate a consistent color based on the name
    $colors = [
        'bg-red-500',
        'bg-orange-500',
        'bg-amber-500',
        'bg-yellow-500',
        'bg-lime-500',
        'bg-green-500',
        'bg-emerald-500',
        'bg-teal-500',
        'bg-cyan-500',
        'bg-sky-500',
        'bg-blue-500',
        'bg-indigo-500',
        'bg-violet-500',
        'bg-purple-500',
        'bg-fuchsia-500',
        'bg-pink-500',
        'bg-rose-500',
    ];
    
    $colorIndex = abs(crc32($name)) % count($colors);
    $bgColor = $colors[$colorIndex];
@endphp

<div {{ $attributes->merge(['class' => "$size $bgColor rounded-full flex items-center justify-center"]) }}>
    <span class="{{ $textSize }} font-medium text-white">{{ $initials }}</span>
</div>