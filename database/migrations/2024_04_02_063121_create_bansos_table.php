<?php

use App\Models\FinancialAssistance;
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
        Schema::create('bansos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('request_by')->index('fk_bansos_civilian');
            $table->text('tanggungan')->nullable();
            $table->text('alasan')->nullable();
            $table->enum('status', ['Open', 'Approved', 'Rejected'])->default('Open');
            $table->text('attachment')->nullable();
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
        Schema::dropIfExists('bansos');
    }
};
