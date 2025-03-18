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
        Schema::create('notas_unidades_didacticas', function (Blueprint $table) {
            $table->uuid('id_nota')->primary();
            $table->string('nota');
            $table->uuid('id_unidad_didactica'); // Asegúrate de que el tipo de dato coincida
            $table->foreign('id_unidad_didactica')->references('id_unidad_didactica')->on('unidades_didacticas')->onDelete('cascade');
            $table->uuid('id_estudiante'); // Asegúrate de que el tipo de dato coincida
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
        Schema::dropIfExists('notas_unidades_didacticas');
    }
};
