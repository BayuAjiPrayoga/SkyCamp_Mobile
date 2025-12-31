<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('peralatan', function (Blueprint $table) {
            $table->id();
            $table->string('nama');
            $table->string('kategori', 50); // tenda, masak, tidur, lainnya
            $table->integer('stok_total')->default(0);
            $table->decimal('harga_sewa', 10, 2);
            $table->text('deskripsi')->nullable();
            $table->string('gambar')->nullable();
            $table->string('kondisi', 20)->default('baik'); // baik, perlu_perbaikan, rusak
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peralatan');
    }
};
