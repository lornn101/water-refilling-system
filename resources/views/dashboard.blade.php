<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Dashboard') }}
            </h2>
            {{-- Role Badge --}}
            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                {{ ucfirst(Auth::user()->role) }}
            </span>
        </div>
    </x-slot>

    <div class="py-12 bg-gradient-to-br from-blue-50/50 via-cyan-50/50 to-blue-100/50 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Welcome Card --}}
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-xl border border-blue-100/50 p-6 mb-6">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center text-blue-600 text-xl font-bold">
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
                
                {{-- 🟢 CUSTOMER DASHBOARD --}}
                @if(Auth::user()->role === 'customer')
                    <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-blue-100 p-6 col-span-2">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">📍 Your Delivery Details</h3>
                        @if(Auth::user()->customerProfile)
                            <div class="space-y-3">
                                <p><span class="font-medium text-gray-600">Address:</span> {{ Auth::user()->customerProfile->street_address }}</p>
                                <p><span class="font-medium text-gray-600">Barangay:</span> {{ Auth::user()->customerProfile->barangay }}</p>
                                <p><span class="font-medium text-gray-600">Delivery Notes:</span> {{ Auth::user()->customerProfile->delivery_notes ?? 'None' }}</p>
                            </div>
                        @else
                            <p class="text-gray-500">Please complete your profile.</p>
                        @endif
                        <div class="mt-6">
                            <a href="#" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition shadow-md">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Place New Order (Coming Soon)
                            </a>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-blue-100 p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">📦 Quick Stats</h3>
                        <div class="space-y-2 text-gray-600">
                            <p>Total Orders: <span class="font-bold text-blue-600">0</span></p>
                            <p>Pending: <span class="font-bold text-yellow-600">0</span></p>
                            <p>Delivered: <span class="font-bold text-green-600">0</span></p>
                        </div>
                    </div>
                @endif

                {{-- 🟢 RIDER DASHBOARD --}}
                @if(Auth::user()->role === 'rider')
                    <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-cyan-100 p-6 col-span-2">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">🛵 Your Rider Profile</h3>
                        @if(Auth::user()->riderProfile)
                            <div class="space-y-3">
                                <p><span class="font-medium text-gray-600">Vehicle:</span> {{ Auth::user()->riderProfile->vehicle_type }}</p>
                                <p><span class="font-medium text-gray-600">Plate Number:</span> {{ Auth::user()->riderProfile->plate_number ?? 'Not specified' }}</p>
                                <p><span class="font-medium text-gray-600">Status:</span> 
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        {{ ucfirst(Auth::user()->riderProfile->availability_status) }}
                                    </span>
                                </p>
                            </div>
                        @else
                            <p class="text-gray-500">Please complete your rider profile.</p>
                        @endif
                        <div class="mt-6">
                            <a href="#" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition shadow-md border border-blue-700">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                        View Deliveries (Coming Soon)
                        </a>
                        </div>
                    </div>

                    <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-cyan-100 p-6">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">📦 Delivery Stats</h3>
                        <div class="space-y-2 text-gray-600">
                            <p>Assigned: <span class="font-bold text-cyan-600">0</span></p>
                            <p>Completed: <span class="font-bold text-green-600">0</span></p>
                        </div>
                    </div>
                @endif

                {{-- 🟢 CASHIER / OWNER DASHBOARD --}}
                @if(Auth::user()->role === 'cashier')
                    <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-purple-100 p-6 col-span-3">
                        <h3 class="text-lg font-semibold text-gray-800 mb-4">📊 Management Overview</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                            <div class="bg-blue-50 p-4 rounded-lg text-center">
                                <p class="text-2xl font-bold text-blue-600">0</p>
                                <p class="text-sm text-gray-600">Total Orders</p>
                            </div>
                            <div class="bg-yellow-50 p-4 rounded-lg text-center">
                                <p class="text-2xl font-bold text-yellow-600">0</p>
                                <p class="text-sm text-gray-600">Pending</p>
                            </div>
                            <div class="bg-green-50 p-4 rounded-lg text-center">
                                <p class="text-2xl font-bold text-green-600">0</p>
                                <p class="text-sm text-gray-600">Delivered</p>
                            </div>
                        </div>
                        <div class="mt-6">
                            <a href="#" class="inline-flex items-center px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition shadow-md">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"></path>
                                </svg>
                                Manage Orders (Coming Soon)
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>