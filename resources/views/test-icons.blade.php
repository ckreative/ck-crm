<!DOCTYPE html>
<html>
<head>
    <title>Icon Test</title>
    @vite(['resources/css/app.css'])
</head>
<body class="p-8">
    <h1 class="text-2xl font-bold mb-4">Testing Heroicons</h1>
    
    <div class="grid grid-cols-4 gap-4">
        <div>
            <p>Outline Home:</p>
            <x-heroicon-o-home class="h-6 w-6" />
        </div>
        
        <div>
            <p>Solid Home:</p>
            <x-heroicon-s-home class="h-6 w-6" />
        </div>
        
        <div>
            <p>Outline Users:</p>
            <x-heroicon-o-users class="h-6 w-6" />
        </div>
        
        <div>
            <p>Mini Bell:</p>
            <x-heroicon-m-bell class="h-5 w-5" />
        </div>
    </div>
    
    <div class="mt-8">
        <h2 class="text-xl font-bold mb-2">Direct SVG Test</h2>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
        </svg>
    </div>
</body>
</html>