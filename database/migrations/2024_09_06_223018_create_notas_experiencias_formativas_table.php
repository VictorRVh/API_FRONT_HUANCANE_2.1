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
        Schema::create('notas_experiencias_formativas', function (Blueprint $table) {
            $table->uuid('id_nota')->primary();
            $table->string('nota');
            $table->uuid('id_experiencia'); // Cambia a unsignedBigInteger para coincidir con la definición en experiencias_formativas
            $table->foreign('id_experiencia')->references('id_experiencia')->on('experiencias_formativas')->onDelete('cascade');
            $table->uuid('id_estudiante'); // Cambia a unsignedBigInteger para coincidir con la definición en usuarios
            $table->foreign('id_estudiante')->references('id')->on('users')->onDelete('cascade');
            $table->uuid('id_grupo');
            $table->foreign('id_grupo')->references('id_grupo')->on('grupos')->onDelete('cascade');
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notas_experiencias_formativas');
    }
};
