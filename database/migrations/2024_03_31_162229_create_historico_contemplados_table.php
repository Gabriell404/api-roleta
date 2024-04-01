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
        Schema::create('historico_contemplados', function (Blueprint $table) {
            $table->id();
            $table->string('pesoPremio');
            $table->dateTime('dataHoraGiro');
            $table->dateTime('dataHoraContemplacao');
            $table->unsignedBigInteger('idGiroSorte');
            $table->unsignedBigInteger('idParticipante');
            $table->unsignedBigInteger('idPremioContemplado');
            $table->unsignedBigInteger('idEstabelecimento');
            $table->foreign('idGiroSorte')->references('id')->on('giro_sortes');
            $table->foreign('idParticipante')->references('id')->on('participantes');
            $table->foreign('idPremioContemplado')->references('id')->on('premios');
            $table->foreign('idEstabelecimento')->references('id')->on('estabelecimentos');
            $table->timestamps();
        });
    }
    
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historico_contemplados');
    }
};
