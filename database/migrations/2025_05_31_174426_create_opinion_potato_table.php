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
            $table->string('opinion'); // Opinion del usuario
            $table->timestamps();

            // Claves foráneas
            $table->foreignId('product_id')->constrained('productos')->onDelete('cascade');                                                                            //relacionar cada opinion con un producto
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');                                                                                  // pa saber que cada opinion tenga un usuario
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
