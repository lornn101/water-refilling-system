<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Create Delivery Rider Account') }}
            </h2>
            <a href="{{ route('cashier.users') }}" class="text-blue-600 hover:text-blue-800 text-sm">← Back to User Management</a>
        </div>
    </x-slot>

    <div class="py-12 bg-gradient-to-br from-blue-50/50 via-cyan-50/50 to-blue-100/50 min-h-screen">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl rounded-xl border border-blue-100 p-8">
                <div class="text-center mb-6">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-cyan-100 rounded-full mb-3">
                        <svg class="w-8 h-8 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0zM3 7h18M3 7a2 2 0 012-2h14a2 2 0 012 2M3 7v10a2 2 0 002 2h14a2 2 0 002-2V7"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800">Add New Rider</h3>
                    <p class="text-sm text-gray-500">Rider accounts are approved immediately.</p>
                </div>

                <form method="POST" action="{{ route('cashier.store-rider') }}">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="name" :value="__('Full Name')" />
                            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required />
                            <x-input-error :messages="$errors->get('name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="email" :value="__('Email Address')" />
                            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
                            <x-input-error :messages="$errors->get('email')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="contact_no" :value="__('Contact Number')" />
                            <x-text-input id="contact_no" class="block mt-1 w-full" type="text" name="contact_no" :value="old('contact_no')" required />
                            <x-input-error :messages="$errors->get('contact_no')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="vehicle_type" :value="__('Vehicle Type')" />
                            <select id="vehicle_type" name="vehicle_type" class="block mt-1 w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-400 focus:ring-blue-400" required>
                                <option value="">Select Vehicle</option>
                                <option value="Motorcycle">Motorcycle</option>
                                <option value="Tricycle">Tricycle</option>
                                <option value="E-Bike">E-Bike</option>
                            </select>
                            <x-input-error :messages="$errors->get('vehicle_type')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="plate_number" :value="__('Plate Number (Optional)')" />
                            <x-text-input id="plate_number" class="block mt-1 w-full" type="text" name="plate_number" :value="old('plate_number')" />
                            <x-input-error :messages="$errors->get('plate_number')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="password" :value="__('Password')" />
                            <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required />
                            <x-input-error :messages="$errors->get('password')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                            <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required />
                            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <x-primary-button class="bg-cyan-600 hover:bg-cyan-700">
                            {{ __('Create Rider Account') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>