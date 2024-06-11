<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('duesPaymentLog', function (Blueprint $table) {
            $table->foreign(['dues_id'], 'fk_dues_payment')->references(['id'])->on('dues')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['paid_by'], 'fk_payer_payment')->references(['id'])->on('users')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('duesPaymentLog', function (Blueprint $table) {
            $table->dropForeign('fk_dues_payment');
            $table->dropForeign('fk_payer_payment');
        });
    }
};
