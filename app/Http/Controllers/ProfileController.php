<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Http\Controllers\FuncController;

class ProfileController extends Controller
{
    public function showChangePasswordForm()
    {
        return view('profile.change-password');
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', Password::min(8)],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Password updated successfully!');
    }

    public function showProfile()
    {
        return view('profile.index');
    }

    public function updateProfile(Request $request)
    {
        $validated = $request->validate([
            'signature' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);

        $user = Auth::user();

        if ($request->hasFile('signature')) {
            $path = $this->uploadImage($request->file('signature'), 'signatures');
            $user->update(['signature_path' => $path]);
        }

        return back()->with('success', 'Profile updated successfully.');
    }

      private function uploadImage($file, $folder='settings')
    { 
        return FuncController::uploadImage($file, $folder);
    }

      private function deleteImage($imagePath){
         FuncController::deleteImage($imagePath);
    }

}