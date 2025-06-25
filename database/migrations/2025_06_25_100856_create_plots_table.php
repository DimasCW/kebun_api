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
        Schema::create('plots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('garden_id')->constrained('gardens')->onDelete('cascade');
            $table->string('nama_petak');
            $table->foreignId('pemilik_id')->nullable()->constrained('users')->onDelete('set null');
            $table->decimal('posisi_peta_x', 8, 2)->nullable();
            $table->decimal('posisi_peta_y', 8, 2)->nullable();
            $table->enum('status', ['ditempati', 'tersedia'])->default('tersedia');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plots');
    }
};
