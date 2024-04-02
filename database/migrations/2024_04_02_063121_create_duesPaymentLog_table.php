<?php

use App\Models\DuesPaymentLog;
use Carbon\Carbon;
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
        Schema::create('duesPaymentLog', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('paid_by')->index('fk_payer_payment');
            $table->unsignedBigInteger('dues_id')->index('fk_dues_payment');
            $table->bigInteger('amount_paid');
            $table->bigInteger('exchange');
            $table->bigInteger('debt');
            $table->bigInteger('created_at');
            $table->unsignedBigInteger('created_by')->nullable()->index('fk_docs_user');
            $table->bigInteger('updated_at')->nullable();
            $table->unsignedBigInteger('updated_by')->index('fk_docs_user_upd')->nullable();
            $table->bigInteger('deleted_at')->nullable();
            $table->unsignedBigInteger('deleted_by')->index('fk_docs_user_del')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('duesPaymentLog');
    }
};
