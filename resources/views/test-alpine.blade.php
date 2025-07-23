<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Alpine Test</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="p-8">
        <h1 class="text-2xl mb-4">Alpine.js Test</h1>
        
        <!-- Simple Alpine component -->
        <div x-data="{ open: false }">
            <button @click="open = !open" class="px-4 py-2 bg-blue-500 text-white rounded">
                Toggle (Simple)
            </button>
            <div x-show="open" class="mt-4 p-4 bg-gray-100">
                Simple toggle works!
            </div>
        </div>
        
        <!-- Modal test -->
        <div class="mt-8">
            <button @click="$dispatch('open-modal', 'test-modal')" class="px-4 py-2 bg-green-500 text-white rounded">
                Open Modal
            </button>
        </div>
        
        <!-- Modal component -->
        <x-modal name="test-modal">
            <div class="p-6">
                <h2 class="text-lg font-medium text-gray-900">Test Modal</h2>
                <p class="mt-1 text-sm text-gray-600">If you can see this, the modal is working!</p>
                <div class="mt-6">
                    <button @click="$dispatch('close')" class="px-4 py-2 bg-gray-500 text-white rounded">
                        Close
                    </button>
                </div>
            </div>
        </x-modal>
    </div>
</body>
</html>