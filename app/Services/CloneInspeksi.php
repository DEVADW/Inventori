<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class CloneInspeksi
{
    public function prepareCloneAttributes(array $attributes): array
    {
        unset($attributes['id']);
        unset($attributes['created_at']);
        unset($attributes['updated_at']);

        // Jangan ikut clone file foto / photo_path dari inspeksi asal.
        foreach (['photo_path', 'foto_path', 'image_path'] as $field) {
            if (array_key_exists($field, $attributes)) {
                unset($attributes[$field]);
            }
        }

        return $attributes;
    }

    public function cloneInpeksi(Request $request, Model $data, string $route)
    {
        try {
            $request->validate([
                'dari_bulan' => 'required|string', // Format dari HTML: YYYY-MM
                'ke_bulan'   => 'required|string', // Format dari HTML: YYYY-MM
            ]);

            $sourceParts = explode('-', $request->dari_bulan);
            $yearSource  = $sourceParts[0];
            $monthSource = $sourceParts[1];

            $targetParts = explode('-', $request->ke_bulan);
            $targetYear  = $targetParts[0];
            $targetMonth = $targetParts[1];

            $tableName = (new $data)->getTable();

            $dataAsal = $data->all()->filter(function ($item) use ($yearSource, $monthSource) {
                if (!$item->tanggal_inspeksi) return false;

                $timestamp = strtotime($item->tanggal_inspeksi);
                $y = date('Y', $timestamp);
                $m = date('m', $timestamp);

                return $y == $yearSource && $m == $monthSource;
            });

            if ($dataAsal->isEmpty()) {
                return redirect()->back()->with('error', "Tidak ada data inspeksi yang ditemukan pada bulan {$monthSource}-{$yearSource}.");
            }

            $count = 0;

            foreach ($dataAsal as $item) {
                $attributes = $this->prepareCloneAttributes($item->getAttributes());

                $randomDay = str_pad(rand(1, 25), 2, '0', STR_PAD_LEFT);
                $attributes['tanggal_inspeksi'] = "{$targetYear}-{$targetMonth}-{$randomDay}";

                // === RESET STATUS APPROVAL ===
                // Sesuaikan nama kolom & nilai default dengan skema tabelmu
                if (array_key_exists('status', $attributes)) {
                    $attributes['status'] = 'pending'; // ganti sesuai nilai default sistemmu
                }
                if (array_key_exists('is_approved', $attributes)) {
                    $attributes['is_approved'] = 0;
                }
                if (array_key_exists('approved_by', $attributes)) {
                    $attributes['approved_by'] = null;
                }
                if (array_key_exists('approved_at', $attributes)) {
                    $attributes['approved_at'] = null;
                }
                // ==============================

                \DB::table($tableName)->insert($attributes);
                $count++;
            }

            $namaBulanTujuan = date('F Y', mktime(0, 0, 0, $targetMonth, 1, $targetYear));
            return redirect()->route($route)
                ->with('success', $count . ' Data inspeksi berhasil dicloning ke bulan ' . $namaBulanTujuan);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
