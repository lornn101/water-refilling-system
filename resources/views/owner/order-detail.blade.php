<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Order Details') }} #{{ $order->id }}
            </h2>
            <a href="{{ route('owner.orders') }}" class="text-blue-600 hover:text-blue-800 text-sm">← Back to Orders</a>
        </div>
    </x-slot>

    <div class="py-12 bg-gradient-to-br from-blue-50 via-cyan-50 to-blue-100 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg border border-green-200">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg border border-red-200">{{ session('error') }}</div>
            @endif

            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                {{-- Order Info --}}
                <div class="p-6 border-b border-gray-200">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        <div>
                            <p class="text-xs text-gray-500">Order ID</p>
                            <p class="font-semibold text-gray-800">#{{ $order->id }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Status</p>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $order->getStatusBadgeColor() }}">
    {{ $order->getStatusIcon() }} {{ $order->getStatusLabel() }}
</span>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Quantity</p>
                            <p class="font-semibold text-gray-800">{{ $order->quantity }} gallon(s)</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Placed On</p>
                            <p class="font-semibold text-gray-800">{{ $order->created_at->format('M d, Y h:i A') }}</p>
                        </div>
                    </div>
                </div>

                {{-- Customer Info --}}
                <div class="p-6 border-b border-gray-200 bg-blue-50/30">
                    <h3 class="font-semibold text-gray-800 mb-3">👤 Customer Details</h3>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                        <div>
                            <p class="text-xs text-gray-500">Name</p>
                            <p class="font-medium text-gray-800">{{ $order->customer->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Contact</p>
                            <p class="font-medium text-gray-800">{{ $order->contact_number }}</p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-xs text-gray-500">Address</p>
                            <p class="font-medium text-gray-800">{{ $order->delivery_address }}</p>
                        </div>
                        @if($order->delivery_notes)
                            <div class="col-span-2">
                                <p class="text-xs text-gray-500">Delivery Notes</p>
                                <p class="font-medium text-gray-800">{{ $order->delivery_notes }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Rider Assignment --}}
                <div class="p-6 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-800 mb-3">🛵 Rider Assignment</h3>
                    <div class="flex flex-wrap items-center gap-4">
                        <div>
                            <p class="text-xs text-gray-500">Current Rider</p>
                            <p class="font-medium text-gray-800">
                                {{ $order->rider->name ?? 'Not Assigned' }}
                            </p>
                        </div>
                        @if($order->status === 'pending' || $order->status === 'assigned')
                            <form method="POST" action="{{ route('owner.assign-rider', $order->id) }}" class="flex items-center gap-2">
                                @csrf
                                <select name="rider_id" class="border-gray-300 rounded-lg focus:border-blue-400 focus:ring-blue-400 text-sm">
                                    <option value="">Select Rider</option>
                                    @foreach($riders as $rider)
                                        <option value="{{ $rider->id }}" {{ $order->rider_id == $rider->id ? 'selected' : '' }}>
                                            {{ $rider->name }} ({{ $rider->riderProfile->vehicle_type ?? 'N/A' }})
                                        </option>
                                    @endforeach
                                </select>
                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium">
                                    Assign
                                </button>
                            </form>
                        @else
                            <span class="text-sm text-gray-500">Rider cannot be changed for this order status.</span>
                        @endif
                    </div>
                </div>

                {{-- Update Status --}}
                <div class="p-6">
                    <h3 class="font-semibold text-gray-800 mb-3">📊 Update Status</h3>
                    <form method="POST" action="{{ route('owner.update-status', $order->id) }}" class="flex items-center gap-3">
                        @csrf
                        <select name="status" class="border-gray-300 rounded-lg focus:border-blue-400 focus:ring-blue-400 text-sm">
    <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>⏳ Pending</option>
    <option value="assigned" {{ $order->status === 'assigned' ? 'selected' : '' }}>📋 Assigned</option>
    <option value="on_delivery" {{ $order->status === 'on_delivery' ? 'selected' : '' }}>🚚 On Delivery</option>
    <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>✅ Delivered</option>
    <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>🏪 Completed (Walk-in Refill)</option>
    <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>❌ Cancelled</option>
</select>
                        <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition text-sm font-medium">
                            Update Status
                        </button>
                    </form>
                </div>
            </div>

            <div class="mt-4 text-center text-sm text-gray-500">
                <p>🔒 Only the System Owner and Cashiers can manage orders.</p>
            </div>
        </div>
    </div>
</x-app-layout>