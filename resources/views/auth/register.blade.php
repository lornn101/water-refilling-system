<x-guest-layout>
    <form method="POST" action="{{ route('register') }}">
        @csrf

    @if ($errors->any())
    <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-md">
        <strong>Please fix the following errors:</strong>
        <ul class="list-disc list-inside mt-2">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
    @endif
        <!-- Name -->
        <div>
            <x-input-label for="name" :value="__('Name')" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <!-- Email Address -->
        <div class="mt-4">
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Contact Number -->
        <div class="mt-4">
            <x-input-label for="contact_no" :value="__('Contact Number')" />
            <x-text-input id="contact_no" class="block mt-1 w-full" type="text" name="contact_no" :value="old('contact_no')" required />
            <x-input-error :messages="$errors->get('contact_no')" class="mt-2" />
        </div>

        <!-- Role Selection -->
        <div class="mt-4">
            <x-input-label for="role" :value="__('Register as')" />
            <select id="role" name="role" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                <option value="customer" {{ old('role') == 'customer' ? 'selected' : '' }}>Customer</option>
                <option value="rider" {{ old('role') == 'rider' ? 'selected' : '' }}>Delivery Rider</option>
                <option value="cashier" {{ old('role') == 'cashier' ? 'selected' : '' }}>Cashier / Owner</option>
            </select>
            <x-input-error :messages="$errors->get('role')" class="mt-2" />
        </div>

        <!-- Customer Fields (hidden by default) -->
        <div id="customer-fields" style="display: none;">
            <div class="mt-4">
                <x-input-label for="street_address" :value="__('Street Address')" />
                <x-text-input id="street_address" class="block mt-1 w-full" type="text" name="street_address" :value="old('street_address')" />
                <x-input-error :messages="$errors->get('street_address')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="barangay" :value="__('Barangay')" />
                <x-text-input id="barangay" class="block mt-1 w-full" type="text" name="barangay" :value="old('barangay', 'Poblacion')" />
                <x-input-error :messages="$errors->get('barangay')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="delivery_notes" :value="__('Delivery Notes (e.g., landmarks)')" />
                <textarea id="delivery_notes" name="delivery_notes" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('delivery_notes') }}</textarea>
                <x-input-error :messages="$errors->get('delivery_notes')" class="mt-2" />
            </div>
        </div>

        <!-- Rider Fields (hidden by default) -->
        <div id="rider-fields" style="display: none;">
            <div class="mt-4">
                <x-input-label for="vehicle_type" :value="__('Vehicle Type')" />
                <select id="vehicle_type" name="vehicle_type" class="block mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                    <option value="">Select Vehicle</option>
                    <option value="Motorcycle" {{ old('vehicle_type') == 'Motorcycle' ? 'selected' : '' }}>Motorcycle</option>
                    <option value="Tricycle" {{ old('vehicle_type') == 'Tricycle' ? 'selected' : '' }}>Tricycle</option>
                    <option value="E-Bike" {{ old('vehicle_type') == 'E-Bike' ? 'selected' : '' }}>E-Bike</option>
                </select>
                <x-input-error :messages="$errors->get('vehicle_type')" class="mt-2" />
            </div>

            <div class="mt-4">
                <x-input-label for="plate_number" :value="__('Plate Number (Optional)')" />
                <x-text-input id="plate_number" class="block mt-1 w-full" type="text" name="plate_number" :value="old('plate_number')" />
                <x-input-error :messages="$errors->get('plate_number')" class="mt-2" />
            </div>
        </div>

        <!-- Password -->
        <div class="mt-4">
            <x-input-label for="password" :value="__('Password')" />
            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirm Password -->
        <div class="mt-4">
            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ms-4">
                {{ __('Register') }}
            </x-primary-button>
        </div>
    </form>

    <script>
        // Show/hide fields based on role selection
        document.addEventListener('DOMContentLoaded', function() {
            const roleSelect = document.getElementById('role');
            const customerFields = document.getElementById('customer-fields');
            const riderFields = document.getElementById('rider-fields');

            function toggleFields() {
                const role = roleSelect.value;
                if (role === 'customer') {
                    customerFields.style.display = 'block';
                    riderFields.style.display = 'none';
                    // Make customer fields required
                    document.getElementById('street_address').required = true;
                    document.getElementById('vehicle_type').required = false;
                } else if (role === 'rider') {
                    customerFields.style.display = 'none';
                    riderFields.style.display = 'block';
                    document.getElementById('street_address').required = false;
                    document.getElementById('vehicle_type').required = true;
                } else { // cashier
                    customerFields.style.display = 'none';
                    riderFields.style.display = 'none';
                    document.getElementById('street_address').required = false;
                    document.getElementById('vehicle_type').required = false;
                }
            }

            roleSelect.addEventListener('change', toggleFields);
            toggleFields(); // Set initial state
        });
    </script>
</x-guest-layout>