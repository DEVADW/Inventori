<?php

namespace App\Http\Controllers;

use App\Models\InventoriTvModel;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class InventoriTvController extends Controller
{
    public function index(Request $request)
    {
        $query = InventoriTvModel::query();

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

        $dataInventoriTv = $query->latest()->paginate(10)->withQueryString();

        return view('aset.inventoritv.index', compact('dataInventoriTv'));
    }

    public function create()
    {
        return view('aset.inventoritv.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'no_aset' => ['required', 'string', 'max:255', 'unique:inventori_tv,no_aset'],
            'merek' => ['required', 'string', 'max:255'],
            'tipe' => ['required', 'string', 'max:255'],
            'serial_number' => ['required', 'string', 'max:255'],
            'department' => ['required', 'string', 'max:255'],
            'lokasi' => ['required', 'string', 'max:255'],
        ]);

        InventoriTvModel::create($validated);

        return redirect()->route('aset.inventoritv.index')->with('success', 'Data Inventori TV berhasil ditambahkan.');
    }

    public function edit(InventoriTvModel $inventoritv)
    {
        return view('aset.inventoritv.edit', compact('inventoritv'));
    }

    public function update(Request $request, InventoriTvModel $inventoritv)
    {
        $validated = $request->validate([
            'no_aset' => ['required', 'string', 'max:255', Rule::unique('inventori_tv', 'no_aset')->ignore($inventoritv->id)],
            'merek' => ['required', 'string', 'max:255'],
            'tipe' => ['required', 'string', 'max:255'],
            'serial_number' => ['required', 'string', 'max:255'],
            'department' => ['required', 'string', 'max:255'],
            'lokasi' => ['required', 'string', 'max:255'],
        ]);

        $inventoritv->update($validated);

        return redirect()->route('aset.inventoritv.index')->with('success', 'Data Inventori TV berhasil diubah.');
    }

    public function destroy(InventoriTvModel $inventoritv)
    {
        $inventoritv->delete();

        return redirect()->route('aset.inventoritv.index')->with('success', 'Data Inventori TV berhasil dihapus.');
    }
}
