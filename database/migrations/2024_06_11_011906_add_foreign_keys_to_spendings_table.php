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
        Schema::table('spendings', function (Blueprint $table) {
            $table->foreign(['created_by'], 'fk_spending_user')->references(['id'])->on('users')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['deleted_by'], 'fk_spending_user_del')->references(['id'])->on('users')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['updated_by'], 'fk_spending_user_upd')->references(['id'])->on('users')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('spendings', function (Blueprint $table) {
            $table->dropForeign('fk_spending_user');
            $table->dropForeign('fk_spending_user_del');
            $table->dropForeign('fk_spending_user_upd');
        });
    }
};
