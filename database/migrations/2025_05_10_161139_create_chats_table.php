<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatsTable extends Migration
{
    public function up()
    {
        Schema::create('chats', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('producto_id');
            $table->unsignedBigInteger('usuario1_id');
            $table->unsignedBigInteger('usuario2_id');
            $table->timestamps();

            // Claves foráneas
            $table->foreign('producto_id')->references('id')->on('productos')->onDelete('cascade');
            $table->foreign('usuario1_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('usuario2_id')->references('id')->on('users')->onDelete('cascade');

            // Evitar duplicados: mismo producto y mismos usuarios (en cualquier orden)
            $table->unique(['producto_id', 'usuario1_id', 'usuario2_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('chats');
    }
}