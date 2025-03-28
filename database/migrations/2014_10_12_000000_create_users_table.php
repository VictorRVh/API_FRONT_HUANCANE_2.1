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
        Schema::create('users', function (Blueprint $table) {
            // $table->id();
            $table->uuid('id')->primary();
            $table->string('name', 45)->nullable();
            $table->string('apellido_paterno', 45)->nullable();
            $table->string('apellido_materno', 45)->nullable();
            $table->string('dni', 8)->unique()->nullable();
            $table->char('sexo',1)->nullable();
            $table->string('celular', 9)->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
