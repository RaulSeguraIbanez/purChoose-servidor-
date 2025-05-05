<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMetodosPagoTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('metodos_pago', function (Blueprint $table) {
            $table->id('id_metodo'); // ID del método de pago
            $table->unsignedBigInteger('user_id'); // Relación con el usuario
            $table->string('tipo'); // Tipo de método de pago: 'tarjeta', 'paypal', 'apple_pay', 'google_pay'
            $table->string('nombre')->nullable(); // Nombre del titular (para tarjetas)
            $table->string('num_tarjeta')->nullable(); // Número de tarjeta (encriptado, para tarjetas)
            $table->string('fecha_caducidad')->nullable(); // Fecha de caducidad (MM/YY, para tarjetas)
            $table->string('codigo_validacion')->nullable(); // Código de validación (encriptado, para tarjetas)
            $table->string('email')->nullable(); // Correo electrónico (para PayPal)
            $table->string('password')->nullable(); // Contraseña del correo (encriptada, para PayPal)
            $table->timestamps();

            // Relación con la tabla users
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('metodos_pago');
    }
}