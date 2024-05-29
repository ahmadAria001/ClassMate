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
        Schema::table('bansos', function (Blueprint $table) {
            $table->foreign(['request_by'], 'fk_bansos_civilian')->references(['id'])->on('users')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bansos', function (Blueprint $table) {
            $table->dropForeign('fk_bansos_civilian');
        });
    }
};
