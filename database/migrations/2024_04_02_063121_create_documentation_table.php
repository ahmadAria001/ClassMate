<?php

use App\Models\Documentation;
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
        Schema::create('documentation', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('docs_id')->index('fk_documentantion_docs');
            $table->enum('contentType', ['Complaint', 'Dues', 'Event', 'Administration'])->nullable();
            $table->text('contentAttachment')->nullable();
            $table->text('contentDesc')->nullable();
            $table->bigInteger('created_at');
            $table->unsignedBigInteger('created_by')->index('fk_docs_user')->nullable();
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
        Schema::dropIfExists('documentation');
    }
};
