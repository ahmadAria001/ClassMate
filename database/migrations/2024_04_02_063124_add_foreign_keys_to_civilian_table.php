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
        Schema::table('civilian', function (Blueprint $table) {
            $table->foreign(['family_id'], 'fk_civilian_family')->references(['id'])->on('family')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('civilian', function (Blueprint $table) {
            $table->dropForeign('fk_civilian_family');
        });
    }
};
