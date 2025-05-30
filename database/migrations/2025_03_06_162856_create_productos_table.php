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
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->text('descripcion');
            $table->float('precio');
            $table->string('estado');
            $table->string('ubicacion')->nullable();
            $table->boolean('oferta')->default(false);
            $table->boolean('individual')->default(true);
            $table->integer('quantity')->default(1);
            $table->unsignedInteger('views')->default(0);
            $table->unsignedInteger('ventas')->default(0);
            $table->boolean('activo')->default(true);
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos');
    }
};
