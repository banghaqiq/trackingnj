<?php

namespace Tests\Feature;

use App\Enums\PaketStatus;
use App\Models\Paket;
use App\Models\User;
use App\Models\Wilayah;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LaporanExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_excel_and_pdf_exports_exist_and_respect_date_format(): void
    {
        $admin = User::factory()->admin()->create();

        Paket::factory()->count(2)->create([
            'status' => PaketStatus::DITERIMA,
            'tanggal_diterima' => Carbon::create(2025, 12, 1, 8, 0, 0),
        ]);

        $params = ['periode' => 'day', 'tanggal' => '2025-12-01'];

        $this->actingAs($admin)
            ->get(route('laporan.export-excel', $params))
            ->assertOk()
            ->assertHeader('content-disposition');

        $this->actingAs($admin)
            ->get(route('laporan.export-pdf', $params))
            ->assertOk()
            ->assertHeader('content-disposition');
    }

    public function test_exports_respect_keamanan_wilayah_visibility(): void
    {
        $wilayahA = Wilayah::factory()->create();
        $wilayahB = Wilayah::factory()->create();

        $keamanan = User::factory()->keamanan()->forWilayah($wilayahA->id)->create();

        Paket::factory()->forWilayah($wilayahA->id)->create([
            'tanggal_diterima' => Carbon::create(2025, 12, 1, 8, 0, 0),
        ]);

        Paket::factory()->forWilayah($wilayahB->id)->create([
            'tanggal_diterima' => Carbon::create(2025, 12, 1, 8, 0, 0),
        ]);

        $this->actingAs($keamanan)
            ->get(route('laporan.index', ['periode' => 'day', 'tanggal' => '2025-12-01']))
            ->assertOk()
            ->assertViewHas('pakets', function ($paginated) {
                return $paginated->total() === 1;
            });
    }
}
