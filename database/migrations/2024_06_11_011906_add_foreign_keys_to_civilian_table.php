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
        Schema::table('civilian', function (Blueprint $table) {
            $table->foreign(['rt_id'], 'fk_civilian_rt')->references(['id'])->on('rt')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('civilian', function (Blueprint $table) {
            $table->dropForeign('fk_civilian_rt');
        });
    }
};
