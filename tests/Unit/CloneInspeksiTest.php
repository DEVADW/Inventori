<?php

namespace Tests\Unit;

use App\Services\CloneInspeksi;
use PHPUnit\Framework\TestCase;

class CloneInspeksiTest extends TestCase
{
    public function testPrepareCloneAttributesRemovesPhotoPathAndSystemFields(): void
    {
        $service = new CloneInspeksi();

        $attributes = [
            'id' => 1,
            'photo_path' => 'inspections/1/test.jpg',
            'tanggal_inspeksi' => '2026-09-10',
            'status' => 'approved',
            'created_at' => '2026-09-10 08:00:00',
            'updated_at' => '2026-09-10 08:30:00',
        ];

        $prepared = $service->prepareCloneAttributes($attributes);

        $this->assertArrayNotHasKey('id', $prepared);
        $this->assertArrayNotHasKey('created_at', $prepared);
        $this->assertArrayNotHasKey('updated_at', $prepared);
        $this->assertArrayNotHasKey('photo_path', $prepared);
    }
}
