<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('My Profile') }}
            </h2>
            <div class="flex items-center space-x-3">
                {{-- Role Badge --}}
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                    {{ Auth::user()->role === 'owner' ? 'bg-red-100 text-red-800' : '' }}
                    {{ Auth::user()->role === 'cashier' ? 'bg-purple-100 text-purple-800' : '' }}
                    {{ Auth::user()->role === 'rider' ? 'bg-cyan-100 text-cyan-800' : '' }}
                    {{ Auth::user()->role === 'customer' ? 'bg-blue-100 text-blue-800' : '' }}">
                    {{ ucfirst(Auth::user()->role) }}
                </span>
                @if(!$canEdit)
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-600">
                        🔒 Read-Only
                    </span>
                @else
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                        👑 Owner Access
                    </span>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-12 bg-gradient-to-br from-blue-50 via-cyan-50 to-blue-100 min-h-screen">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            {{-- Success Message --}}
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg border border-green-200 flex items-center justify-between">
                    <span>{{ session('success') }}</span>
                    <button onclick="this.parentElement.remove()" class="text-green-700 hover:text-green-900">×</button>
                </div>
            @endif

            {{-- Error Messages --}}
            @if($errors->any())
                <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg border border-red-200">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Profile Card --}}
            <div class="bg-white overflow-hidden shadow-xl rounded-2xl border border-blue-100/50 p-8">
                {{-- Header with Avatar --}}
                <div class="flex items-center space-x-4 mb-6 pb-6 border-b border-gray-100">
                    <div class="w-16 h-16 rounded-full flex items-center justify-center text-2xl font-bold text-white
                        {{ Auth::user()->role === 'owner' ? 'bg-red-600' : '' }}
                        {{ Auth::user()->role === 'cashier' ? 'bg-purple-600' : '' }}
                        {{ Auth::user()->role === 'rider' ? 'bg-cyan-600' : '' }}
                        {{ Auth::user()->role === 'customer' ? 'bg-blue-600' : '' }}">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold text-gray-800">
                            {{ $canEdit ? 'Edit Your Profile' : 'Your Profile' }}
                        </h3>
                        <p class="text-sm text-gray-500">
                            @if($canEdit)
                                👑 You are the system owner – you can edit all fields.
                            @else
                                🔒 This profile is read-only. Contact the owner for changes.
                            @endif
                        </p>
                    </div>
                </div>

                {{-- FORM (only if owner) --}}
                @if($canEdit)
                    <form method="POST" action="{{ route('my-profile.update') }}">
                        @csrf
                        @method('PUT')
                @endif

                {{-- Common Fields --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <x-input-label for="name" :value="__('Full Name')" />
                        @if($canEdit)
                            <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" class="block mt-1 w-full border-gray-300 focus:border-blue-400 focus:ring-blue-400 rounded-lg shadow-sm" required />
                        @else
                            <input id="name" type="text" value="{{ $user->name }}" class="block mt-1 w-full border-gray-300 bg-gray-100 text-gray-700 rounded-lg shadow-sm" readonly disabled />
                        @endif
                        <x-input-error :messages="$errors->get('name')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="email" :value="__('Email Address')" />
                        @if($canEdit)
                            <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" class="block mt-1 w-full border-gray-300 focus:border-blue-400 focus:ring-blue-400 rounded-lg shadow-sm" required />
                        @else
                            <input id="email" type="email" value="{{ $user->email }}" class="block mt-1 w-full border-gray-300 bg-gray-100 text-gray-700 rounded-lg shadow-sm" readonly disabled />
                        @endif
                        <x-input-error :messages="$errors->get('email')" class="mt-2" />
                    </div>

                    <div>
                        <x-input-label for="contact_no" :value="__('Contact Number')" />
                        @if($canEdit)
                            <input id="contact_no" name="contact_no" type="text" value="{{ old('contact_no', $user->contact_no) }}" class="block mt-1 w-full border-gray-300 focus:border-blue-400 focus:ring-blue-400 rounded-lg shadow-sm" required />
                        @else
                            <input id="contact_no" type="text" value="{{ $user->contact_no }}" class="block mt-1 w-full border-gray-300 bg-gray-100 text-gray-700 rounded-lg shadow-sm" readonly disabled />
                        @endif
                        <x-input-error :messages="$errors->get('contact_no')" class="mt-2" />
                    </div>
                </div>

                {{-- 🟢 CUSTOMER FIELDS (only if user is customer) --}}
                @if($user->role === 'customer')
                    <div class="mt-6 p-4 bg-blue-50/50 rounded-xl border border-blue-100">
                        <p class="text-xs font-semibold text-blue-700 uppercase tracking-wider mb-3">📍 Delivery Details</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="street_address" :value="__('Street Address')" />
                                @if($canEdit)
                                    <input id="street_address" name="street_address" type="text" value="{{ old('street_address', $user->customerProfile->street_address ?? '') }}" class="block mt-1 w-full border-gray-300 focus:border-blue-400 focus:ring-blue-400 rounded-lg shadow-sm" required />
                                @else
                                    <input id="street_address" type="text" value="{{ $user->customerProfile->street_address ?? 'N/A' }}" class="block mt-1 w-full border-gray-300 bg-gray-100 text-gray-700 rounded-lg shadow-sm" readonly disabled />
                                @endif
                                <x-input-error :messages="$errors->get('street_address')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="barangay" :value="__('Barangay')" />
                                @if($canEdit)
                                    <input id="barangay" name="barangay" type="text" value="{{ old('barangay', $user->customerProfile->barangay ?? 'Poblacion') }}" class="block mt-1 w-full border-gray-300 focus:border-blue-400 focus:ring-blue-400 rounded-lg shadow-sm" />
                                @else
                                    <input id="barangay" type="text" value="{{ $user->customerProfile->barangay ?? 'N/A' }}" class="block mt-1 w-full border-gray-300 bg-gray-100 text-gray-700 rounded-lg shadow-sm" readonly disabled />
                                @endif
                                <x-input-error :messages="$errors->get('barangay')" class="mt-2" />
                            </div>

                            <div class="md:col-span-2">
                                <x-input-label for="delivery_notes" :value="__('Delivery Notes (Landmarks)')" />
                                @if($canEdit)
                                    <textarea id="delivery_notes" name="delivery_notes" class="block mt-1 w-full border-gray-300 focus:border-blue-400 focus:ring-blue-400 rounded-lg shadow-sm">{{ old('delivery_notes', $user->customerProfile->delivery_notes ?? '') }}</textarea>
                                @else
                                    <textarea id="delivery_notes" class="block mt-1 w-full border-gray-300 bg-gray-100 text-gray-700 rounded-lg shadow-sm" readonly disabled>{{ $user->customerProfile->delivery_notes ?? 'None' }}</textarea>
                                @endif
                                <x-input-error :messages="$errors->get('delivery_notes')" class="mt-2" />
                            </div>
                        </div>
                    </div>
                @endif

                {{-- 🟢 RIDER FIELDS (only if user is rider) --}}
                @if($user->role === 'rider')
                    <div class="mt-6 p-4 bg-cyan-50/50 rounded-xl border border-cyan-100">
                        <p class="text-xs font-semibold text-cyan-700 uppercase tracking-wider mb-3">🛵 Vehicle Details</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="vehicle_type" :value="__('Vehicle Type')" />
                                @if($canEdit)
                                    <select id="vehicle_type" name="vehicle_type" class="block mt-1 w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-400 focus:ring-blue-400" required>
                                        <option value="Motorcycle" {{ old('vehicle_type', $user->riderProfile->vehicle_type ?? '') == 'Motorcycle' ? 'selected' : '' }}>Motorcycle</option>
                                        <option value="Tricycle" {{ old('vehicle_type', $user->riderProfile->vehicle_type ?? '') == 'Tricycle' ? 'selected' : '' }}>Tricycle</option>
                                        <option value="E-Bike" {{ old('vehicle_type', $user->riderProfile->vehicle_type ?? '') == 'E-Bike' ? 'selected' : '' }}>E-Bike</option>
                                    </select>
                                @else
                                    <input id="vehicle_type" type="text" value="{{ $user->riderProfile->vehicle_type ?? 'N/A' }}" class="block mt-1 w-full border-gray-300 bg-gray-100 text-gray-700 rounded-lg shadow-sm" readonly disabled />
                                @endif
                                <x-input-error :messages="$errors->get('vehicle_type')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="plate_number" :value="__('Plate Number (Optional)')" />
                                @if($canEdit)
                                    <input id="plate_number" name="plate_number" type="text" value="{{ old('plate_number', $user->riderProfile->plate_number ?? '') }}" class="block mt-1 w-full border-gray-300 focus:border-blue-400 focus:ring-blue-400 rounded-lg shadow-sm" />
                                @else
                                    <input id="plate_number" type="text" value="{{ $user->riderProfile->plate_number ?? 'N/A' }}" class="block mt-1 w-full border-gray-300 bg-gray-100 text-gray-700 rounded-lg shadow-sm" readonly disabled />
                                @endif
                                <x-input-error :messages="$errors->get('plate_number')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="availability_status" :value="__('Availability Status')" />
                                @if($canEdit)
                                    <select id="availability_status" name="availability_status" class="block mt-1 w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-400 focus:ring-blue-400" required>
                                        <option value="available" {{ old('availability_status', $user->riderProfile->availability_status ?? '') == 'available' ? 'selected' : '' }}>Available</option>
                                        <option value="on_delivery" {{ old('availability_status', $user->riderProfile->availability_status ?? '') == 'on_delivery' ? 'selected' : '' }}>On Delivery</option>
                                        <option value="offline" {{ old('availability_status', $user->riderProfile->availability_status ?? '') == 'offline' ? 'selected' : '' }}>Offline</option>
                                    </select>
                                @else
                                    <input id="availability_status" type="text" value="{{ ucfirst($user->riderProfile->availability_status ?? 'N/A') }}" class="block mt-1 w-full border-gray-300 bg-gray-100 text-gray-700 rounded-lg shadow-sm" readonly disabled />
                                @endif
                                <x-input-error :messages="$errors->get('availability_status')" class="mt-2" />
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Password Change Section (only for owner) --}}
                @if($canEdit)
                    <div class="mt-6 p-4 bg-gray-50/80 rounded-xl border border-gray-200">
                        <p class="text-xs font-semibold text-gray-600 uppercase tracking-wider mb-3">🔑 Change Password (Optional)</p>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="password" :value="__('New Password')" />
                                <input id="password" name="password" type="password" class="block mt-1 w-full border-gray-300 focus:border-blue-400 focus:ring-blue-400 rounded-lg shadow-sm" />
                                <p class="text-xs text-gray-400 mt-1">Leave blank to keep current password</p>
                                <x-input-error :messages="$errors->get('password')" class="mt-2" />
                            </div>

                            <div>
                                <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                                <input id="password_confirmation" name="password_confirmation" type="password" class="block mt-1 w-full border-gray-300 focus:border-blue-400 focus:ring-blue-400 rounded-lg shadow-sm" />
                                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Submit / Navigation Buttons --}}
                @if($canEdit)
                    <div class="mt-8 flex items-center justify-end gap-3">
                        <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition text-sm font-medium">
                            Cancel
                        </a>
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition shadow-md text-sm font-medium">
                            💾 Update Profile
                        </button>
                    </div>
                @else
                    <div class="mt-8 text-center text-sm text-gray-500">
                        <p>🔒 Profile is read-only. Only the system owner (<strong>owner@owner.com</strong>) can edit profiles.</p>
                        <a href="{{ route('dashboard') }}" class="inline-block mt-3 px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition shadow-md text-sm font-medium">
                            ← Back to Dashboard
                        </a>
                    </div>
                @endif

                @if($canEdit)
                    </form>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>