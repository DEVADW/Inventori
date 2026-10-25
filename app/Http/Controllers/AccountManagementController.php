<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AccountManagementController extends Controller
{
    /**
     * Daftar akun yang terdaftar.
     */
    public function index(Request $request)
    {
        $query = User::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', '%' . $search . '%')
                    ->orWhere('nrp', 'like', '%' . $search . '%')
                    ->orWhere('jabatan', 'like', '%' . $search . '%');
            });
        }

        $users = $query->latest()->paginate(15);

        return view('manajemen-akun.index', compact('users'));
    }

    /**
     * Form pengaturan akses akun.
     */
    public function edit(User $user)
    {
        $modules = User::inspectionModules();
        $selected = $user->allowedInspectionModules();

        return view('manajemen-akun.edit', compact('user', 'modules', 'selected'));
    }

    /**
     * Simpan pengaturan akses akun.
     */
    public function update(Request $request, User $user)
    {
        $izinGaleriFoto = $request->boolean('izin_galeri_foto');

        if ($user->role === 'admin') {
            $user->update([
                'akses_inspeksi' => array_keys(User::inspectionModules()),
                'izin_galeri_foto' => $izinGaleriFoto,
            ]);

            return redirect()->route('manajemen-akun.index')
                ->with('success', 'Akun admin memiliki akses ke semua modul inspeksi.');
        }

        $validated = $request->validate([
            'modules' => ['nullable', 'array'],
            'modules.*' => ['string', Rule::in(array_keys(User::inspectionModules()))],
        ]);

        $user->update([
            'akses_inspeksi' => array_values($validated['modules'] ?? []),
            'izin_galeri_foto' => $izinGaleriFoto,
        ]);

        return redirect()->route('manajemen-akun.index')
            ->with('success', 'Hak akses akun berhasil diperbarui!');
    }
}
