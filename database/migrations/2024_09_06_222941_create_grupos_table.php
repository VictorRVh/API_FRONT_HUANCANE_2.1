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
        Schema::create('grupos', function (Blueprint $table) {
            $table->uuid('id_grupo')->primary(); // Clave primaria
            $table->string('nombre_grupo');

            $table->foreignId('id_sede')->constrained('sedes', 'id_sede')->onDelete('cascade');
            $table->foreignId('id_turno')->constrained('turnos', 'id')->onDelete('cascade');

            $table->uuid('id_especialidad');
            $table->foreign('id_especialidad')->references('id_especialidad')->on('especialidades')->onDelete('cascade');

            $table->uuid('id_plan');
            $table->foreign('id_plan')->references('id_plan')->on('planes')->onDelete('cascade');

            $table->uuid('id_programa');
            $table->foreign('id_programa')->references('id_programa')->on('programas')->onDelete('cascade');

            $table->uuid('id_docente');
            $table->foreign('id_docente')->references('id')->on('users')->onDelete('cascade');

            $table->timestamps();
        });
    }



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('grupos');
    }
};
