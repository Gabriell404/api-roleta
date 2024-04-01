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
        Schema::create('promotors', function (Blueprint $table) {
            $table->id();
            $table->string('cpf');
            $table->string('nome');
            $table->date('dataAcaoPromocional');
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
        Schema::dropIfExists('promotors');
    }
};
