<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ __('Create Delivery Rider Account') }}</h2>
            <a href="{{ route('cashier.users') }}" class="text-blue-600 hover:text-blue-800 text-sm">← Back to User Management</a>
        </div>
    </x-slot>

    <div class="py-12 bg-gradient-to-br from-blue-50 via-cyan-50 to-blue-100 min-h-screen">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl rounded-xl border border-blue-100 p-8">
                <div class="text-center mb-6">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-cyan-100 rounded-full mb-3">
                        <svg class="w-8 h-8 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0zM3 7h18M3 7a2 2 0 012-2h14a2 2 0 012 2M3 7v10a2 2 0 002 2h14a2 2 0 002-2V7"></path></svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800">Add New Rider</h3>
                    <p class="text-sm text-gray-500">Rider accounts are approved immediately.</p>
                </div>

                <form method="POST" action="{{ route('cashier.store-rider') }}">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <x-input-label for="first_name" :value="__('First Name')" />
                            <input id="first_name" name="first_name" type="text" value="{{ old('first_name') }}" class="block mt-1 w-full border-gray-300 focus:border-blue-400 focus:ring-blue-400 rounded-lg shadow-sm" required />
                            <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="middle_name" :value="__('Middle Name')" />
                            <input id="middle_name" name="middle_name" type="text" value="{{ old('middle_name') }}" class="block mt-1 w-full border-gray-300 focus:border-blue-400 focus:ring-blue-400 rounded-lg shadow-sm" />
                            <x-input-error :messages="$errors->get('middle_name')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="last_name" :value="__('Last Name')" />
                            <input id="last_name" name="last_name" type="text" value="{{ old('last_name') }}" class="block mt-1 w-full border-gray-300 focus:border-blue-400 focus:ring-blue-400 rounded-lg shadow-sm" required />
                            <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div>
                            <x-input-label for="email" :value="__('Email Address')" />
                            <input id="email" name="email" type="email" value="{{ old('email') }}" class="block mt-1 w-full border-gray-300 focus:border-blue-400 focus:ring-blue-400 rounded-lg shadow-sm" required />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="contact_no" :value="__('Contact Number')" />
                            <input id="contact_no" name="contact_no" type="text" value="{{ old('contact_no') }}" class="block mt-1 w-full border-gray-300 focus:border-blue-400 focus:ring-blue-400 rounded-lg shadow-sm" required />
                            <x-input-error :messages="$errors->get('contact_no')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="vehicle_type" :value="__('Vehicle Type')" />
                            <select id="vehicle_type" name="vehicle_type" class="block mt-1 w-full border-gray-300 rounded-lg shadow-sm" required>
                                <option value="">Select Vehicle</option>
                                <option value="Motorcycle" {{ old('vehicle_type') == 'Motorcycle' ? 'selected' : '' }}>Motorcycle</option>
                                <option value="Tricycle" {{ old('vehicle_type') == 'Tricycle' ? 'selected' : '' }}>Tricycle</option>
                                <option value="E-Bike" {{ old('vehicle_type') == 'E-Bike' ? 'selected' : '' }}>E-Bike</option>
                            </select>
                            <x-input-error :messages="$errors->get('vehicle_type')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="plate_number" :value="__('Plate Number (Optional)')" />
                            <input id="plate_number" name="plate_number" type="text" value="{{ old('plate_number') }}" class="block mt-1 w-full border-gray-300 focus:border-blue-400 focus:ring-blue-400 rounded-lg shadow-sm" />
                            <x-input-error :messages="$errors->get('plate_number')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="password" :value="__('Password')" />
                            <input id="password" name="password" type="password" class="block mt-1 w-full border-gray-300 focus:border-blue-400 focus:ring-blue-400 rounded-lg shadow-sm" required />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>
                        <div>
                            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                            <input id="password_confirmation" name="password_confirmation" type="password" class="block mt-1 w-full border-gray-300 focus:border-blue-400 focus:ring-blue-400 rounded-lg shadow-sm" required />
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="px-6 py-2 bg-cyan-600 text-white rounded-lg hover:bg-cyan-700 transition shadow-md">Create Rider Account</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>