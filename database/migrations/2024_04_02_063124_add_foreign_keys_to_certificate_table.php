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
        Schema::table('certificate', function (Blueprint $table) {
            $table->foreign(['request_by'], 'fk_docs_user')->references(['id'])->on('users')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('certificate', function (Blueprint $table) {
            $table->dropForeign('fk_certificate_created');
            $table->dropForeign('fk_certificate_updated');
        });
    }
};
