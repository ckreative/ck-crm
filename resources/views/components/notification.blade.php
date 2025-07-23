@props(['type' => 'success', 'title' => null, 'message' => null])

@php
    $sessionKey = $type === 'success' ? 'success' : 'error';
    $sessionMessage = $message ?? session($sessionKey);
    
    // Use provided title or fall back to defaults
    if ($sessionMessage && !$title) {
        if ($type === 'success') {
            $title = 'Success!';
        } else {
            $title = 'Error';
        }
    }
    
    $displayTitle = $title ?? '';
    $displayMessage = $sessionMessage ?? '';
@endphp

<!-- Global notification live region, render this permanently at the end of the document -->
<div x-data="{ 
    show: {{ $displayMessage ? 'true' : 'false' }},
    type: '{{ $type }}',
    title: '{{ $displayTitle }}',
    message: '{{ $displayMessage }}',
    init() {
        if (this.show) {
            setTimeout(() => {
                this.show = false;
            }, 5000);
        }
    }
}" 
     aria-live="assertive" 
     class="pointer-events-none fixed inset-0 flex items-end px-4 py-6 sm:items-start sm:p-6 z-50">
  <div class="flex w-full flex-col items-center space-y-4 sm:items-end">
    <div x-show="show"
         x-transition:enter="transform ease-out duration-300 transition"
         x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
         x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="pointer-events-auto w-full max-w-sm rounded-lg bg-white shadow-lg ring-1 ring-black/5">
      <div class="p-4">
        <div class="flex items-start">
          <div class="shrink-0">
            <template x-if="type === 'success'">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon" aria-hidden="true" class="size-6 text-green-400">
                <path d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
            </template>
            <template x-if="type === 'error'">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" data-slot="icon" aria-hidden="true" class="size-6 text-red-400">
                <path d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" stroke-linecap="round" stroke-linejoin="round" />
              </svg>
            </template>
          </div>
          <div class="ml-3 w-0 flex-1 pt-0.5">
            <p class="text-sm font-medium text-gray-900" x-text="title"></p>
            <p class="mt-1 text-sm text-gray-500" x-text="message"></p>
          </div>
          <div class="ml-4 flex shrink-0">
            <button @click="show = false" type="button" class="inline-flex rounded-md bg-white text-gray-400 hover:text-gray-500 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 focus:outline-hidden">
              <span class="sr-only">Close</span>
              <svg viewBox="0 0 20 20" fill="currentColor" data-slot="icon" aria-hidden="true" class="size-5">
                <path d="M6.28 5.22a.75.75 0 0 0-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 1 0 1.06 1.06L10 11.06l3.72 3.72a.75.75 0 1 0 1.06-1.06L11.06 10l3.72-3.72a.75.75 0 0 0-1.06-1.06L10 8.94 6.28 5.22Z" />
              </svg>
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>