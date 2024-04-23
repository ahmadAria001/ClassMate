<?php

use App\Models\Civilian;
use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('civilian', function (Blueprint $table) {
            $table->id();
            $table->char('nik', 16)->index();
            $table->string('fullName', 100)->nullable();
            $table->string('birthplace', 20)->nullable();
            $table->bigInteger('birthdate')->nullable();
            $table->enum('residentstatus', ['ContractResident', 'PermanentResident'])->nullable();
            $table->char('nkk', 16)->nullable();
            $table->boolean('isFamilyHead')->default(false);
            $table->unsignedBigInteger('rt_id')->index('fk_civilian_rt');
            $table->text('address')->nullable();
            $table->string('status', 100)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('religion', 100)->nullable();
            $table->string('job', 100)->nullable();
            $table->bigInteger('created_at');
            $table->unsignedBigInteger('created_by')->nullable();
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
        Schema::dropIfExists('civilian');
    }
};
