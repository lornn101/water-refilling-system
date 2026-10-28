<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Business Settings') }}
            </h2>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                👑 Owner Only
            </span>
        </div>
    </x-slot>

    <div class="py-12 bg-gradient-to-br from-blue-50 via-cyan-50 to-blue-100 min-h-screen">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg border border-green-200 flex justify-between items-center">
                    <span>{{ session('success') }}</span>
                    <button onclick="this.parentElement.remove()" class="text-green-700 hover:text-green-900">×</button>
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg border border-red-200">
                    <ul class="list-disc list-inside">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-xl rounded-2xl border border-blue-100/50 p-8">
                <div class="text-center mb-8">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-red-100 rounded-full mb-3">
                        <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800">Business Settings</h3>
                    <p class="text-sm text-gray-500">Configure pricing and business information</p>
                </div>

                <form method="POST" action="{{ route('owner.settings.update') }}">
                    @csrf
                    @method('PUT')

                    {{-- Pricing Section --}}
                    <div class="p-4 bg-blue-50/50 rounded-xl border border-blue-100 mb-6">
                        <h4 class="text-sm font-semibold text-blue-700 uppercase tracking-wider mb-4">💰 Pricing</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <x-input-label for="price_per_gallon" :value="__('Price per Gallon (₱)')" />
                                <input id="price_per_gallon" name="price_per_gallon" type="number" step="0.01" min="0" max="9999" value="{{ old('price_per_gallon', $settings['price_per_gallon']) }}" class="block mt-1 w-full border-gray-300 focus:border-blue-400 focus:ring-blue-400 rounded-lg shadow-sm" required />
                                <p class="text-xs text-gray-500 mt-1">This price applies to new orders only. Existing orders keep their original price.</p>
                                <x-input-error :messages="$errors->get('price_per_gallon')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="delivery_fee" :value="__('Delivery Fee (₱)')" />
                                <input id="delivery_fee" name="delivery_fee" type="number" step="0.01" min="0" max="9999" value="{{ old('delivery_fee', $settings['delivery_fee']) }}" class="block mt-1 w-full border-gray-300 focus:border-blue-400 focus:ring-blue-400 rounded-lg shadow-sm" required />
                                <p class="text-xs text-gray-500 mt-1">Applied to online and walk-in delivery orders.</p>
                                <x-input-error :messages="$errors->get('delivery_fee')" class="mt-2" />
                            </div>
                        </div>
                    </div>

                    {{-- Business Info Section --}}
                    <div class="p-4 bg-cyan-50/50 rounded-xl border border-cyan-100">
                        <h4 class="text-sm font-semibold text-cyan-700 uppercase tracking-wider mb-4">🏢 Business Information (for Receipts)</h4>
                        <div class="space-y-4">
                            <div>
                                <x-input-label for="business_name" :value="__('Business Name')" />
                                <input id="business_name" name="business_name" type="text" value="{{ old('business_name', $settings['business_name']) }}" class="block mt-1 w-full border-gray-300 focus:border-blue-400 focus:ring-blue-400 rounded-lg shadow-sm" required />
                                <x-input-error :messages="$errors->get('business_name')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="business_address" :value="__('Business Address')" />
                                <input id="business_address" name="business_address" type="text" value="{{ old('business_address', $settings['business_address']) }}" class="block mt-1 w-full border-gray-300 focus:border-blue-400 focus:ring-blue-400 rounded-lg shadow-sm" required />
                                <x-input-error :messages="$errors->get('business_address')" class="mt-2" />
                            </div>
                            <div>
                                <x-input-label for="business_contact" :value="__('Business Contact')" />
                                <input id="business_contact" name="business_contact" type="text" value="{{ old('business_contact', $settings['business_contact']) }}" class="block mt-1 w-full border-gray-300 focus:border-blue-400 focus:ring-blue-400 rounded-lg shadow-sm" required />
                                <x-input-error :messages="$errors->get('business_contact')" class="mt-2" />
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <a href="{{ route('dashboard') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition text-sm font-medium">Cancel</a>
                        <button type="submit" class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition shadow-md text-sm font-medium">
                            💾 Save Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>