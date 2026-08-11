<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * "Source Code" sebagai produk yang dijual diganti dengan halaman
     * "Jasa Pembuatan Aplikasi" (portofolio aplikasi, bukan produk checkout).
     */
    public function up(): void
    {
        Schema::dropIfExists('source_codes');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('source_codes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description');
            $table->decimal('price', 12, 2);
            $table->string('file_path');
            $table->string('demo_url')->nullable();
            $table->string('tech_stack')->nullable();
            $table->string('image')->nullable();
            $table->timestamps();
        });
    }
};
