<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paket', function (Blueprint $table) {
            $table->id();
            $table->string('kode_resi')->unique();
            $table->string('nama_penerima');
            $table->string('telepon_penerima')->nullable();
            $table->foreignId('wilayah_id')->nullable()->constrained('wilayah')->onDelete('set null');
            $table->foreignId('asrama_id')->nullable()->constrained('asrama')->onDelete('set null');
            $table->string('nomor_kamar')->nullable();
            $table->text('alamat_lengkap')->nullable();
            $table->boolean('tanpa_wilayah')->default(false);
            $table->boolean('keluarga')->default(false);
            $table->string('nama_pengirim')->nullable();
            $table->text('keterangan')->nullable();
            $table->enum('status', ['diterima', 'diproses', 'diantar', 'selesai', 'dikembalikan'])->default('diterima');
            $table->timestamp('tanggal_diterima')->useCurrent();
            $table->timestamp('tanggal_diambil')->nullable();
            $table->foreignId('diterima_oleh')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('diantar_oleh')->nullable()->constrained('users')->onDelete('set null');
            $table->softDeletes();
            $table->timestamps();

            $table->index(['status', 'wilayah_id']);
            $table->index(['tanpa_wilayah', 'keluarga']);
            $table->index('tanggal_diterima');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paket');
    }
};
