<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payment_types', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('slug')->unique();
            $table->decimal('nominal', 12, 2);
            $table->text('deskripsi')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['slug', 'is_active']);
        });

        Schema::create('monthly_bills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('house_id')->constrained()->cascadeOnDelete();
            $table->foreignId('payment_type_id')->constrained('payment_types')->cascadeOnDelete();
            $table->integer('bulan'); // 1-12
            $table->integer('tahun');
            $table->decimal('nominal', 12, 2);
            $table->enum('status', ['belum_lunas', 'lunas', 'dibebaskan'])->default('belum_lunas');
            $table->date('jatuh_tempo');
            $table->date('tanggal_lunas')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->unique(['house_id', 'payment_type_id', 'bulan', 'tahun'], 'unique_bill');
            $table->index(['bulan', 'tahun', 'status']);
            $table->index(['house_id', 'status']);
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pembayaran', 20)->unique();
            $table->foreignId('house_id')->constrained()->cascadeOnDelete();
            $table->foreignId('resident_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('payment_type_id')->constrained('payment_types')->cascadeOnDelete();
            $table->foreignId('monthly_bill_id')->nullable()->constrained('monthly_bills')->nullOnDelete();
            $table->decimal('nominal', 12, 2);
            $table->date('tanggal_bayar');
            $table->enum('metode_pembayaran', ['tunai', 'transfer', 'lainnya'])->default('tunai');
            $table->string('bukti_bayar')->nullable();
            $table->text('keterangan')->nullable();
            $table->foreignId('created_by')->constrained('users');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['kode_pembayaran', 'tanggal_bayar']);
            $table->index(['house_id', 'payment_type_id']);
            $table->index('created_by');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payments');
        Schema::dropIfExists('monthly_bills');
        Schema::dropIfExists('payment_types');
    }
};