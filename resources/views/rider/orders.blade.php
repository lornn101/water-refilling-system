<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('My Deliveries') }}
            </h2>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-cyan-100 text-cyan-800">
                🛵 Rider
            </span>
        </div>
    </x-slot>

    <div class="py-12 bg-gradient-to-br from-blue-50 via-cyan-50 to-blue-100 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg border border-green-200">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg border border-red-200">{{ session('error') }}</div>
            @endif

            {{-- Rider Stats --}}
            <div class="grid grid-cols-3 gap-4 mb-6">
                <div class="bg-white rounded-xl shadow p-4 text-center border-l-4 border-blue-500">
                    <p class="text-2xl font-bold text-blue-600">{{ $stats['assigned'] }}</p>
                    <p class="text-xs text-gray-500">Assigned</p>
                </div>
                <div class="bg-white rounded-xl shadow p-4 text-center border-l-4 border-purple-500">
                    <p class="text-2xl font-bold text-purple-600">{{ $stats['on_delivery'] }}</p>
                    <p class="text-xs text-gray-500">On Delivery</p>
                </div>
                <div class="bg-white rounded-xl shadow p-4 text-center border-l-4 border-green-500">
                    <p class="text-2xl font-bold text-green-600">{{ $stats['completed'] }}</p>
                    <p class="text-xs text-gray-500">Completed</p>
                </div>
            </div>

            {{-- Active Orders (Assigned & On Delivery) --}}
            <div class="bg-white rounded-xl shadow-lg overflow-hidden mb-6">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="font-semibold text-gray-800">🔄 Active Deliveries</h3>
                </div>
                @if($assignedOrders->isEmpty())
                    <div class="p-6 text-center text-gray-500">No active deliveries assigned to you.</div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Address</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($assignedOrders as $order)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">#{{ $order->id }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-800">{{ $order->customer->name ?? 'N/A' }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-500 max-w-xs truncate">{{ $order->delivery_address }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $order->quantity }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $order->getStatusBadgeColor() }}">
                                                {{ $order->getStatusIcon() }} {{ ucfirst($order->status) }}
                                            </span>
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm">
                                            @if($order->status === 'assigned')
                                                <form method="POST" action="{{ route('rider.mark-on-delivery', $order->id) }}" class="inline">
                                                    @csrf
                                                    <button type="submit" class="px-3 py-1 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition text-xs font-medium">🚚 Start Delivery</button>
                                                </form>
                                            @endif
                                            @if($order->status === 'on_delivery' || $order->status === 'assigned')
                                                <form method="POST" action="{{ route('rider.mark-delivered', $order->id) }}" class="inline">
                                                    @csrf
                                                    <button type="submit" class="px-3 py-1 bg-green-600 text-white rounded-lg hover:bg-green-700 transition text-xs font-medium">✅ Delivered</button>
                                                </form>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            {{-- Completed Orders --}}
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                    <h3 class="font-semibold text-gray-800">✅ Completed Deliveries</h3>
                </div>
                @if($completedOrders->isEmpty())
                    <div class="p-6 text-center text-gray-500">No completed deliveries yet.</div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Address</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Delivered At</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($completedOrders as $order)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">#{{ $order->id }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-800">{{ $order->customer->name ?? 'N/A' }}</td>
                                        <td class="px-4 py-3 text-sm text-gray-500 max-w-xs truncate">{{ $order->delivery_address }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $order->delivered_at ? $order->delivered_at->format('M d, Y h:i A') : 'N/A' }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="px-4 py-3 border-t border-gray-200">
                        {{ $completedOrders->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>