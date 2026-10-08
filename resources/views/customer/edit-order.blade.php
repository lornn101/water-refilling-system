<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Edit Order') }} #{{ $order->id }}
            </h2>
            <a href="{{ route('customer.orders') }}" class="text-blue-600 hover:text-blue-800 text-sm">← Back to My Orders</a>
        </div>
    </x-slot>

    <div class="py-12 bg-gradient-to-br from-blue-50 via-cyan-50 to-blue-100 min-h-screen">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl rounded-2xl border border-blue-100/50 p-8">
                <div class="text-center mb-6">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-yellow-100 rounded-full mb-3">
                        <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800">Edit Order #{{ $order->id }}</h3>
                    <p class="text-sm text-gray-500">You can modify this order while it's still <span class="font-medium text-yellow-600">pending</span></p>
                </div>

                @if($errors->any())
                    <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg border border-red-200">
                        <ul class="list-disc list-inside">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('customer.update-order', $order->id) }}">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="quantity" :value="__('Number of Gallons')" />
                            <input id="quantity" name="quantity" type="number" min="1" max="20" value="{{ old('quantity', $order->quantity) }}" class="block mt-1 w-full border-gray-300 focus:border-blue-400 focus:ring-blue-400 rounded-lg shadow-sm" required />
                            <x-input-error :messages="$errors->get('quantity')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="contact_number" :value="__('Contact Number')" />
                            <input id="contact_number" name="contact_number" type="text" value="{{ old('contact_number', $order->contact_number) }}" class="block mt-1 w-full border-gray-300 focus:border-blue-400 focus:ring-blue-400 rounded-lg shadow-sm" required />
                            <x-input-error :messages="$errors->get('contact_number')" class="mt-2" />
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="delivery_address" :value="__('Delivery Address')" />
                            <input id="delivery_address" name="delivery_address" type="text" value="{{ old('delivery_address', $order->delivery_address) }}" class="block mt-1 w-full border-gray-300 focus:border-blue-400 focus:ring-blue-400 rounded-lg shadow-sm" required />
                            <x-input-error :messages="$errors->get('delivery_address')" class="mt-2" />
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="delivery_notes" :value="__('Delivery Notes (Optional)')" />
                            <textarea id="delivery_notes" name="delivery_notes" class="block mt-1 w-full border-gray-300 focus:border-blue-400 focus:ring-blue-400 rounded-lg shadow-sm">{{ old('delivery_notes', $order->delivery_notes) }}</textarea>
                            <x-input-error :messages="$errors->get('delivery_notes')" class="mt-2" />
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="delivery_date" :value="__('Preferred Delivery Date')" />
                            <input id="delivery_date" name="delivery_date" type="date" value="{{ old('delivery_date', $order->delivery_date ? $order->delivery_date->format('Y-m-d') : now()->addDay()->format('Y-m-d')) }}" class="block mt-1 w-full border-gray-300 focus:border-blue-400 focus:ring-blue-400 rounded-lg shadow-sm" />
                            <x-input-error :messages="$errors->get('delivery_date')" class="mt-2" />
                        </div>
                    </div>

                    <div class="mt-6 flex items-center justify-end gap-3">
                        <a href="{{ route('customer.orders') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition text-sm font-medium">
                            Cancel
                        </a>
                        <button type="submit" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition shadow-md text-sm font-medium">
                            💾 Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>