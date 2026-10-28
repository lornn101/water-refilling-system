<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Order Details') }} #{{ $order->id }}
            </h2>
            <div class="flex gap-2">
                <a href="{{ route('orders.track', $order->id) }}" class="px-3 py-1 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium">
                    📍 Track
                </a>
                <a href="{{ route('orders.receipt', $order->id) }}" target="_blank" class="px-3 py-1 bg-gray-700 text-white rounded-lg hover:bg-gray-800 transition text-sm font-medium">
                    🧾 Print Receipt
                </a>
                <a href="{{ route('owner.orders') }}" class="text-blue-600 hover:text-blue-800 text-sm px-3 py-1">← Back</a>
            </div>
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
                            <p class="text-xs text-gray-500">Type</p>
                            <p class="font-semibold text-gray-800">{{ $order->is_walk_in ? '🏪 Walk-in' : '🌐 Online' }}</p>
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
                            <p class="font-medium text-gray-800">{{ $order->getCustomerName() }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Contact</p>
                            <p class="font-medium text-gray-800">{{ $order->getCustomerContact() }}</p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-xs text-gray-500">Address</p>
                            <p class="font-medium text-gray-800">{{ $order->delivery_address }}</p>
                        </div>
                        @if($order->delivery_notes)
                            <div class="col-span-2">
                                <p class="text-xs text-gray-500">Notes</p>
                                <p class="font-medium text-gray-800">{{ $order->delivery_notes }}</p>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Billing Summary --}}
                <div class="p-6 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-800 mb-3">💰 Billing Summary</h3>
                    <div class="bg-gray-50 rounded-xl p-4">
                        <div class="flex justify-between text-sm mb-2">
                            <span class="text-gray-600">{{ $order->quantity }} gallon(s) × ₱{{ number_format($order->unit_price, 2) }}</span>
                            <span class="font-medium">₱{{ number_format($order->quantity * $order->unit_price, 2) }}</span>
                        </div>
                        @if($order->delivery_fee > 0)
                            <div class="flex justify-between text-sm mb-2">
                                <span class="text-gray-600">Delivery Fee</span>
                                <span class="font-medium">₱{{ number_format($order->delivery_fee, 2) }}</span>
                            </div>
                        @endif
                        <div class="border-t border-gray-300 mt-2 pt-2 flex justify-between">
                            <span class="font-bold text-gray-800">TOTAL</span>
                            <span class="font-bold text-blue-700 text-lg">₱{{ number_format($order->total_amount, 2) }}</span>
                        </div>
                    </div>
                </div>

                {{-- Payment Section --}}
                <div class="p-6 border-b border-gray-200">
                    <div class="flex justify-between items-center mb-3">
                        <h3 class="font-semibold text-gray-800">💳 Payment</h3>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $order->getPaymentStatusBadgeColor() }}">
                            {{ $order->getPaymentStatusLabel() }}
                        </span>
                    </div>

                    @if($order->isPaid())
                        <div class="bg-green-50 rounded-xl p-4 border border-green-200">
                            <div class="grid grid-cols-2 gap-3 text-sm">
                                <div>
                                    <p class="text-xs text-gray-500">Method</p>
                                    <p class="font-medium">{{ $order->getPaymentMethodLabel() }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-gray-500">Amount</p>
                                    <p class="font-medium">₱{{ number_format($order->total_amount, 2) }}</p>
                                </div>
                                @if($order->payment_reference)
                                    <div>
                                        <p class="text-xs text-gray-500">Reference</p>
                                        <p class="font-medium">{{ $order->payment_reference }}</p>
                                    </div>
                                @endif
                                <div>
                                    <p class="text-xs text-gray-500">Paid At</p>
                                    <p class="font-medium">{{ $order->paid_at?->format('M d, Y h:i A') }}</p>
                                </div>
                                @if($order->paidBy)
                                    <div>
                                        <p class="text-xs text-gray-500">Recorded By</p>
                                        <p class="font-medium">{{ $order->paidBy->name }}</p>
                                    </div>
                                @endif
                            </div>

                            @if(Auth::user()->role === 'owner')
                                <form method="POST" action="{{ route('orders.unmark-paid', $order->id) }}" class="mt-4" onsubmit="return confirm('Revert payment? Order will be marked as UNPAID.')">
                                    @csrf
                                    <button type="submit" class="px-3 py-1 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 text-xs font-medium">
                                        ↩️ Revert Payment
                                    </button>
                                </form>
                            @endif
                        </div>
                    @else
                        <div class="bg-orange-50 rounded-xl p-4 border border-orange-200">
                            <p class="text-sm text-orange-700 mb-3">⚠️ This order has not been paid yet.</p>
                            <form method="POST" action="{{ route('orders.mark-paid', $order->id) }}" class="grid grid-cols-1 md:grid-cols-3 gap-3">
                                @csrf
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Payment Method *</label>
                                    <select name="payment_method" class="w-full border-gray-300 rounded-lg focus:border-blue-400 focus:ring-blue-400 text-sm" required>
                                        <option value="cash">💵 Cash</option>
                                        <option value="gcash">📱 GCash</option>
                                        <option value="bank_transfer">🏦 Bank Transfer</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-600 mb-1">Reference # (Optional)</label>
                                    <input type="text" name="payment_reference" placeholder="e.g., GCash ref" class="w-full border-gray-300 rounded-lg focus:border-blue-400 focus:ring-blue-400 text-sm">
                                </div>
                                <div class="flex items-end">
                                    <button type="submit" class="w-full px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition shadow-md text-sm font-medium">
                                        💰 Mark as Paid
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>

                {{-- Rider Assignment --}}
                <div class="p-6 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-800 mb-3">🛵 Rider Assignment</h3>
                    <div class="flex flex-wrap items-center gap-4">
                        <div>
                            <p class="text-xs text-gray-500">Current Rider</p>
                            <p class="font-medium text-gray-800">{{ $order->rider->name ?? 'Not Assigned' }}</p>
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
                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium">Assign</button>
                            </form>
                        @else
                            <span class="text-sm text-gray-500">Rider cannot be changed for this status.</span>
                        @endif
                    </div>
                </div>

                {{-- Update Status --}}
                <div class="p-6 border-b border-gray-200">
                    <h3 class="font-semibold text-gray-800 mb-3">📊 Update Status</h3>
                    <form method="POST" action="{{ route('owner.update-status', $order->id) }}" class="space-y-3">
                        @csrf
                        <div class="flex flex-wrap items-end gap-3">
                            <div class="flex-1 min-w-[200px]">
                                <label class="block text-xs font-medium text-gray-500 mb-1">New Status</label>
                                <select name="status" class="w-full border-gray-300 rounded-lg focus:border-blue-400 focus:ring-blue-400 text-sm">
                                    <option value="pending" {{ $order->status === 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                                    <option value="assigned" {{ $order->status === 'assigned' ? 'selected' : '' }}>📋 Assigned</option>
                                    <option value="on_delivery" {{ $order->status === 'on_delivery' ? 'selected' : '' }}>🚚 On Delivery</option>
                                    <option value="delivered" {{ $order->status === 'delivered' ? 'selected' : '' }}>✅ Delivered</option>
                                    <option value="completed" {{ $order->status === 'completed' ? 'selected' : '' }}>🏪 Completed (Walk-in)</option>
                                    <option value="cancelled" {{ $order->status === 'cancelled' ? 'selected' : '' }}>❌ Cancelled</option>
                                </select>
                            </div>
                            <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition text-sm font-medium">
                                Update Status
                            </button>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-500 mb-1">Notes (Optional — visible to customer)</label>
                            <textarea name="notes" rows="2" placeholder="e.g., Customer not home, will retry tomorrow" class="w-full border-gray-300 rounded-lg focus:border-blue-400 focus:ring-blue-400 text-sm"></textarea>
                        </div>
                    </form>
                </div>

                {{-- A7: Activity Timeline --}}
                <div class="p-6 bg-gray-50">
                    <h3 class="font-semibold text-gray-800 mb-3">📋 Activity Timeline</h3>
                    @if($order->statusLogs->isEmpty())
                        <p class="text-sm text-gray-500">No activity recorded yet.</p>
                    @else
                        <div class="space-y-3">
                            @foreach($order->statusLogs as $log)
                                <div class="flex items-start bg-white rounded-lg p-3 border border-gray-200">
                                    <span class="text-xl mr-3">{{ $log->getStatusIcon() }}</span>
                                    <div class="flex-1">
                                        <div class="flex justify-between items-start">
                                            <div>
                                                <p class="font-semibold text-gray-800 text-sm">{{ $log->getStatusLabel() }}</p>
                                                <p class="text-xs text-gray-500 mt-0.5">{{ $log->getChangedByLabel() }}</p>
                                            </div>
                                            <div class="text-right">
                                                <p class="text-xs text-gray-500">{{ $log->created_at->format('M d, Y') }}</p>
                                                <p class="text-xs text-gray-400">{{ $log->created_at->format('h:i A') }}</p>
                                            </div>
                                        </div>
                                        @if($log->notes)
                                            <p class="text-xs text-gray-600 italic mt-1">💬 {{ $log->notes }}</p>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>