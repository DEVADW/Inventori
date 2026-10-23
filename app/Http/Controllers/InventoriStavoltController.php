<?php

namespace App\Http\Controllers;

use App\Models\InventoriStavoltModel;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class InventoriStavoltController extends Controller
{
    public function index(Request $request)
    {
        $query = InventoriStavoltModel::query();

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

        $dataInventoriStavolt = $query->latest()->paginate(10)->withQueryString();

        return view('aset.inventoristavolt.index', compact('dataInventoriStavolt'));
    }

    public function create()
    {
        return view('aset.inventoristavolt.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'no_aset' => ['required', 'string', 'max:255', 'unique:inventori_stavolt,no_aset'],
            'merek' => ['required', 'string', 'max:255'],
            'tipe' => ['required', 'string', 'max:255'],
            'serial_number' => ['required', 'string', 'max:255'],
            'department' => ['required', 'string', 'max:255'],
            'lokasi' => ['required', 'string', 'max:255'],
        ]);

        InventoriStavoltModel::create($validated);

        return redirect()->route('aset.inventoristavolt.index')->with('success', 'Data Inventori Stavolt berhasil ditambahkan.');
    }

    public function edit(InventoriStavoltModel $inventoristavolt)
    {
        return view('aset.inventoristavolt.edit', compact('inventoristavolt'));
    }

    public function update(Request $request, InventoriStavoltModel $inventoristavolt)
    {
        $validated = $request->validate([
            'no_aset' => ['required', 'string', 'max:255', Rule::unique('inventori_stavolt', 'no_aset')->ignore($inventoristavolt->id)],
            'merek' => ['required', 'string', 'max:255'],
            'tipe' => ['required', 'string', 'max:255'],
            'serial_number' => ['required', 'string', 'max:255'],
            'department' => ['required', 'string', 'max:255'],
            'lokasi' => ['required', 'string', 'max:255'],
        ]);

        $inventoristavolt->update($validated);

        return redirect()->route('aset.inventoristavolt.index')->with('success', 'Data Inventori Stavolt berhasil diubah.');
    }

    public function destroy(InventoriStavoltModel $inventoristavolt)
    {
        $inventoristavolt->delete();

        return redirect()->route('aset.inventoristavolt.index')->with('success', 'Data Inventori Stavolt berhasil dihapus.');
    }
}
