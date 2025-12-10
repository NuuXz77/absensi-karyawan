<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('absensi', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karyawan_id')->constrained('karyawan')->cascadeOnDelete();

            $table->foreignId('lokasi_id')->constrained('lokasi')->cascadeOnDelete();

            $table->date('tanggal');
            $table->time('jam_masuk')->nullable();
            $table->time('jam_pulang')->nullable();

            $table->decimal('lat_masuk', 10, 7)->nullable();
            $table->decimal('long_masuk', 10, 7)->nullable();
            $table->decimal('lat_keluar', 10, 7)->nullable();
            $table->decimal('long_keluar', 10, 7)->nullable();

            $table->string('foto_masuk')->nullable();
            $table->string('foto_keluar')->nullable();

            $table->enum('status', ['tepat_waktu', 'terlambat', 'izin', 'cuti', 'alpha'])->default('alpha');

            $table->unique(['karyawan_id', 'tanggal']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('absensis');
    }
};
