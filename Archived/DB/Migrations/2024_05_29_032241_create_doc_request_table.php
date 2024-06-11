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
        Schema::create('doc_request', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->enum('requestStatus', ['Resolved', 'Declined', 'Open'])->default('Open');
            $table->unsignedBigInteger('request_by')->index('fk_request_by');
            $table->tinyInteger('rt_stat')->default(2);
            $table->unsignedBigInteger('responsed_by_rt')->nullable();
            $table->tinyInteger('rw_stat')->default(2);
            $table->unsignedBigInteger('responsed_by_rw')->nullable();
            $table->unsignedBigInteger('docs_id')->index('fk_request_docs');
            $table->bigInteger('created_at');
            $table->unsignedBigInteger('created_by')->nullable()->index('fk_user');
            $table->bigInteger('updated_at')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable()->index('fk_user_upd');
            $table->bigInteger('deleted_at')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable()->index('fk_user_del');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doc_request');
    }
};
