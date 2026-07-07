<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('barcode_history', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tournee_line_id');
            $table->string('barcode_scanned');
            $table->string('article_code');
            $table->boolean('matched')->default(false);
            $table->unsignedBigInteger('chauffeur_id')->nullable();
            $table->foreign('tournee_line_id')->references('id')->on('tournee_lines')->onDelete('cascade');
            $table->foreign('chauffeur_id')->references('id')->on('chauffeurs')->onDelete('set null');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barcode_history');
    }
};