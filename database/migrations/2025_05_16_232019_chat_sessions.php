<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('chat_sessions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('vaga_id');
            $table->unsignedBigInteger('empresa_id');
            $table->unsignedBigInteger('estudante_id');
            $table->timestamps();

            $table->foreign('vaga_id')
                ->references('id')->on('vagas')
                ->onDelete('cascade');

            $table->foreign('empresa_id')
                ->references('id')->on('usuarios')
                ->onDelete('cascade');

            $table->foreign('estudante_id')
                ->references('id')->on('usuarios')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
                Schema::dropIfExists('chat_sessions');

    }
};
