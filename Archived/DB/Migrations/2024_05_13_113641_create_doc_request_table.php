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
            $table->id();
            $table->enum('requestStatus', ['Resolved', 'Declined', 'Open'])->default('Open');
            $table->unsignedBigInteger('request_by')->index('fk_request_by');
            $table->unsignedBigInteger('docs_id')->index('fk_request_docs');
            $table->bigInteger('created_at');
            $table->unsignedBigInteger('created_by')->index('fk_user')->nullable();
            $table->bigInteger('updated_at')->nullable();
            $table->unsignedBigInteger('updated_by')->index('fk_user_upd')->nullable();
            $table->bigInteger('deleted_at')->nullable();
            $table->unsignedBigInteger('deleted_by')->index('fk_user_del')->nullable();
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
