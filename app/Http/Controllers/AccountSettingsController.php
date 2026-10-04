<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class AccountSettingsController extends Controller
{
    /**
     * Show the account settings page
     */
    public function index()
    {
        $user = Auth::user();
        return view('account-settings.index', compact('user'));
    }

    /**
     * Update user profile information
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'nrp' => ['required', 'string', 'max:255', Rule::unique('users', 'nrp')->ignore($user->id)],
            'jabatan' => ['nullable', 'string', 'max:255'],
        ]);

        $user->update($validated);

        return redirect()->route('account-settings.index')->with('success', 'Data profil berhasil diperbarui!');
    }

    /**
     * Update user password
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'current_password' => ['required', function ($attribute, $value, $fail) use ($user) {
                if (!Hash::check($value, $user->password)) {
                    $fail('Password saat ini tidak sesuai.');
                }
            }],
            'new_password' => ['required', 'string', 'confirmed'],
            'new_password_confirmation' => ['required'],
        ]);

        $user->update([
            'password' => Hash::make($validated['new_password']),
        ]);

        return redirect()->route('account-settings.index')->with('success', 'Password berhasil diperbarui!');
    }

    /**
     * Update user avatar/photo
     */
    public function updateAvatar(Request $request)
    {
        $user = Auth::user();
        
        $validated = $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
        ]);

        // Delete old avatar if exists
        if ($user->foto) {
            $oldPath = storage_path('app/public/' . $user->foto);
            if (file_exists($oldPath)) {
                unlink($oldPath);
            }
        }

        // Store new avatar
        $path = $request->file('avatar')->store('avatars', 'public');
        
        $user->update([
            'foto' => $path,
        ]);

        return redirect()->route('account-settings.index')->with('success', 'Foto profil berhasil diperbarui!');
    }
}
