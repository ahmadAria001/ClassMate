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
        Schema::table('dues_member', function (Blueprint $table) {
            $table->foreign(['dues'], 'dues_member_FK')->references(['id'])->on('dues')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['member'], 'dues_member_FK1')->references(['id'])->on('civilian')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['created_by'], 'dues_member_FK2')->references(['id'])->on('users')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['updated_by'], 'dues_member_FK3')->references(['id'])->on('users')->onUpdate('restrict')->onDelete('restrict');
            $table->foreign(['deleted_by'], 'dues_member_FK4')->references(['id'])->on('users')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dues_member', function (Blueprint $table) {
            $table->dropForeign('dues_member_FK');
            $table->dropForeign('dues_member_FK1');
            $table->dropForeign('dues_member_FK2');
            $table->dropForeign('dues_member_FK3');
            $table->dropForeign('dues_member_FK4');
        });
    }
};
