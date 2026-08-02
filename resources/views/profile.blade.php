<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('My Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium">Personal Information</h3>
                    <div class="mt-4 space-y-2">
                        <p><strong>Name:</strong> {{ Auth::user()->name }}</p>
                        <p><strong>Email:</strong> {{ Auth::user()->email }}</p>
                        <p><strong>Contact No.:</strong> {{ Auth::user()->contact_no }}</p>
                        <p><strong>Role:</strong> {{ ucfirst(Auth::user()->role) }}</p>
                    </div>

                    @if(Auth::user()->role === 'customer' && Auth::user()->customerProfile)
                    <hr class="my-4">
                    <h3 class="text-lg font-medium">Customer Details</h3>
                    <div class="mt-4 space-y-2">
                        <p><strong>Street Address:</strong> {{ Auth::user()->customerProfile->street_address }}</p>
                        <p><strong>Barangay:</strong> {{ Auth::user()->customerProfile->barangay }}</p>
                        <p><strong>Delivery Notes:</strong> {{ Auth::user()->customerProfile->delivery_notes ?? 'None' }}</p>
                    </div>
                    @endif

                    @if(Auth::user()->role === 'rider' && Auth::user()->riderProfile)
                    <hr class="my-4">
                    <h3 class="text-lg font-medium">Rider Details</h3>
                    <div class="mt-4 space-y-2">
                        <p><strong>Vehicle Type:</strong> {{ Auth::user()->riderProfile->vehicle_type }}</p>
                        <p><strong>Plate Number:</strong> {{ Auth::user()->riderProfile->plate_number ?? 'Not specified' }}</p>
                        <p><strong>Availability Status:</strong> {{ ucfirst(Auth::user()->riderProfile->availability_status) }}</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>