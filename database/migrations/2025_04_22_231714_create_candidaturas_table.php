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
        Schema::create('candidaturas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('estudante_id')->constrained('estudantes')->onDelete('cascade');
            $table->foreignId('vaga_id')->constrained('vagas')->onDelete('cascade');
            $table->enum('status', ['pendente', 'aprovado', 'rejeitado'])->default('pendente');
            $table->timestamps();

            $table->unique(['estudante_id', 'vaga_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('candidaturas');
    }
};
