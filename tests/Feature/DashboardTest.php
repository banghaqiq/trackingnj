<?php

namespace Tests\Feature;

use App\Enums\PaketStatus;
use App\Models\Paket;
use App\Models\User;
use App\Models\Wilayah;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_shows_correct_totals_for_admin(): void
    {
        $admin = User::factory()->admin()->create();

        Paket::factory()->count(3)->create([
            'tanggal_diterima' => Carbon::today()->setTime(9, 0),
            'status' => PaketStatus::DITERIMA,
        ]);

        Paket::factory()->count(2)->create([
            'tanggal_diterima' => Carbon::yesterday(),
            'status' => PaketStatus::DITERIMA,
        ]);

        Paket::factory()->count(4)->diantar()->create();
        Paket::factory()->count(1)->tanpaWilayah()->diterima()->create();

        $this->actingAs($admin)
            ->get(route('dashboard.index'))
            ->assertOk()
            ->assertViewHas('todayDiterima', 3)
            ->assertViewHas('belumDiambil', 4)
            ->assertViewHas('salahWilayah', 1);
    }

    public function test_dashboard_respects_keamanan_wilayah_visibility(): void
    {
        $wilayahA = Wilayah::factory()->create();
        $wilayahB = Wilayah::factory()->create();

        $keamanan = User::factory()->keamanan()->forWilayah($wilayahA->id)->create();

        Paket::factory()->count(2)->forWilayah($wilayahA->id)->create([
            'tanggal_diterima' => Carbon::today(),
        ]);

        Paket::factory()->count(5)->forWilayah($wilayahB->id)->create([
            'tanggal_diterima' => Carbon::today(),
        ]);

        $this->actingAs($keamanan)
            ->get(route('dashboard.index'))
            ->assertOk()
            ->assertViewHas('todayDiterima', 2);

        $this->actingAs($keamanan)
            ->getJson(route('dashboard.chart-data', ['period' => 'daily']))
            ->assertOk()
            ->assertJsonStructure(['labels', 'data']);
    }
}
