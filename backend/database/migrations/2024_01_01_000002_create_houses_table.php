<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('houses', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_rumah', 10)->unique();
            $table->string('blok', 10)->nullable();
            $table->enum('status', ['dihuni', 'tidak_dihuni'])->default('tidak_dihuni');
            $table->foreignId('current_resident_id')->nullable()->constrained('residents')->nullOnDelete();
            $table->text('catatan')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['nomor_rumah', 'status']);
            $table->index('blok');
        });

        Schema::create('resident_house_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('house_id')->constrained()->cascadeOnDelete();
            $table->foreignId('resident_id')->nullable()->constrained()->nullOnDelete();
            $table->date('tanggal_masuk');
            $table->date('tanggal_keluar')->nullable();
            $table->enum('status', ['tetap', 'kontrak'])->default('tetap');
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->index(['house_id', 'resident_id']);
            $table->index('tanggal_masuk');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('resident_house_histories');
        Schema::dropIfExists('houses');
    }
};