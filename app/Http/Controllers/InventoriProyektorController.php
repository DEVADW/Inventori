<?php

namespace App\Http\Controllers;

use App\Models\InventoriProyektorModel;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class InventoriProyektorController extends Controller
{
    public function index(Request $request)
    {
        $query = InventoriProyektorModel::query();

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('no_aset', 'like', '%'.$search.'%')
                    ->orWhere('merek', 'like', '%'.$search.'%')
                    ->orWhere('tipe', 'like', '%'.$search.'%')
                    ->orWhere('serial_number', 'like', '%'.$search.'%')
                    ->orWhere('department', 'like', '%'.$search.'%')
                    ->orWhere('lokasi', 'like', '%'.$search.'%');
            });
        }

        $dataInventoriProyektor = $query->latest()->paginate(10)->withQueryString();

        return view('aset.inventoriproyektor.index', compact('dataInventoriProyektor'));
    }

    public function create()
    {
        return view('aset.inventoriproyektor.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'no_aset' => ['required', 'string', 'max:255', 'unique:inventori_proyektor,no_aset'],
            'merek' => ['required', 'string', 'max:255'],
            'tipe' => ['required', 'string', 'max:255'],
            'serial_number' => ['required', 'string', 'max:255'],
            'department' => ['required', 'string', 'max:255'],
            'lokasi' => ['required', 'string', 'max:255'],
        ]);

        InventoriProyektorModel::create($validated);

        return redirect()->route('aset.inventoriproyektor.index')->with('success', 'Data Inventori Proyektor berhasil ditambahkan.');
    }

    public function edit(InventoriProyektorModel $inventoriproyektor)
    {
        return view('aset.inventoriproyektor.edit', compact('inventoriproyektor'));
    }

    public function update(Request $request, InventoriProyektorModel $inventoriproyektor)
    {
        $validated = $request->validate([
            'no_aset' => ['required', 'string', 'max:255', Rule::unique('inventori_proyektor', 'no_aset')->ignore($inventoriproyektor->id)],
            'merek' => ['required', 'string', 'max:255'],
            'tipe' => ['required', 'string', 'max:255'],
            'serial_number' => ['required', 'string', 'max:255'],
            'department' => ['required', 'string', 'max:255'],
            'lokasi' => ['required', 'string', 'max:255'],
        ]);

        $inventoriproyektor->update($validated);

        return redirect()->route('aset.inventoriproyektor.index')->with('success', 'Data Inventori Proyektor berhasil diubah.');
    }

    public function destroy(InventoriProyektorModel $inventoriproyektor)
    {
        $inventoriproyektor->delete();

        return redirect()->route('aset.inventoriproyektor.index')->with('success', 'Data Inventori Proyektor berhasil dihapus.');
    }
}
