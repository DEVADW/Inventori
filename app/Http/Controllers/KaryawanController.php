<?php

namespace App\Http\Controllers;

use App\Models\KaryawanModel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class KaryawanController extends Controller
{
    //
    public function index(Request $request)
    {
        $query = KaryawanModel::query();

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', '%'.$search.'%')->orWhere('nrp', 'like', '%'.$search.'%');
            });
        }

        $dataKaryawan = $query->latest()->paginate(10);

        return view('karyawan.index', compact('dataKaryawan'));
    }

    public function create()
    {
        return view('karyawan.create');
    }

    public function store(Request $req)
    {
        $data = $req->validate([
            'nama' => ['required', 'string', 'max:255'],
            'nrp' => ['required', 'string', 'max:50'],
            'jabatan' => ['required', 'string', 'max:100'],
            'departemen' => ['required', 'string', 'max:100'],
            'qr_code'    => ['nullable', 'string', 'max:255'],
        ]);

        DB::beginTransaction();

        try {
            $teksQr = 'Nama : '.$data['nama'].' | NRP : '.$data['nrp'].' | Jabatan : '.$data['jabatan'];

            $data['qr_code'] = $teksQr;

            
            KaryawanModel::create($data);
            
            User::create([
                'nama' => $data['nama'],
                'nrp' => $data['nrp'],
                'jabatan' => $data['jabatan'],
                'role' => 'user',
                'password' => Hash::make($data['nrp'])
            ]);

            DB::commit();

            return redirect()->route('karyawan.index')->with('success_data', 'data berhasil di simpan!');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Gagal Menyimpan data ' . $e->getMessage());
        }

        // return redirect()->route('karyawan.index')->with('success_data', 'data berhasil di simpan!');
    }

    public function edit(KaryawanModel $karyawan)
    {
        return view('karyawan.edit', compact('karyawan'));
    }

    public function update(Request $req, KaryawanModel $karyawan)
    {
        $data = $req->validate([
            'nama' => ['required', 'string', 'max:255'],
            'nrp' => [
                'required',
                'string',
                'max:50',
                Rule::unique('karyawans', 'nrp')->ignore($karyawan->id),
            ],
            'jabatan' => ['required', 'string', 'max:100'],
            'departemen' => ['required', 'string', 'max:100'],
            'qr_code' => ['nullable', 'string', 'max:255'],
        ]);

     DB::beginTransaction();

        try {
            $karyawan->update($data);

            // Update user table
            User::where('nrp', $karyawan->getOriginal('nrp'))->update([
                'nama' => $data['nama'],
                'nrp' => $data['nrp'],
                'jabatan' => $data['jabatan'],
            ]);

            DB::commit();

            return redirect()
                ->route('karyawan.index')
                ->with('success', 'Data karyawan berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', 'Gagal Memperbarui data ' . $e->getMessage());
        }
    }

    public function destroy(KaryawanModel $karyawan)
    {
        $karyawan->delete();

        return redirect()
            ->route('karyawan.index')
            ->with('success', 'Data karyawan berhasil dihapus.');
    }
}
