<?php

namespace App\Http\Controllers;

use App\Models\InventoriUpsModel;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class InventoriUpsController extends Controller
{
    public function index(Request $request)
    {
        $query = InventoriUpsModel::query();

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

        $dataInventoriUps = $query->latest()->paginate(10)->withQueryString();

        return view('aset.inventoriups.index', compact('dataInventoriUps'));
    }

    public function create()
    {
        return view('aset.inventoriups.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'no_aset' => ['required', 'string', 'max:255', 'unique:inventori_ups,no_aset'],
            'merek' => ['required', 'string', 'max:255'],
            'tipe' => ['required', 'string', 'max:255'],
            'serial_number' => ['required', 'string', 'max:255'],
            'department' => ['required', 'string', 'max:255'],
            'lokasi' => ['required', 'string', 'max:255'],
        ]);

        InventoriUpsModel::create($validated);

        return redirect()->route('aset.inventoriups.index')->with('success', 'Data Inventori UPS berhasil ditambahkan.');
    }

    public function edit(InventoriUpsModel $inventoriups)
    {
        return view('aset.inventoriups.edit', compact('inventoriups'));
    }

    public function update(Request $request, InventoriUpsModel $inventoriups)
    {
        $validated = $request->validate([
            'no_aset' => ['required', 'string', 'max:255', Rule::unique('inventori_ups', 'no_aset')->ignore($inventoriups->id)],
            'merek' => ['required', 'string', 'max:255'],
            'tipe' => ['required', 'string', 'max:255'],
            'serial_number' => ['required', 'string', 'max:255'],
            'department' => ['required', 'string', 'max:255'],
            'lokasi' => ['required', 'string', 'max:255'],
        ]);

        $inventoriups->update($validated);

        return redirect()->route('aset.inventoriups.index')->with('success', 'Data Inventori UPS berhasil diubah.');
    }

    public function destroy(InventoriUpsModel $inventoriups)
    {
        $inventoriups->delete();

        return redirect()->route('aset.inventoriups.index')->with('success', 'Data Inventori UPS berhasil dihapus.');
    }
}
