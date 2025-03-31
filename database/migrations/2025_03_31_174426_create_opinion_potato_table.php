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
        Schema::create('opinion_potato', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id'); // Relación con producto
            $table->unsignedBigInteger('user_id'); // Relación con usuario
            $table->string('opinion'); // Opinion del usuario
            $table->timestamps();

            // Claves foráneas
            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('opinion_potato');
    }
};
