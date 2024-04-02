<?php

use App\Models\EnvirontmentInfo;
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
        Schema::create('environtmentinfo', function (Blueprint $table) {
            $table->id();
            $table->text('env_condition');
            $table->text('desc');
            $table->text('general_facility');
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
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('environmentinfo');
    }
};
