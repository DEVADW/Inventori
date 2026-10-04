<?php

namespace App\Http\Controllers;

use App\Exports\BukuTamuExport;
use App\Models\BukuTamuModel;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class BukuTamuController extends Controller
{
    //
    public function index(Request $request)
    {
        $query = BukuTamuModel::query();
        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', '%'.$search.'%')
                    ->orWhere('no_telp', 'like', '%'.$search.'%')
                    ->orWhere('instansi', 'like', '%'.$search.'%');
            });
        }

        $dataTamu = $query->latest()->paginate(10)->withQueryString();

        return view('buku_tamu.index', compact('dataTamu'));
    }

    public function create()
    {
        return view('buku_tamu.create');
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'nama' => 'required|string|max:255',
            'no_telp' => 'required|string|max:20',
            'nrp' => 'nullable|string|max:50',
            'instansi' => 'required|string|max:255',
            'keperluan' => 'required|string',
        ]);

        BukuTamuModel::create($validatedData);

        return redirect()->route('tamu.index')->with('success', 'Data tamu berhasil ditambahkan!');
    }

    public function edit(string $id)
    {
        $tamu = BukuTamuModel::findOrFail($id);

        return view('buku_tamu.edit', compact('tamu'));
    }

    public function update(Request $request, string $id)
    {
        $validatedData = $request->validate([
            'nama' => 'required|string|max:255',
            'no_telp' => 'required|string|max:20',
            'nrp' => 'nullable|string|max:50',
            'instansi' => 'required|string|max:255',
            'keperluan' => 'required|string',
        ]);

        $tamu = BukuTamuModel::findOrFail($id);

        $tamu->update($validatedData);

        return redirect()->route('tamu.index')->with('success', 'Data tamu berhasil diperbarui!');
    }

    public function destroy(string $id)
    {
        $tamu = BukuTamuModel::findOrFail($id);

        $tamu->delete();

        return redirect()->route('tamu.index')->with('success', 'Data tamu berhasil dihapus!');
    }

    public function export()
    {
        return Excel::download(new BukuTamuExport, 'buku_tamu.xlsx');
    }
}
