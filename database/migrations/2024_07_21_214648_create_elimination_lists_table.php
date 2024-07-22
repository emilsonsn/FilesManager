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
        Schema::create('elimination_lists', function (Blueprint $table) {
            $table->id();
            $table->string('organ')->nullable();
            $table->string('unit')->nullable();
            $table->string('responsible_selection')->nullable();
            $table->string('responsible_unit')->nullable();
            $table->string('president')->nullable();
            $table->text('observations')->nullable();
            $table->enum('status', ['em_construcao', 'em_avaliacao', 'concluida'])->nullable();
            $table->unsignedBigInteger('project_id');
            $table->timestamps();

            $table->foreign('project_id')->references('id')->on('projects');
        });

        Schema::create('elimination_list_files', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('path');
            $table->unsignedBigInteger('elimination_list_id');
            $table->timestamps();

            $table->foreign('elimination_list_id')->references('id')->on('elimination_lists')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('elimination_list_files', function (Blueprint $table) {
            $table->dropForeign(['elimination_list_id']);
        });

        Schema::table('elimination_lists', function (Blueprint $table) {
            $table->dropForeign(['project_id']);
        });

        Schema::dropIfExists('elimination_lists');
        Schema::dropIfExists('elimination_list_files');
    }
};
