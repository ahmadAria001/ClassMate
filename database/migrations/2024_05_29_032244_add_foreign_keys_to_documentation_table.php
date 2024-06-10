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
        Schema::table('documentation', function (Blueprint $table) {
            $table->foreign(['docs_id'], 'fk_documentantion_docs')->references(['id'])->on('docs')->onUpdate('restrict')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documentation', function (Blueprint $table) {
            $table->dropForeign('fk_documentantion_docs');
        });
    }
};
