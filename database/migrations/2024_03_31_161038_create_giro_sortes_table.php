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
        Schema::create('giro_sortes', function (Blueprint $table) {
            $table->id();
            $table->dateTime('dataHoraGiro');
            $table->dateTime('dataHoraContemplacao');
            $table->unsignedBigInteger('idPremio');
            $table->unsignedBigInteger('idPromotor');
            $table->unsignedBigInteger('idEstabelecimento');
            $table->unsignedBigInteger('idParticipante');
            $table->unsignedBigInteger('idPremioContemplado');
            $table->foreign('idPremio')->references('id')->on('premios');
            $table->foreign('idPromotor')->references('id')->on('promotors');
            $table->foreign('idEstabelecimento')->references('id')->on('estabelecimentos');
            $table->foreign('idParticipante')->references('id')->on('participantes');
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('giro_sortes');
    }
};
