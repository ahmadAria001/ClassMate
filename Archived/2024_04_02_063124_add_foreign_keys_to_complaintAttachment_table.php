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
        Schema::table('complaintAttachment', function (Blueprint $table) {
            $table->foreign(['complaint_id'], 'fk_attachment_complaint')->references(['id'])->on('complaint')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('complaintAttachment', function (Blueprint $table) {
            $table->dropForeign('fk_attachment_complaint');
        });
    }
};
