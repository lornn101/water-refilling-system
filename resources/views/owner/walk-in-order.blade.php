<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('New Walk-in Order') }}
            </h2>
            <a href="{{ route('owner.orders') }}" class="text-blue-600 hover:text-blue-800 text-sm">← Back to Orders</a>
        </div>
    </x-slot>

    <div class="py-12 bg-gradient-to-br from-blue-50 via-cyan-50 to-blue-100 min-h-screen">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl rounded-2xl border border-blue-100/50 p-8">
                <div class="text-center mb-6">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-cyan-100 rounded-full mb-3">
                        <svg class="w-8 h-8 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-800">Walk-in Customer Order</h3>
                    <p class="text-sm text-gray-500">For customers who come directly to the station</p>
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

                <form method="POST" action="{{ route('owner.walk-in-store') }}">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <x-input-label for="walk_in_customer_name" :value="__('Customer Name')" />
                            <input id="walk_in_customer_name" name="walk_in_customer_name" type="text" value="{{ old('walk_in_customer_name') }}" placeholder="e.g., Juan Dela Cruz" class="block mt-1 w-full border-gray-300 focus:border-blue-400 focus:ring-blue-400 rounded-lg shadow-sm" required />
                            <x-input-error :messages="$errors->get('walk_in_customer_name')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="walk_in_contact" :value="__('Contact Number (Optional)')" />
                            <input id="walk_in_contact" name="walk_in_contact" type="text" value="{{ old('walk_in_contact') }}" placeholder="09123456789" class="block mt-1 w-full border-gray-300 focus:border-blue-400 focus:ring-blue-400 rounded-lg shadow-sm" />
                            <x-input-error :messages="$errors->get('walk_in_contact')" class="mt-2" />
                        </div>

                        <div>
                            <x-input-label for="quantity" :value="__('Number of Gallons')" />
                            <input id="quantity" name="quantity" type="number" min="1" max="50" value="{{ old('quantity', 1) }}" oninput="calculateTotal()" class="block mt-1 w-full border-gray-300 focus:border-blue-400 focus:ring-blue-400 rounded-lg shadow-sm" required />
                            <x-input-error :messages="$errors->get('quantity')" class="mt-2" />
                        </div>

                        {{-- Order Type --}}
                        <div class="md:col-span-2 p-4 bg-cyan-50/50 rounded-xl border border-cyan-100">
                            <p class="text-xs font-semibold text-cyan-700 uppercase tracking-wider mb-3">🎯 Order Type</p>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <label class="flex items-start p-4 bg-white rounded-lg border-2 border-gray-200 cursor-pointer hover:border-cyan-400 transition has-[:checked]:border-cyan-600 has-[:checked]:bg-cyan-50">
                                    <input type="radio" name="order_type" value="refill" {{ old('order_type', 'refill') == 'refill' ? 'checked' : '' }} class="mt-1 text-cyan-600 focus:ring-cyan-500" onchange="toggleDeliveryFields(); calculateTotal()">
                                    <div class="ml-3">
                                        <p class="font-semibold text-gray-800">🏪 Walk-in Refill</p>
                                        <p class="text-xs text-gray-500 mt-1">Customer refills at station</p>
                                        <p class="text-xs text-emerald-600 font-medium mt-1">✓ No delivery fee</p>
                                    </div>
                                </label>

                                <label class="flex items-start p-4 bg-white rounded-lg border-2 border-gray-200 cursor-pointer hover:border-cyan-400 transition has-[:checked]:border-cyan-600 has-[:checked]:bg-cyan-50">
                                    <input type="radio" name="order_type" value="delivery" {{ old('order_type') == 'delivery' ? 'checked' : '' }} class="mt-1 text-cyan-600 focus:ring-cyan-500" onchange="toggleDeliveryFields(); calculateTotal()">
                                    <div class="ml-3">
                                        <p class="font-semibold text-gray-800">🛵 Delivery Request</p>
                                        <p class="text-xs text-gray-500 mt-1">Needs rider</p>
                                        <p class="text-xs text-blue-600 font-medium mt-1">+ ₱{{ number_format($deliveryFee, 2) }} delivery fee</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        {{-- Delivery Address --}}
                        <div id="delivery-fields" class="md:col-span-2 hidden">
                            <div class="p-4 bg-blue-50/50 rounded-xl border border-blue-100">
                                <x-input-label for="delivery_address" :value="__('Delivery Address')" />
                                <input id="delivery_address" name="delivery_address" type="text" value="{{ old('delivery_address') }}" placeholder="Street, Barangay, Landmarks" class="block mt-1 w-full border-gray-300 focus:border-blue-400 focus:ring-blue-400 rounded-lg shadow-sm" />
                                <x-input-error :messages="$errors->get('delivery_address')" class="mt-2" />
                            </div>
                        </div>

                        <div class="md:col-span-2">
                            <x-input-label for="delivery_notes" :value="__('Notes (Optional)')" />
                            <textarea id="delivery_notes" name="delivery_notes" rows="2" placeholder="Any additional notes..." class="block mt-1 w-full border-gray-300 focus:border-blue-400 focus:ring-blue-400 rounded-lg shadow-sm">{{ old('delivery_notes') }}</textarea>
                            <x-input-error :messages="$errors->get('delivery_notes')" class="mt-2" />
                        </div>
                    </div>

                    {{-- Total Preview --}}
                    <div class="mt-6 p-4 bg-gradient-to-r from-blue-50 to-cyan-50 rounded-xl border border-blue-200">
                        <div class="flex justify-between items-center">
                            <div>
                                <p class="text-xs text-gray-500 uppercase tracking-wider">Live Total Calculation</p>
                                <p class="text-sm text-gray-600 mt-1">
                                    <span id="preview-qty">1</span> gallon × ₱{{ number_format($pricePerGallon, 2) }}
                                    <span id="preview-fee" class="hidden"> + ₱{{ number_format($deliveryFee, 2) }} delivery</span>
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-xs text-gray-500 uppercase tracking-wider">Total Amount</p>
                                <p class="text-3xl font-bold text-blue-700">₱<span id="preview-total">0.00</span></p>
                            </div>
                        </div>
                    </div>

                    {{-- Mark as Paid (only for refill) --}}
                    <div id="paid-section" class="mt-4 p-4 bg-emerald-50/50 rounded-xl border border-emerald-100">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="mark_paid" value="1" {{ old('mark_paid') ? 'checked' : '' }} class="rounded border-gray-300 text-emerald-600 focus:ring-emerald-500">
                            <div class="ml-3">
                                <p class="font-medium text-gray-800">💵 Customer paid immediately (Cash)</p>
                                <p class="text-xs text-gray-500">Check this if the customer already paid. Leave unchecked if unpaid.</p>
                            </div>
                        </label>
                    </div>

                    <div class="mt-6 flex items-center justify-end gap-3">
                        <a href="{{ route('owner.orders') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition text-sm font-medium">
                            Cancel
                        </a>
                        <button type="submit" class="px-6 py-2 bg-cyan-600 text-white rounded-lg hover:bg-cyan-700 transition shadow-md text-sm font-medium">
                            ✅ Save Walk-in Order
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const pricePerGallon = {{ $pricePerGallon }};
        const deliveryFee = {{ $deliveryFee }};

        function toggleDeliveryFields() {
            const deliveryRadio = document.querySelector('input[name="order_type"][value="delivery"]');
            const deliveryFields = document.getElementById('delivery-fields');
            const addressInput = document.getElementById('delivery_address');
            const paidSection = document.getElementById('paid-section');
            const previewFee = document.getElementById('preview-fee');

            if (deliveryRadio && deliveryRadio.checked) {
                deliveryFields.classList.remove('hidden');
                addressInput.required = true;
                paidSection.classList.add('hidden');
                previewFee.classList.remove('hidden');
            } else {
                deliveryFields.classList.add('hidden');
                addressInput.required = false;
                paidSection.classList.remove('hidden');
                previewFee.classList.add('hidden');
            }
            calculateTotal();
        }

        function calculateTotal() {
            const qty = parseInt(document.getElementById('quantity').value) || 0;
            const deliveryRadio = document.querySelector('input[name="order_type"][value="delivery"]');
            const isDelivery = deliveryRadio && deliveryRadio.checked;

            const subtotal = qty * pricePerGallon;
            const fee = isDelivery ? deliveryFee : 0;
            const total = subtotal + fee;

            document.getElementById('preview-qty').textContent = qty;
            document.getElementById('preview-total').textContent = total.toFixed(2);
        }

        document.addEventListener('DOMContentLoaded', function() {
            toggleDeliveryFields();
            calculateTotal();
        });
    </script>
</x-app-layout>