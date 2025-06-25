<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('garden_id')->constrained('gardens')->onDelete('cascade');
            $table->string('judul');
            $table->text('deskripsi')->nullable();
            $table->timestamp('tanggal_kegiatan');
            $table->json('penanggung_jawab_ids')->nullable(); // Untuk menyimpan array user ID
            $table->enum('status', ['mendatang', 'selesai'])->default('mendatang');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('schedules');
    }
};
