<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardSpecialRoleTest extends TestCase
{
    use RefreshDatabase;

    public function test_ict_technician_only_sees_icc_menu_item_in_sidebar(): void
    {
        $user = User::factory()->create([
            'nama' => 'ICT Technician Test',
            'email' => 'ict.technician@example.com',
            'nrp' => 'ICT001',
            'jabatan' => 'ICT Technician',
            'role' => 'user',
        ]);

        $this->actingAs($user);

        $response = $this->get('/dashboard');

        $response->assertOk();
        $response->assertSee('Inspeksi ICC');
        $response->assertDontSee('Inspeksi OFA');
    }

    public function test_hardware_engineer_only_sees_ofa_menu_item_in_sidebar(): void
    {
        $user = User::factory()->create([
            'nama' => 'Hardware Engineer Test',
            'email' => 'hardware.engineer@example.com',
            'nrp' => 'HE001',
            'jabatan' => 'Hardware Engineer',
            'role' => 'user',
        ]);

        $this->actingAs($user);

        $response = $this->get('/dashboard');

        $response->assertOk();
        $response->assertSee('Inspeksi OFA');
        $response->assertDontSee('Inspeksi ICC');
    }
}
