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
        // ✅ Only customers can register through this form
        // The 'role' field is a hidden input set to 'customer'
        
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'contact_no' => ['required', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            // Customer-specific fields (always required for registration)
            'street_address' => ['required', 'string', 'max:255'],
            'barangay' => ['nullable', 'string', 'max:255'],
            'delivery_notes' => ['nullable', 'string'],
        ]);

        // ✅ Create the user with role = 'customer' and status = 'pending'
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'contact_no' => $request->contact_no,
            'role' => 'customer', // ✅ Always customer
            'password' => Hash::make($request->password),
            'status' => 'pending', // ✅ Requires cashier approval
        ]);

        // ✅ Always create a customer profile (since only customers register)
        CustomerProfile::create([
            'user_id' => $user->id,
            'street_address' => $request->street_address,
            'barangay' => $request->barangay ?? 'Poblacion',
            'delivery_notes' => $request->delivery_notes,
            'preferred_delivery_time' => null,
        ]);

        // ❌ DO NOT auto-login the user
        // event(new Registered($user)); // Commented out
        // Auth::login($user);           // Commented out

        // ✅ Redirect to login with pending approval message
        return redirect()->route('login')->with('status', 'Your account has been registered and is pending approval. Please wait for the cashier to approve your account.');
    }
}