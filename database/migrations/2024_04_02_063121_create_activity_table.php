<?php

use App\Models\Activity;
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
        Schema::create('activity', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('docs_id')->index('fk_activity_docs');
            $table->text('name')->nullable();
            $table->bigInteger('startDate')->nullable();
            $table->bigInteger('endDate')->nullable();
            $table->bigInteger('created_at');
            $table->unsignedBigInteger('created_by')->nullable()->index('fk_user');
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
        Schema::dropIfExists('activity');
    }
};
