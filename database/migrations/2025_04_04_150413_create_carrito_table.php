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
        Schema::create('carrito', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('producto_id')->constrained('productos')->onDelete('cascade');
            $table->integer('cantidad')->default(1);
            $table->string('estado', ['pagado', 'no pagado','recibido', 'enviado', 'cancelado']) ->default('no pagado'); // 'pagado', 'no pagado', 'recibido', 'enviado', 'cancelado'
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carrito');
    }
};
