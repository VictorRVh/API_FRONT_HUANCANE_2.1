<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        schema::create('experiencias_formativas', function (Blueprint $table) {
            $table->uuid('id_experiencia')->primary();
            $table->string('nombre_experiencia');
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->integer('creditos')->nullable();
            $table->integer('dias')->nullable();
            $table->integer('horas')->nullable();
            $table->uuid('id_programa');  // Asegúrate de que el tipo sea bigint
            $table->foreign('id_programa')->references('id_programa')->on('programas')->onDelete('cascade');  // Relación definida manualmente
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('experiencias_formativas');
    }
};
