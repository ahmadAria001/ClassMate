<?php

use App\Models\Dues;
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
        Schema::create('dues', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->enum('typeDues', ['Security', 'TrashManagement', 'Event'])->nullable();
            $table->text('description')->nullable();
            $table->decimal('amt_dues', 13, 2)->nullable();
            $table->decimal('amt_fund', 13, 2)->nullable();
            $table->boolean('status')->nullable();
            $table->unsignedBigInteger('rt_id')->index('fk_dues_rt');
            $table->bigInteger('created_at');
            $table->unsignedBigInteger('created_by')->nullable()->index('fk_docs_user');
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
        Schema::dropIfExists('dues');
    }
};
