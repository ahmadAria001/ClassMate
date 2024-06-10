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
        Schema::table('duesPaymentLog', function (Blueprint $table) {
            $table->foreign(['dues_member'], 'fk_dues_member')->references(['id'])->on('dues_member')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('duesPaymentLog', function (Blueprint $table) {
            $table->dropForeign('fk_dues_member');
        });
    }
};
