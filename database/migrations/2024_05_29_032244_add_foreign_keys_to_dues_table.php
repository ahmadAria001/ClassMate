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
        Schema::table('dues', function (Blueprint $table) {
            $table->foreign(['rt_id'], 'fk_dues_rt')->references(['id'])->on('rt')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['created_by'], 'fk_dues_user_created')->references(['id'])->on('users')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['updated_by'], 'fk_dues_user_updated')->references(['id'])->on('users')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dues', function (Blueprint $table) {
            $table->dropForeign('fk_dues_rt');
            $table->dropForeign('fk_dues_user_created');
            $table->dropForeign('fk_dues_user_updated');
        });
    }
};
