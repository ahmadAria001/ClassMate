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
        Schema::create('complaintAttachment', function (Blueprint $table) {
            $table->id();
            $table->integer('complaint_id')->index('fk_attachment_complaint');
            $table->text('attachment_filename')->nullable();
            $table->text('attachment_dir')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('complaintAttachment');
    }
};
