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
        Schema::create('participantes', function (Blueprint $table) {
            $table->id();
            $table->string('nome');
            $table->string('idade');
            $table->string('cpf');
            $table->String('telefone');
            $table->string('cupomFiscal');
            $table->date('dataParticipacao');
            $table->unsignedBigInteger('idEstabelecimento');
            $table->foreign('idEstabelecimento')->references('id')->on('estabelecimentos');
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('participantes');
    }
};
