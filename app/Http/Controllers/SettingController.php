<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingController extends Controller
{
    /**
     * Show settings page (owner only).
     */
    public function index()
    {
        if (!Auth::check() || Auth::user()->role !== 'owner') {
            abort(403, 'Only the system owner can manage settings.');
        }

        $settings = [
            'price_per_gallon' => Setting::getDecimal('price_per_gallon', 25),
            'delivery_fee' => Setting::getDecimal('delivery_fee', 10),
            'business_name' => Setting::get('business_name', 'Poblacion Water Refilling Station'),
            'business_address' => Setting::get('business_address', 'Poblacion, Bilar, Bohol'),
            'business_contact' => Setting::get('business_contact', '0912-345-6789'),
        ];

        return view('owner.settings', compact('settings'));
    }

    /**
     * Update settings (owner only).
     */
    public function update(Request $request)
    {
        if (!Auth::check() || Auth::user()->role !== 'owner') {
            abort(403, 'Only the system owner can manage settings.');
        }

        $request->validate([
            'price_per_gallon' => ['required', 'numeric', 'min:0', 'max:9999'],
            'delivery_fee' => ['required', 'numeric', 'min:0', 'max:9999'],
            'business_name' => ['required', 'string', 'max:255'],
            'business_address' => ['required', 'string', 'max:255'],
            'business_contact' => ['required', 'string', 'max:50'],
        ]);

        Setting::set('price_per_gallon', $request->price_per_gallon, 'Price per gallon in PHP');
        Setting::set('delivery_fee', $request->delivery_fee, 'Standard delivery fee in PHP');
        Setting::set('business_name', $request->business_name, 'Business name for receipts');
        Setting::set('business_address', $request->business_address, 'Business address for receipts');
        Setting::set('business_contact', $request->business_contact, 'Business contact for receipts');

        return redirect()->route('owner.settings')->with('success', '✅ Settings saved successfully!');
    }
}