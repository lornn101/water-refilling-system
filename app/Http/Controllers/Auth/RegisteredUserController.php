<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use App\Models\CustomerProfile;
use App\Models\RiderProfile;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request)
{
    // Custom conditional rules to avoid "required_if" issues with hidden fields
    $rules = [
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
        'contact_no' => ['required', 'string', 'max:20'],
        'role' => ['required', 'in:customer,rider,cashier'],
        'password' => ['required', 'confirmed', Rules\Password::defaults()],
        'barangay' => ['nullable', 'string', 'max:255'],
        'delivery_notes' => ['nullable', 'string'],
        'plate_number' => ['nullable', 'string', 'max:50'],
    ];

    // Add conditional rules based on role
    if ($request->role === 'customer') {
        $rules['street_address'] = ['required', 'string', 'max:255'];
    } else {
        $rules['street_address'] = ['nullable', 'string', 'max:255']; // Accept null/empty
    }

    if ($request->role === 'rider') {
        $rules['vehicle_type'] = ['required', 'string', 'max:255'];
    } else {
        $rules['vehicle_type'] = ['nullable', 'string', 'max:255'];
    }

    $request->validate($rules);

    // Create the user
    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'contact_no' => $request->contact_no,
        'role' => $request->role,
        'password' => Hash::make($request->password),
    ]);

    // Create profile based on role
    if ($request->role === 'customer') {
        CustomerProfile::create([
            'user_id' => $user->id,
            'street_address' => $request->street_address,
            'barangay' => $request->barangay ?? 'Poblacion',
            'delivery_notes' => $request->delivery_notes,
            'preferred_delivery_time' => null,
        ]);
    } elseif ($request->role === 'rider') {
        RiderProfile::create([
            'user_id' => $user->id,
            'vehicle_type' => $request->vehicle_type,
            'plate_number' => $request->plate_number,
            'valid_id_path' => null,
            'availability_status' => 'available',
            'current_lat_long' => null,
        ]);
    }

    event(new Registered($user));
    Auth::login($user);
    return redirect(route('dashboard', absolute: false));
    }
}
