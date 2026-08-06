<x-guest-layout>
    {{-- Back to Home Link --}}
    <div class="mb-4">
        <a href="{{ url('/') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-blue-600 transition">
            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
            </svg>
            
        </a>
    </div>

    <!-- Branding -->
    <div class="text-center mb-6">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-100 rounded-full mb-3">
            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
            </svg>
        </div>
        <h2 class="text-2xl font-extrabold text-gray-800">Create Account</h2>
        <p class="text-sm text-gray-500 mt-1">Join Poblacion Water Refilling Station</p>
    </div>

    <form method="POST" action="{{ route('register') }}">
        @csrf

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

        <!-- Role Selection -->
        <div class="mt-4">
            <x-input-label for="role" :value="__('Register as')" class="text-gray-700 font-medium" />
            <select id="role" name="role" class="block mt-1 w-full border-gray-200 focus:border-blue-400 focus:ring-blue-400 rounded-lg shadow-sm" required>
                <option value="customer" {{ old('role') == 'customer' ? 'selected' : '' }}>Customer</option>
                <option value="rider" {{ old('role') == 'rider' ? 'selected' : '' }}>Delivery Rider</option>
                <option value="cashier" {{ old('role') == 'cashier' ? 'selected' : '' }}>Cashier / Owner</option>
            </select>
            <x-input-error :messages="$errors->get('role')" class="mt-2" />
        </div>

        <!-- Customer Fields -->
        <div id="customer-fields" style="display: none;" class="mt-4 p-4 bg-blue-50/50 rounded-lg border border-blue-100">
            <p class="text-xs font-semibold text-blue-700 uppercase tracking-wider mb-3">📍 Customer Delivery Details</p>
            <div class="mt-2">
                <x-input-label for="street_address" :value="__('Street Address')" class="text-gray-700 font-medium" />
                <x-text-input id="street_address" class="block mt-1 w-full border-gray-200 focus:border-blue-400 focus:ring-blue-400 rounded-lg" type="text" name="street_address" :value="old('street_address')" />
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

        <!-- Rider Fields -->
        <div id="rider-fields" style="display: none;" class="mt-4 p-4 bg-cyan-50/50 rounded-lg border border-cyan-100">
            <p class="text-xs font-semibold text-cyan-700 uppercase tracking-wider mb-3">🛵 Rider Vehicle Details</p>
            <div class="mt-2">
                <x-input-label for="vehicle_type" :value="__('Vehicle Type')" class="text-gray-700 font-medium" />
                <select id="vehicle_type" name="vehicle_type" class="block mt-1 w-full border-gray-200 focus:border-blue-400 focus:ring-blue-400 rounded-lg shadow-sm">
                    <option value="">Select Vehicle</option>
                    <option value="Motorcycle" {{ old('vehicle_type') == 'Motorcycle' ? 'selected' : '' }}>Motorcycle</option>
                    <option value="Tricycle" {{ old('vehicle_type') == 'Tricycle' ? 'selected' : '' }}>Tricycle</option>
                    <option value="E-Bike" {{ old('vehicle_type') == 'E-Bike' ? 'selected' : '' }}>E-Bike</option>
                </select>
                <x-input-error :messages="$errors->get('vehicle_type')" class="mt-2" />
            </div>
            <div class="mt-4">
                <x-input-label for="plate_number" :value="__('Plate Number (Optional)')" class="text-gray-700 font-medium" />
                <x-text-input id="plate_number" class="block mt-1 w-full border-gray-200 focus:border-blue-400 focus:ring-blue-400 rounded-lg" type="text" name="plate_number" :value="old('plate_number')" />
                <x-input-error :messages="$errors->get('plate_number')" class="mt-2" />
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const roleSelect = document.getElementById('role');
            const customerFields = document.getElementById('customer-fields');
            const riderFields = document.getElementById('rider-fields');

            function toggleFields() {
                const role = roleSelect.value;
                if (role === 'customer') {
                    customerFields.style.display = 'block';
                    riderFields.style.display = 'none';
                } else if (role === 'rider') {
                    customerFields.style.display = 'none';
                    riderFields.style.display = 'block';
                } else {
                    customerFields.style.display = 'none';
                    riderFields.style.display = 'none';
                }
            }

            roleSelect.addEventListener('change', toggleFields);
            toggleFields();
        });
    </script>
</x-guest-layout>