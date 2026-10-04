<?php

namespace App\Http\Controllers;

use App\Exports\InventarisExport;
use App\Models\InventarisModel;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;


class InventarisController extends Controller
{
    //
    public function index(Request $request)
    {
        $column = ['nrp', 'nama', 'nama_perangkat', 'no_asset', 'status_peminjaman'];
    
        $query = InventarisModel::query();

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($column, $search) {
                $q->search($column, $search);
            });
        }

        $dataInventaris = $query->latest()->paginate(10)->withQueryString();

        $totalBelumDikembalikan = InventarisModel::where('status_peminjaman', 'Belum Dikembalikan', '', '')->count();
        $totalDikembalikan = InventarisModel::where('status_peminjaman', 'Dikembalikan', '', '')->count();

        return view('inventaris.index', compact(['dataInventaris', 'totalBelumDikembalikan', 'totalDikembalikan']));
    }

    public function returnStatus(Request $request, string $id)
    {
        $request->validate([
            'status_peminjaman' => 'required',
        ]);
        $dataStatus = InventarisModel::findOrFail($id);

        $dataStatus->status_peminjaman = $request->status_peminjaman;
        if ($dataStatus->status_peminjaman == 'Dikembalikan') {
            $dataStatus->tanggal_pengembalian = Carbon::now();
        } else {
            $dataStatus->tanggal_pengembalian = null;
        }
        $dataStatus->save();

        return redirect()->back()->with('success_simpan', 'Status Berhasil Diubah');
    }

    public function create()
    {
        return view('inventaris.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'nrp' => ['required', 'string', 'max:255'],
            'nama_perangkat' => ['required', 'string', 'max:255'],
            'no_asset' => ['required', 'string', 'max:255'],
            'status_peminjaman' => ['required', Rule::in(['Belum Dikembalikan', 'Dikembalikan'])],
            'tanggal_peminjaman' => ['nullable', 'date'],
            'tanggal_pengembalian' => ['nullable', 'date'],
        ]);

        if ($validated['status_peminjaman'] === 'Dikembalikan') {
            $validated['tanggal_pengembalian'] = now();
        } else {
            $validated['tanggal_pengembalian'] = null; // PAKSA NULL saat belum dikembalikan
        }

        InventarisModel::create($validated);

        return redirect()->route('inventaris.index')->with('success', 'Data inventaris berhasil ditambahkan.');
    }

    public function edit(string $id)
    {
        $inventaris = InventarisModel::findOrFail($id);

        return view('inventaris.edit', compact('inventaris'));
    }

    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'nrp' => ['required', 'string', 'max:255'],
            'nama_perangkat' => ['required', 'string', 'max:255'],
            'no_asset' => ['required', 'string', 'max:255'],
            'status_peminjaman' => ['required', Rule::in(['Belum Dikembalikan', 'Dikembalikan'])],
            'tanggal_peminjaman' => ['nullable', 'date'],
            'tanggal_pengembalian' => ['nullable', 'date'],
        ]);

        $inventaris = InventarisModel::findOrFail($id);

        if ($validated['status_peminjaman'] === 'Dikembalikan') {
            $validated['tanggal_pengembalian'] = now();
        } else {
            $validated['tanggal_pengembalian'] = null;
        }

        $inventaris->update($validated);

        return redirect()->route('inventaris.index')->with('success', 'Data inventaris berhasil diubah.');
    }

    public function destroy(string $id)
    {
        $inventaris = InventarisModel::findOrFail($id);
        $inventaris->delete();

        return redirect()->route('inventaris.index')->with('success', 'Data inventaris berhasil dihapus.');
    }

      public function export()
    {
        return Excel::download(new InventarisExport, 'Inventaris.xlsx');
    }
}
