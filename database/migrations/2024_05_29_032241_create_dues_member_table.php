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
        Schema::create('dues_member', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('dues')->index('dues_member_fk');
            $table->unsignedBigInteger('member')->index('dues_member_fk1');
            $table->bigInteger('created_at');
            $table->unsignedBigInteger('created_by')->nullable()->index('dues_member_fk2');
            $table->bigInteger('updated_at')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable()->index('dues_member_fk3');
            $table->bigInteger('deleted_at')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable()->index('dues_member_fk4');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dues_member');
    }
};
