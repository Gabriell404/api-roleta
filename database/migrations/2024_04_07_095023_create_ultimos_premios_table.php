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
        Schema::create('ultimos_premios', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('idPremio');
            $table->foreign('idPremio')->references('id')->on('premios');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ultimos_premios');
    }
};
