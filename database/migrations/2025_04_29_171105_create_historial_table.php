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
        Schema::create('historial', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            // Relaciones
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade'); // Usuario que realizó la compra
            $table->foreignId('producto_id')->constrained('productos')->onDelete('cascade'); // Producto comprado

            // Datos adicionales
            $table->integer('cantidad')->default(1); // Cantidad del producto
            $table->decimal('precio_total', 8, 2); // Precio total de la compra (opcional)
            $table->enum('estado', ['pagado', 'recibido', 'enviado', 'cancelado'])->default('pagado'); // Estado del producto en el historial
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historial');
    }
};