<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('My Orders') }}
            </h2>
            <a href="{{ route('customer.place-order') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition shadow-md text-sm font-medium">
                ➕ Place New Order
            </a>
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

            <div class="bg-white overflow-hidden shadow-xl rounded-2xl border border-blue-100/50 p-6">
                @if($orders->isEmpty())
                    <div class="text-center py-12">
                        <div class="text-6xl mb-4">📦</div>
                        <h3 class="text-xl font-semibold text-gray-600">No orders yet</h3>
                        <p class="text-gray-500 mt-2">Place your first order today!</p>
                        <a href="{{ route('customer.place-order') }}" class="inline-block mt-4 px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition shadow-md">
                            Place Order →
                        </a>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order #</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Address</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($orders as $order)
                                    <tr class="hover:bg-gray-50 transition">
                                        <td class="px-4 py-3 whitespace-nowrap text-sm font-medium text-gray-900">#{{ $order->id }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $order->quantity }} gallon(s)</td>
                                        <td class="px-4 py-3 text-sm text-gray-500 max-w-xs truncate">{{ $order->delivery_address }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap">
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $order->getStatusBadgeColor() }}">
                                                {{ $order->getStatusIcon() }} {{ $order->getStatusLabel() }}
                                            </span>
                                            @if($order->wasModified())
                                                <span class="ml-1 inline-flex items-center px-1.5 py-0.5 rounded text-xs bg-gray-100 text-gray-600" title="Modified after creation">✏️</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">{{ $order->created_at->format('M d, Y h:i A') }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-sm">
                                            @if($order->canBeModified())
                                                <div class="flex gap-2">
                                                    <a href="{{ route('customer.edit-order', $order->id) }}" class="px-3 py-1 bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition text-xs font-medium">✏️ Edit</a>
                                                    <form method="POST" action="{{ route('customer.cancel-order', $order->id) }}" class="inline" onsubmit="return confirm('Cancel this order?')">
                                                        @csrf
                                                        <button type="submit" class="px-3 py-1 bg-red-500 text-white rounded-lg hover:bg-red-600 transition text-xs font-medium">❌ Cancel</button>
                                                    </form>
                                                </div>
                                            @else
                                                <span class="text-xs text-gray-400">—</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-4">
                        {{ $orders->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>