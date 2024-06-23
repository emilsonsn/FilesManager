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
        Schema::table('document_collections', function (Blueprint $table) {
            $table->text('observations')->after('return_author')->nullable();
            $table->enum('type', ['loan', 'transfer'])->nullable()->after('observations');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('document_collections', function (Blueprint $table) {
            $table->dropColumn('observations');
            $table->dropColumn('type');
        });
    }
};
