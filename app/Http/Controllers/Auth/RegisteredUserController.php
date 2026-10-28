<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use App\Models\CustomerProfile;

class RegisteredUserController extends Controller
{
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * @throws ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'contact_no' => ['required', 'string', 'max:20'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'street_address' => ['required', 'string', 'max:255'],
            'barangay' => ['nullable', 'string', 'max:255'],
            'delivery_notes' => ['nullable', 'string'],
        ]);

        $user = User::create([
            'first_name' => $request->first_name,
            'middle_name' => $request->middle_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'contact_no' => $request->contact_no,
            'role' => 'customer',
            'password' => Hash::make($request->password),
            'status' => 'pending',
        ]);

        CustomerProfile::create([
            'user_id' => $user->id,
            'street_address' => $request->street_address,
            'barangay' => $request->barangay ?? 'Poblacion',
            'delivery_notes' => $request->delivery_notes,
            'preferred_delivery_time' => null,
        ]);

        return redirect()->route('login')->with('status', 'Your account has been registered and is pending approval. Please wait for the cashier to approve your account.');
    }
}