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
        Schema::table('doc_request', function (Blueprint $table) {
            $table->foreign(['request_by'], 'fk_request_by')->references(['id'])->on('civilian')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('doc_request', function (Blueprint $table) {
            $table->dropForeign('fk_request_by');
        });
    }
};
