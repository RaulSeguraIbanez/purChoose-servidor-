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
        Schema::create('valoraciones', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('producto_id'); // ID del producto relacionado
            $table->unsignedBigInteger('usuario_id');  // ID del usuario que valora el producto
            $table->tinyInteger('puntuacion')->unsigned(); // Puntuación de 0 al 5
            $table->timestamps();

            // Claves foráneas
            $table->foreign('producto_id')->references('id')->on('productos')->onDelete('cascade');  // Relación con la tabla productos
            $table->foreign('usuario_id')->references('id')->on('users')->onDelete('cascade'); // Relación con la tabla users
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('valoraciones');
    }
};
