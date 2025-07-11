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
        Schema::table('vagas', function (Blueprint $table) {
            $table->string('local')->nullable();
            $table->double('salario')->nullable();
            $table->enum('modelo_contratacao', ['pj', 'clt'])->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vagas', function (Blueprint $table) {
            $table->dropColumn(['local', 'salario', 'modelo_contratacao']);
        });
    }
};
