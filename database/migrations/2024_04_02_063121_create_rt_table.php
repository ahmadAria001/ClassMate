<?php

use App\Models\RT;
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
        Schema::create('rt', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('leader_id')->index('fk_rt_user')->nullable();
            $table->bigInteger('created_at');
        $table->unsignedBigInteger('created_by')->nullable()->index('fk_user')->nullable()->nullable();
            $table->tinyInteger('number')->unique();
            $table->bigInteger('updated_at')->nullable();
            $table->unsignedBigInteger('updated_by')->index('fk_user_upd')->nullable();
            $table->bigInteger('deleted_at')->nullable();
            $table->unsignedBigInteger('deleted_by')->index('fk_user_del')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('rt');
    }
};
