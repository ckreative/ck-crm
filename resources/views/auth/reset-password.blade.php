<x-guest-layout>
    <div class="flex min-h-full flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <img src="https://tailwindcss.com/plus-assets/img/logos/mark.svg?color=indigo&shade=600" alt="{{ config('app.name', 'Laravel') }}" class="mx-auto h-10 w-auto" />
            <h2 class="mt-6 text-center text-2xl/9 font-bold tracking-tight text-gray-900">Reset your password</h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                Enter your new password below to regain access to your account.
            </p>
        </div>

        <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-[480px]">
            <div class="bg-white px-6 py-12 shadow-sm sm:rounded-lg sm:px-12">
                <form method="POST" action="{{ route('password.store') }}" class="space-y-6">
                    @csrf

                    <!-- Password Reset Token -->
                    <input type="hidden" name="token" value="{{ $request->route('token') }}">
                    
                    <!-- Email (Hidden) -->
                    <input type="hidden" name="email" value="{{ old('email', $request->email) }}">
                    
                    <!-- Show email errors if any -->
                    <x-input-error :messages="$errors->get('email')" class="mb-4" />

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm/6 font-medium text-gray-900">New password</label>
                        <div class="mt-2">
                            <input id="password" type="password" name="password" required autofocus autocomplete="new-password" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-sm/6 font-medium text-gray-900">Confirm password</label>
                        <div class="mt-2">
                            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" class="block w-full rounded-md bg-white px-3 py-1.5 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-600 sm:text-sm/6" />
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                        </div>
                    </div>

                    <div>
                        <button type="submit" class="flex w-full justify-center rounded-md bg-indigo-600 px-3 py-1.5 text-sm/6 font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Reset password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-guest-layout>