<?php

namespace Tests\Feature;

use App\Models\IccUnitModels;
use Tests\TestCase;

class IccApiSelect2Test extends TestCase
{
    public function test_icc_unit_select2_accepts_q_parameter_and_returns_unit_data(): void
    {
        IccUnitModels::query()->delete();

        IccUnitModels::query()->create([
            'device_name' => 'C1234',
            'fleet' => 'ADW',
            'sim' => '8115110000',
            'imei' => '865847000000000',
        ]);

        $response = $this->getJson('/api/icc/units/select2?q=C1234');

        $response->assertOk()
            ->assertJsonFragment([
                'code_unit' => 'C1234',
            ]);
    }
}
