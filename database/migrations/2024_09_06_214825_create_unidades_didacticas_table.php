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
        schema::create('unidades_didacticas', function (Blueprint $table) {
            $table->uuid('id_unidad_didactica')->primary();
            $table->string('nombre_unidad');
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->integer('creditos')->nullable();
            $table->integer('dias')->nullable();
            $table->integer('horas')->nullable();
            $table->string('capacidad')->nullable();

            $table->char('numero_unidad', 1)->nullable();

            $table->uuid('id_programa');  // Asegúrate de que el tipo sea bigint
            $table->foreign('id_programa')->references('id_programa')->on('programas')->onDelete('cascade');  // Definir la relación manualmente
            $table->timestamps();
        });
    }



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unidades_didacticas');
    }
};
