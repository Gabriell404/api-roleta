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
        Schema::create('premios', function (Blueprint $table) {
            $table->id();
            $table->string('nomePremio');
            $table->string('codigoColor');
            $table->string('caminhoImage');
            $table->integer('pesoPremio');
            $table->integer('estoque')->nullable();
            $table->enum('status', ['ativo', 'desativado'])->default('desativado');
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('premios');
    }
};
