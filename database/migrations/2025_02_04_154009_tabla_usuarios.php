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
        Schema::create('usuarios', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 50); // Ahora solo "nombre"
            $table->string('email', 50)->unique();
            $table->string('password', 255);
            $table->enum('role', ['admin', 'usuario'])->default('usuario');
            $table->timestamp('fechaRegistro')->useCurrent();
            $table->string('ubicacion')->nullable();
            $table->string('telefono', 20)->nullable();
            $table->string('fotoPerfil')->default('storage/images/userProfPic/user_profilepic_default.jpg'); // Imagen por defecto
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};
