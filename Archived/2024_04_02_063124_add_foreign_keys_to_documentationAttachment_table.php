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
        Schema::table('documentationAttachment', function (Blueprint $table) {
            $table->foreign(['documentation_id'], 'fk_attachment_documentation')->references(['id'])->on('documentation')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('documentationAttachment', function (Blueprint $table) {
            $table->dropForeign('fk_attachment_documentation');
        });
    }
};
