<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('admin.edit-profile', compact('user'));
    }

    public function update(Request $request)
    {
        $dataValidated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . Auth::id(),
            'password' => 'nullable|string|min:8|confirmed',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $user = \App\Models\User::find(Auth::id());
        foreach ($dataValidated as $key => $value) {
            if ($key !== 'password' || !empty($value)) {
                $user->$key = $value;
            }
        }

        if (!empty($dataValidated['password'])) {
            $user->password = Hash::make($dataValidated['password']);
        }
        $user->save();

        return redirect()->route('admin.profile')->with('success', 'Profile updated successfully.');
    }
}
