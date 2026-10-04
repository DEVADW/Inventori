<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
class ProfileController extends Controller
{
    //

    public function update(Request $request, User $user) 
{
    $validated = $request->validate([
        'nama'     => ['required', 'string', 'max:255'],
        'nrp'      => ['required', 'string', 'max:255', 'unique:users,nrp,' . $user->id], 
        'password' => ['nullable', 'string', 'max:255'], // dibuat nullable (boleh kosong)
    ]);

    if ($request->filled('password')) {
        $validated['password'] = Hash::make($request->password);
    } else {
        unset($validated['password']);
    }

    

    $user->update($validated);

    return redirect()->back()->with('success', 'Profile berhasil diperbaharui!');
}
}
