<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Traits\StoreBase64Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    use StoreBase64Image;

    public function index()
    {
        $user = Auth::user();
        return view('kasir.edit-profile', compact('user'));
    }

    public function update(Request $request)
    {
        $dataValidated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . Auth::id(),
            'password' => 'nullable|string|min:8|confirmed',
            'profile_img_base64' => 'nullable|string',
        ]);

        $dataValidated['profile_image'] = $this->storeBase64Image('images/users', $request->profile_img_base64);

        $user = \App\Models\User::find(Auth::id());
        
        foreach ($dataValidated as $key => $value) {
            if ($key === 'profile_img_base64') {
                continue; // skip profile_img_base64
            }
            if ($key !== 'password' || !empty($value)) {
                $user->$key = $value;
            }
        }

        if (!empty($dataValidated['password'])) {
            $user->password = Hash::make($dataValidated['password']);
        }

        $user->save();

        return redirect()->route('kasir.profile')->with('success', 'Profile updated successfully.');
    }
}
