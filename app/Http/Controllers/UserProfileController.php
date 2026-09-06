<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\CustomerProfile;
use App\Models\RiderProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserProfileController extends Controller
{
    /**
     * Helper method to check if the authenticated user is the owner.
     */
    private function isOwner()
    {
        return Auth::check() && Auth::user()->role === 'owner';
    }

    /**
     * Show the profile page (read-only unless user is the owner).
     */
    public function edit()
{
    // ✅ Check if user is authenticated
    if (!Auth::check()) {
        return redirect()->route('login');
    }

    $user = Auth::user();

    // ✅ Load the role-specific profile
    if ($user->role === 'customer') {
        $user->load('customerProfile');
    } elseif ($user->role === 'rider') {
        $user->load('riderProfile');
    }

    // ✅ Only the Owner can edit
    $canEdit = $this->isOwner();

    // ✅ Debug: Uncomment to check data
    // dd($user);

    return view('profile-edit', compact('user', 'canEdit'));
}

    /**
     * Update a user's profile (Owner only).
     */
    public function update(Request $request)
    {
        // ✅ Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // ✅ ONLY OWNER CAN EDIT
        if (!$this->isOwner()) {
            abort(403, 'Only the system owner can edit user profiles.');
        }

        // 1. Base validation rules (common to all roles)
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'contact_no' => ['required', 'string', 'max:20'],
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ];

        // 2. Role-specific validation rules
        if ($user->role === 'customer') {
            $rules['street_address'] = ['required', 'string', 'max:255'];
            $rules['barangay'] = ['nullable', 'string', 'max:255'];
            $rules['delivery_notes'] = ['nullable', 'string'];
        }

        if ($user->role === 'rider') {
            $rules['vehicle_type'] = ['required', 'string', 'max:255'];
            $rules['plate_number'] = ['nullable', 'string', 'max:50'];
            $rules['availability_status'] = ['required', 'in:available,on_delivery,offline'];
        }

        // 3. Validate the request
        $validated = $request->validate($rules);

        // 4. Update common user fields
        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->contact_no = $validated['contact_no'];

        // 5. Update password if provided
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        // 6. Update role-specific profile
        if ($user->role === 'customer') {
            $profile = $user->customerProfile ?? new CustomerProfile(['user_id' => $user->id]);
            $profile->street_address = $validated['street_address'];
            $profile->barangay = $validated['barangay'] ?? 'Poblacion';
            $profile->delivery_notes = $validated['delivery_notes'] ?? null;
            $profile->save();
        }

        if ($user->role === 'rider') {
            $profile = $user->riderProfile ?? new RiderProfile(['user_id' => $user->id]);
            $profile->vehicle_type = $validated['vehicle_type'];
            $profile->plate_number = $validated['plate_number'] ?? null;
            $profile->availability_status = $validated['availability_status'];
            $profile->save();
        }

        return redirect()->route('my-profile')->with('success', '✅ Profile has been updated successfully!');
    }
}