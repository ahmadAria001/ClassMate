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
        Schema::create('spendings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->text('category');
            $table->text('desc');
            $table->decimal('amount', 13);
            $table->bigInteger('created_at');
            $table->unsignedBigInteger('created_by')->nullable()->index('fk_spending_user');
            $table->bigInteger('updated_at')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable()->index('fk_spending_user_upd');
            $table->bigInteger('deleted_at')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable()->index('fk_spending_user_del');
        });

        Schema::table('spendings', function (Blueprint $table) {
            $table->foreign(['created_by'], 'fk_spending_user')->references(['id'])->on('users')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['updated_by'], 'fk_spending_user_upd')->references(['id'])->on('users')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['deleted_by'], 'fk_spending_user_del')->references(['id'])->on('users')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('spendings');
    }
};
