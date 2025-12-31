<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique(); // BK-240101-001
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('kavling_id')->nullable()->constrained('kavlings')->onDelete('set null');
            $table->date('tanggal_check_in');
            $table->date('tanggal_check_out');
            $table->decimal('total_harga', 12, 2);
            $table->string('status', 20)->index(); // pending, waiting_verification, confirmed, rejected, cancelled, completed
            $table->string('bukti_pembayaran')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->string('qr_code')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
