<?php

namespace App\Http\Controllers;

use App\Models\CustomerProfile;
use App\Models\RiderProfile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserProfileController extends Controller
{
    private function isOwner()
    {
        return Auth::check() && Auth::user()->role === 'owner';
    }

    public function edit()
    {
        $user = Auth::user();

        if ($user->role === 'customer') {
            $user->load('customerProfile');
        } elseif ($user->role === 'rider') {
            $user->load('riderProfile');
        }

        $canEdit = $this->isOwner();

        return view('profile-edit', compact('user', 'canEdit'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        if (!$this->isOwner()) {
            abort(403, 'Only the system owner can edit user profiles.');
        }

        $rules = [
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'contact_no' => ['required', 'string', 'max:20'],
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ];

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

        $validated = $request->validate($rules);

        $user->first_name = $validated['first_name'];
        $user->middle_name = $validated['middle_name'] ?? null;
        $user->last_name = $validated['last_name'];
        $user->email = $validated['email'];
        $user->contact_no = $validated['contact_no'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

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