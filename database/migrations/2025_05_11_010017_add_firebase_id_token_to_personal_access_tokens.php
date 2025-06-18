<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('personal_access_tokens', function (Blueprint $table) {
            $table->text('firebase_id_token')
                ->nullable()
                ->after('abilities')
                ->comment('ID-Token JWT do Firebase');
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('personal_access_tokens', function (Blueprint $table) {
            $table->dropColumn('firebase_id_token');
        });
    }
};
