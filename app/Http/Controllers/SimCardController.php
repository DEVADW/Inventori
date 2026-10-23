<?php

namespace App\Http\Controllers;

use App\Models\SimCardModel;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SimCardController extends Controller
{
    public function index(Request $request)
    {
        $query = SimCardModel::query();

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('nomor_msisdn', 'like', '%'.$search.'%')
                    ->orWhere('remark', 'like', '%'.$search.'%');
            });
        }

        $dataSimCard = $query->latest()->paginate(10)->withQueryString();

        $totalTerpasang = SimCardModel::where('status_kartu', 'Terpasang')->count();
        $totalStandby = SimCardModel::where('status_kartu', 'Standby')->count();
        $totalNonaktif = SimCardModel::where('status_kartu', 'Nonaktif')->count();

        return view('aset.simcard.index', compact('dataSimCard', 'totalTerpasang', 'totalStandby', 'totalNonaktif'));
    }

    public function create()
    {
        return view('aset.simcard.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomor_msisdn' => ['required', 'string', 'max:20', 'unique:sim_cards,nomor_msisdn'],
            'jumlah_paket_kuota' => ['nullable', 'string', 'max:255'],
            'status_kartu' => ['required', Rule::in(['Terpasang', 'Standby', 'Nonaktif'])],
            'remark' => ['nullable', 'string'],
        ]);

        SimCardModel::create($validated);

        return redirect()->route('aset.simcard.index')->with('success', 'Data SIM Card berhasil ditambahkan.');
    }

    public function edit(SimCardModel $simcard)
    {
        return view('aset.simcard.edit', compact('simcard'));
    }

    public function update(Request $request, SimCardModel $simcard)
    {
        $validated = $request->validate([
            'nomor_msisdn' => ['required', 'string', 'max:20', Rule::unique('sim_cards', 'nomor_msisdn')->ignore($simcard->id)],
            'jumlah_paket_kuota' => ['nullable', 'string', 'max:255'],
            'status_kartu' => ['required', Rule::in(['Terpasang', 'Standby', 'Nonaktif'])],
            'remark' => ['nullable', 'string'],
        ]);

        $simcard->update($validated);

        return redirect()->route('aset.simcard.index')->with('success', 'Data SIM Card berhasil diubah.');
    }

    public function destroy(SimCardModel $simcard)
    {
        $simcard->delete();

        return redirect()->route('aset.simcard.index')->with('success', 'Data SIM Card berhasil dihapus.');
    }
}
