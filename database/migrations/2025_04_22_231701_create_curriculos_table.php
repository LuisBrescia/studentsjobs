<?php

use Illuminate\Database\Migrations\Migration;
use MongoDB\Laravel\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    protected $connection = 'mongodb';

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // if (!Schema::connection('mongodb')->hasCollection('curriculos')) {
        //     Schema::create('curriculos', function (Blueprint $table) {
        //         $table->id();
        //         $table->foreignId('estudante_id')->constrained('estudantes')->onDelete('cascade');
        //         $table->text('conteudo');
        //         $table->timestamps();
        //     });
        // }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('curriculos');
    }
};
