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
        schema::create('programas', function (Blueprint $table) {
            // $table->id('id_programa');
            $table->uuid('id_programa')->primary();
            $table->string('nombre_programa');
            $table->integer('horas_semanales');
            $table->string('unidades_competencia');
            $table->uuid('id_plan');  // Asegúrate de que el tipo sea bigint
            $table->foreign('id_plan')->references('id_plan')->on('planes')->onDelete('cascade');  // Definir manualmente la relación
            $table->uuid('id_especialidad');
            $table->foreign('id_especialidad')->references('id_especialidad')->on('especialidades')->onDelete('cascade');
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programas');
    }
};
