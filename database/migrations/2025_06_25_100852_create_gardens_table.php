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
        Schema::create('gardens', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kebun');
            $table->text('alamat')->nullable();
            $table->decimal('koordinat_asli_lat', 10, 8)->nullable();
            $table->decimal('koordinat_asli_lng', 11, 8)->nullable();
            $table->string('denah_url')->nullable();
            $table->foreignId('pemilik_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gardens');
    }
};
