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
        Schema::table('environtmentinfo', function (Blueprint $table) {
            $table->foreign(
                ['created_by'],
                'fk_created_env'
            )->references(['id'])->on('users')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('environmentinfo', function (Blueprint $table) {
            $table->dropForeign('fk_created_env');
        });
    }
};
