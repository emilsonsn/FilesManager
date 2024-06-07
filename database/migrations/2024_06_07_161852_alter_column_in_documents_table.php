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
            $table->text('observations')->nullable()->change();
            $table->string('holder_name')->nullable()->change();
            $table->text('description')->nullable()->change();
            $table->string('doc_number')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('documents', function (Blueprint $table) {
            $table->text('observations')->change();
            $table->string('holder_name')->change();
            $table->text('description')->change();
            $table->string('doc_number')->change();
        });
    }
};
