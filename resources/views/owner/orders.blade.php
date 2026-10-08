<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Order Management') }}
            </h2>
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                {{ Auth::user()->role === 'owner' ? 'bg-red-100 text-red-800' : 'bg-purple-100 text-purple-800' }}">
                {{ Auth::user()->role === 'owner' ? '👑 Owner' : '💼 Cashier' }}
            </span>
        </div>
    </x-slot>

    <div class="py-12 bg-gradient-to-br from-blue-50 via-cyan-50 to-blue-100 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-700 rounded-lg border border-green-200 flex justify-between items-center">
                    <span>{{ session('success') }}</span>
                    <button onclick="this.parentElement.remove()" class="text-green-700 hover:text-green-900">×</button>
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 p-4 bg-red-100 text-red-700 rounded-lg border border-red-200 flex justify-between items-center">
                    <span>{{ session('error') }}</span>
                    <button onclick="this.parentElement.remove()" class="text-red-700 hover:text-red-900">×</button>
                </div>
            @endif

            {{-- Header with New Walk-in Button --}}
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-800">All Orders</h1>
                    <p class="text-sm text-gray-500">Manage online and walk-in orders</p>
                </div>
                <a href="{{ route('owner.walk-in-create') }}" class="px-4 py-2 bg-cyan-600 text-white rounded-lg hover:bg-cyan-700 transition shadow-md text-sm font-medium inline-flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    New Walk-in Order
                </a>
            </div>

            {{-- Stats Dashboard (6 cards) --}}
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-6">
                <div class="bg-white rounded-xl shadow p-4 text-center border-l-4 border-gray-500">
                    <p class="text-2xl font-bold text-gray-600">{{ $totalOrders }}</p>
                    <p class="text-xs text-gray-500 mt-1">Total</p>
                </div>
                <div class="bg-white rounded-xl shadow p-4 text-center border-l-4 border-yellow-500">
                    <p class="text-2xl font-bold text-yellow-600">{{ $pendingCount }}</p>
                    <p class="text-xs text-gray-500 mt-1">⏳ Pending</p>
                </div>
                <div class="bg-white rounded-xl shadow p-4 text-center border-l-4 border-blue-500">
                    <p class="text-2xl font-bold text-blue-600">{{ $inProgressCount }}</p>
                    <p class="text-xs text-gray-500 mt-1">🚚 In Progress</p>
                </div>
                <div class="bg-white rounded-xl shadow p-4 text-center border-l-4 border-green-500">
                    <p class="text-2xl font-bold text-green-600">{{ $deliveredCount }}</p>
                    <p class="text-xs text-gray-500 mt-1">✅ Delivered</p>
                </div>
                <div class="bg-white rounded-xl shadow p-4 text-center border-l-4 border-emerald-500">
                    <p class="text-2xl font-bold text-emerald-600">{{ $completedCount }}</p>
                    <p class="text-xs text-gray-500 mt-1">🏪 Completed</p>
                </div>
                <div class="bg-white rounded-xl shadow p-4 text-center border-l-4 border-red-500">
                    <p class="text-2xl font-bold text-red-600">{{ $cancelledCount }}</p>
                    <p class="text-xs text-gray-500 mt-1">❌ Cancelled</p>
                </div>
            </div>

            {{-- Filters --}}
            <div class="bg-white rounded-xl shadow-lg p-4 mb-6">
                <form method="GET" action="{{ route('owner.orders') }}" class="flex flex-wrap gap-3 items-end">
                    <div class="flex-1 min-w-[150px]">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Search</label>
                        <input type="text" name="search" value="{{ request('search') }}" placeholder="Order #, customer name..." class="w-full border-gray-200 rounded-lg focus:border-blue-400 focus:ring-blue-400 text-sm">
                    </div>
                    <div class="w-[140px]">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Status</label>
                        <select name="status" class="w-full border-gray-200 rounded-lg focus:border-blue-400 focus:ring-blue-400 text-sm">
                            <option value="">All</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                            <option value="assigned" {{ request('status') == 'assigned' ? 'selected' : '' }}>Assigned</option>
                            <option value="on_delivery" {{ request('status') == 'on_delivery' ? 'selected' : '' }}>On Delivery</option>
                            <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                            <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                        </select>
                    </div>
                    <div class="w-[140px]">
                        <label class="block text-xs font-medium text-gray-500 mb-1">Type</label>
                        <select name="type" class="w-full border-gray-200 rounded-lg focus:border-blue-400 focus:ring-blue-400 text-sm">
                            <option value="">All Types</option>
                            <option value="online" {{ request('type') == 'online' ? 'selected' : '' }}>Online</option>
                            <option value="walk_in" {{ request('type') == 'walk_in' ? 'selected' : '' }}>Walk-in</option>
                        </select>
                    </div>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition text-sm font-medium">🔍 Filter</button>
                    <a href="{{ route('owner.orders') }}" class="px-4 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition text-sm font-medium">↺ Reset</a>
                </form>
            </div>

            {{-- Orders Table --}}
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">#</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Qty</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Address</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rider</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($orders as $order)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">
                                        #{{ $order->id }}
                                        @if($order->is_walk_in)
                                            <span class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded text-xs bg-cyan-100 text-cyan-700">W</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-800">
                                        {{ $order->getCustomerName() }}
                                        @if($order->is_walk_in)
                                            <p class="text-xs text-gray-400">Walk-in customer</p>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $order->quantity }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-500 max-w-xs truncate">{{ $order->delivery_address }}</td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                                        {{ $order->rider->name ?? '—' }}
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $order->getStatusBadgeColor() }}">
                                            {{ $order->getStatusIcon() }} {{ $order->getStatusLabel() }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 whitespace-nowrap text-sm">
                                        <a href="{{ route('owner.order-detail', $order->id) }}" class="text-blue-600 hover:text-blue-800 font-medium">View</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-4 py-8 text-center text-gray-500">No orders found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="px-4 py-3 border-t border-gray-200">
                    {{ $orders->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>