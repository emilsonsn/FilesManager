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
        Schema::table('temporalitys', function (Blueprint $table) {
            $table->string('activity')->after('sub_function');
            $table->string('tipology')->after('activity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('temporalitys', function (Blueprint $table) {
            $table->dropColumn('activity');
            $table->dropColumn('tipology');
        });
    }
};
