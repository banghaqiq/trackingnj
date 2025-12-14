<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('paket_status_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('paket_id')->constrained('paket')->onDelete('cascade');
            $table->enum('status_dari', ['diterima', 'diproses', 'diantar', 'selesai', 'dikembalikan'])->nullable();
            $table->enum('status_ke', ['diterima', 'diproses', 'diantar', 'selesai', 'dikembalikan']);
            $table->foreignId('diubah_oleh')->nullable()->constrained('users')->onDelete('set null');
            $table->text('catatan')->nullable();
            $table->timestamp('created_at')->useCurrent();

            $table->index(['paket_id', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('paket_status_logs');
    }
};
