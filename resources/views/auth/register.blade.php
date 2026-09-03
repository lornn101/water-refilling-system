<x-guest-layout>
    {{-- Back to Home Link --}}
    <div class="mb-4">
        <a href="{{ url('/') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-blue-600 transition">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            Back to Home
        </a>
    </div>

    <!-- Branding -->
    <div class="text-center mb-6">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 rounded-full mb-3">
            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
            </svg>
        </div>
        <h2 class="text-2xl font-extrabold text-gray-800">Customer Registration</h2>
        <p class="text-sm text-gray-500 mt-1">Join Poblacion Water Refilling Station</p>
        <p class="text-xs text-gray-400 mt-1">⚠️ Accounts require cashier approval before login</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

        <!-- Hidden role – always customer -->
        <input type="hidden" name="role" value="customer">

        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Full Name')" class="text-gray-700 font-medium" />
            <x-text-input id="name" class="block mt-1 w-full border-gray-200 focus:border-blue-400 focus:ring-blue-400 rounded-lg" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email Address')" class="text-gray-700 font-medium" />
            <x-text-input id="email" class="block mt-1 w-full border-gray-200 focus:border-blue-400 focus:ring-blue-400 rounded-lg" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Contact Number -->
        <div class="mt-4">
            <x-input-label for="contact_no" :value="__('Contact Number')" class="text-gray-700 font-medium" />
            <x-text-input id="contact_no" class="block mt-1 w-full border-gray-200 focus:border-blue-400 focus:ring-blue-400 rounded-lg" type="text" name="contact_no" :value="old('contact_no')" required />
            <x-input-error :messages="$errors->get('contact_no')" class="mt-2" />
        </div>

        <!-- Customer Fields (always visible now) -->
        <div class="mt-4 p-4 bg-blue-50/50 rounded-lg border border-blue-100">
            <p class="text-xs font-semibold text-blue-700 uppercase tracking-wider mb-3">📍 Customer Delivery Details</p>
            <div class="mt-2">
                <x-input-label for="street_address" :value="__('Street Address')" class="text-gray-700 font-medium" />
                <x-text-input id="street_address" class="block mt-1 w-full border-gray-200 focus:border-blue-400 focus:ring-blue-400 rounded-lg" type="text" name="street_address" :value="old('street_address')" required />
                <x-input-error :messages="$errors->get('street_address')" class="mt-2" />
            </div>
            <div class="mt-4">
                <x-input-label for="barangay" :value="__('Barangay')" class="text-gray-700 font-medium" />
                <x-text-input id="barangay" class="block mt-1 w-full border-gray-200 focus:border-blue-400 focus:ring-blue-400 rounded-lg" type="text" name="barangay" :value="old('barangay', 'Poblacion')" />
                <x-input-error :messages="$errors->get('barangay')" class="mt-2" />
            </div>
            <div class="mt-4">
                <x-input-label for="delivery_notes" :value="__('Delivery Notes (Landmarks)')" class="text-gray-700 font-medium" />
                <textarea id="delivery_notes" name="delivery_notes" class="block mt-1 w-full border-gray-200 focus:border-blue-400 focus:ring-blue-400 rounded-lg shadow-sm">{{ old('delivery_notes') }}</textarea>
                <x-input-error :messages="$errors->get('delivery_notes')" class="mt-2" />
            </div>
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" class="text-gray-700 font-medium" />
            <x-text-input id="password" class="block mt-1 w-full border-gray-200 focus:border-blue-400 focus:ring-blue-400 rounded-lg" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" class="text-gray-700 font-medium" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full border-gray-200 focus:border-blue-400 focus:ring-blue-400 rounded-lg" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-6">
            <a class="underline text-sm text-blue-600 hover:text-blue-800 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" href="{{ route('login') }}">
                {{ __('Already registered? Login here') }}
            </a>

            <x-primary-button class="ms-4 bg-blue-600 hover:bg-blue-700 active:bg-blue-800 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all shadow-md hover:shadow-lg rounded-lg px-6 py-2">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>