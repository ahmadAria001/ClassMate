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
        Schema::create('documentationAttachment', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('documentation_id')->index('fk_attachment_documentation');
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
        Schema::dropIfExists('documentationAttachment');
    }
};
