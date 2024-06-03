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
        Schema::table('documents', function (Blueprint $table) {
            $table->date('initial_date')->after('situationAC');
            $table->date('archive_date')->after('initial_date');
            $table->text('observations')->after('expiration_date_A_I');
            $table->dropColumn('file');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->dropColumn('initial_date');
            $table->dropColumn('archive_date');
            $table->dropColumn('observations');
            $table->string('file')->nullable()->after("expiration_date_A_I");
        });
    }
};
