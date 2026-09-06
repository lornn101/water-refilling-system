<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard') }}
            </h2>
            {{-- Role Badge --}}
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                {{ Auth::user()->role === 'owner' ? 'bg-red-100 text-red-800' : '' }}
                {{ Auth::user()->role === 'cashier' ? 'bg-purple-100 text-purple-800' : '' }}
                {{ Auth::user()->role === 'rider' ? 'bg-cyan-100 text-cyan-800' : '' }}
                {{ Auth::user()->role === 'customer' ? 'bg-blue-100 text-blue-800' : '' }}">
                {{ ucfirst(Auth::user()->role) }}
            </span>
        </div>
    </x-slot>

    {{-- Main Dashboard with Blue Theme --}}
    <div class="py-12 bg-gradient-to-br from-blue-50 via-cyan-50 to-blue-100 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- Welcome Card --}}
            <div class="bg-white overflow-hidden shadow-xl rounded-2xl border border-blue-100/50 p-6 mb-6">
                <div class="flex items-center space-x-4">
                    <div class="w-14 h-14 rounded-full flex items-center justify-center text-2xl font-bold text-white
                        {{ Auth::user()->role === 'owner' ? 'bg-red-600' : '' }}
                        {{ Auth::user()->role === 'cashier' ? 'bg-purple-600' : '' }}
                        {{ Auth::user()->role === 'rider' ? 'bg-cyan-600' : '' }}
                        {{ Auth::user()->role === 'customer' ? 'bg-blue-600' : '' }}">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-800">Hello, {{ Auth::user()->name }}! 👋</h1>
                        <p class="text-sm text-gray-500">Welcome to Poblacion Water Refilling Station</p>
                    </div>
                </div>
            </div>

            {{-- Role-Specific Content --}}
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">

                {{-- ============================ --}}
                {{-- 🟢 CUSTOMER DASHBOARD --}}
                {{-- ============================ --}}
                @if(Auth::user()->role === 'customer')
                    <div class="bg-white overflow-hidden shadow-lg rounded-2xl border border-blue-100 p-6 col-span-2">
                        <div class="flex items-center mb-4">
                            <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-800">📍 Your Delivery Details</h3>
                        </div>

                        @if(Auth::user()->customerProfile)
                            <div class="space-y-3 bg-blue-50/50 rounded-xl p-4 border border-blue-100">
                                <p class="flex items-center"><span class="font-medium text-gray-600 w-32">Address:</span> <span class="text-gray-800">{{ Auth::user()->customerProfile->street_address }}</span></p>
                                <p class="flex items-center"><span class="font-medium text-gray-600 w-32">Barangay:</span> <span class="text-gray-800">{{ Auth::user()->customerProfile->barangay }}</span></p>
                                <p class="flex items-start"><span class="font-medium text-gray-600 w-32">Delivery Notes:</span> <span class="text-gray-800">{{ Auth::user()->customerProfile->delivery_notes ?? 'None' }}</span></p>
                            </div>
                        @else
                            <p class="text-gray-500">Please complete your profile.</p>
                        @endif

                        <div class="mt-6">
                            <a href="{{ route('customer.place-order') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition shadow-md">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Place New Order
                            </a>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-lg rounded-2xl border border-blue-100 p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">📦 Order Stats</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg">
                                <span class="text-gray-600">Total Orders</span>
                                <span class="font-bold text-blue-600">{{ Auth::user()->orders()->count() }}</span>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-yellow-50 rounded-lg">
                                <span class="text-gray-600">Pending</span>
                                <span class="font-bold text-yellow-600">{{ Auth::user()->orders()->where('status', 'pending')->count() }}</span>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg">
                                <span class="text-gray-600">Delivered</span>
                                <span class="font-bold text-green-600">{{ Auth::user()->orders()->where('status', 'delivered')->count() }}</span>
                            </div>
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('customer.orders') }}" class="text-sm text-blue-600 hover:text-blue-800">View All Orders →</a>
                        </div>
                    </div>
                @endif

                {{-- ============================ --}}
                {{-- 🟢 RIDER DASHBOARD --}}
                {{-- ============================ --}}
                @if(Auth::user()->role === 'rider')
                    <div class="bg-white overflow-hidden shadow-lg rounded-2xl border border-cyan-100 p-6 col-span-2">
                        <div class="flex items-center mb-4">
                            <div class="w-10 h-10 bg-cyan-100 rounded-full flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17a2 2 0 11-4 0 2 2 0 014 0zM19 17a2 2 0 11-4 0 2 2 0 014 0zM3 7h18M3 7a2 2 0 012-2h14a2 2 0 012 2M3 7v10a2 2 0 002 2h14a2 2 0 002-2V7"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-800">🛵 Your Rider Profile</h3>
                        </div>

                        @if(Auth::user()->riderProfile)
                            <div class="space-y-3 bg-cyan-50/50 rounded-xl p-4 border border-cyan-100">
                                <p class="flex items-center"><span class="font-medium text-gray-600 w-32">Vehicle:</span> <span class="text-gray-800">{{ Auth::user()->riderProfile->vehicle_type }}</span></p>
                                <p class="flex items-center"><span class="font-medium text-gray-600 w-32">Plate Number:</span> <span class="text-gray-800">{{ Auth::user()->riderProfile->plate_number ?? 'Not specified' }}</span></p>
                                <p class="flex items-center">
                                    <span class="font-medium text-gray-600 w-32">Status:</span>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                        {{ Auth::user()->riderProfile->availability_status === 'available' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ Auth::user()->riderProfile->availability_status === 'on_delivery' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ Auth::user()->riderProfile->availability_status === 'offline' ? 'bg-gray-100 text-gray-800' : '' }}">
                                        {{ ucfirst(Auth::user()->riderProfile->availability_status) }}
                                    </span>
                                </p>
                            </div>
                        @else
                            <p class="text-gray-500">Please complete your rider profile.</p>
                        @endif

                        <div class="mt-6">
                            <a href="{{ route('rider.orders') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition shadow-md border border-blue-700">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                View Deliveries
                            </a>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-lg rounded-2xl border border-cyan-100 p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">📦 Delivery Stats</h3>
                        <div class="space-y-3">
                            <div class="flex justify-between items-center p-3 bg-cyan-50 rounded-lg">
                                <span class="text-gray-600">Assigned</span>
                                <span class="font-bold text-cyan-600">{{ Auth::user()->assignedOrders()->where('status', 'assigned')->count() }}</span>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-purple-50 rounded-lg">
                                <span class="text-gray-600">On Delivery</span>
                                <span class="font-bold text-purple-600">{{ Auth::user()->assignedOrders()->where('status', 'on_delivery')->count() }}</span>
                            </div>
                            <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg">
                                <span class="text-gray-600">Completed</span>
                                <span class="font-bold text-green-600">{{ Auth::user()->assignedOrders()->where('status', 'delivered')->count() }}</span>
                            </div>
                        </div>
                        <div class="mt-4">
                            <a href="{{ route('rider.orders') }}" class="text-sm text-cyan-600 hover:text-cyan-800">View All Deliveries →</a>
                        </div>
                    </div>
                @endif

                {{-- ============================ --}}
                {{-- 🟢 OWNER DASHBOARD --}}
                {{-- ============================ --}}
                @if(Auth::user()->role === 'owner')
                    <div class="bg-white overflow-hidden shadow-lg rounded-2xl border border-red-100 p-6 col-span-3">
                        <div class="flex items-center mb-4">
                            <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center mr-3">
                                <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-800">👑 Owner Dashboard</h3>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <div class="bg-red-50 p-4 rounded-xl text-center border border-red-100">
                                <p class="text-2xl font-bold text-red-600">{{ \App\Models\Order::count() }}</p>
                                <p class="text-sm text-gray-600">Total Orders</p>
                            </div>
                            <div class="bg-yellow-50 p-4 rounded-xl text-center border border-yellow-100">
                                <p class="text-2xl font-bold text-yellow-600">{{ \App\Models\Order::where('status', 'pending')->count() }}</p>
                                <p class="text-sm text-gray-600">Pending</p>
                            </div>
                            <div class="bg-green-50 p-4 rounded-xl text-center border border-green-100">
                                <p class="text-2xl font-bold text-green-600">{{ \App\Models\Order::where('status', 'delivered')->count() }}</p>
                                <p class="text-sm text-gray-600">Delivered</p>
                            </div>
                            <div class="bg-purple-50 p-4 rounded-xl text-center border border-purple-100">
                                <p class="text-2xl font-bold text-purple-600">{{ \App\Models\User::count() }}</p>
                                <p class="text-sm text-gray-600">Total Users</p>
                            </div>
                        </div>

                        <div class="mt-6 flex flex-wrap gap-3">
                            <a href="{{ route('cashier.users') }}" class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition shadow-md">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                                Manage Users
                            </a>
                            <a href="{{ route('cashier.create-rider') }}" class="inline-flex items-center px-4 py-2 bg-cyan-600 text-white rounded-lg hover:bg-cyan-700 transition shadow-md">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Add Rider
                            </a>
                            <a href="{{ route('owner.orders') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition shadow-md">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"></path>
                                </svg>
                                Manage Orders
                            </a>
                        </div>
                    </div>
                @endif

                {{-- ============================ --}}
{{-- 🟢 CASHIER DASHBOARD (Order Management Access) --}}
{{-- ============================ --}}
@if(Auth::user()->role === 'cashier')
    <div class="bg-white overflow-hidden shadow-lg rounded-2xl border border-purple-100 p-6 col-span-3">
        <div class="flex items-center mb-4">
            <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center mr-3">
                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-800">📊 Cashier Dashboard</h3>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-purple-50 p-4 rounded-xl text-center border border-purple-100">
                <p class="text-2xl font-bold text-purple-600">{{ \App\Models\Order::count() }}</p>
                <p class="text-sm text-gray-600">Total Orders</p>
            </div>
            <div class="bg-yellow-50 p-4 rounded-xl text-center border border-yellow-100">
                <p class="text-2xl font-bold text-yellow-600">{{ \App\Models\Order::where('status', 'pending')->count() }}</p>
                <p class="text-sm text-gray-600">Pending</p>
            </div>
            <div class="bg-green-50 p-4 rounded-xl text-center border border-green-100">
                <p class="text-2xl font-bold text-green-600">{{ \App\Models\Order::where('status', 'delivered')->count() }}</p>
                <p class="text-sm text-gray-600">Delivered</p>
            </div>
            <div class="bg-gray-50 p-4 rounded-xl text-center border border-gray-100">
                <p class="text-2xl font-bold text-gray-600">{{ \App\Models\User::count() }}</p>
                <p class="text-sm text-gray-600">Total Users</p>
            </div>
        </div>

        <div class="mt-6 flex flex-wrap gap-3">
            <a href="{{ route('owner.orders') }}" class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition shadow-md">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"></path>
                </svg>
                Manage Orders
            </a>
        </div>
    </div>
@endif

            </div>
        </div>
    </div>
</x-app-layout>