<?php

namespace App\Http\Controllers\Concerns;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

trait HandlesInspectionPhoto
{
    private function storeInspectionPhoto(Request $request, Model $inspection): void
    {
        if (!$request->hasFile('photos.0')) {
            return;
        }

        if ($inspection->photo_path) {
            Storage::disk('public')->delete($inspection->photo_path);
        }

        $inspection->update([
            'photo_path' => $request->file('photos.0')->store("inspections/{$inspection->getKey()}", 'public'),
        ]);
    }

    private function photoValidationRules(): array
    {
        return [
            'photos' => ['nullable', 'array', 'max:1'],
            'photos.*' => ['image', 'mimes:jpeg,png,jpg,webp', 'max:5120'],
        ];
    }
}
