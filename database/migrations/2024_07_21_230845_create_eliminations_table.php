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
        Schema::create('eliminations', function (Blueprint $table) {
            $table->id();
            $table->string('doc_number')->nullable();
            $table->string('holder_name')->nullable();
            $table->text('description')->nullable();
            $table->string('box')->nullable();
            $table->integer('qtpasta')->nullable();
            $table->string('cabinet')->nullable();
            $table->string('drawer')->nullable();
            $table->string('classification')->nullable();
            $table->string('version')->nullable();
            $table->string('situationAC')->nullable();
            $table->string('situationAI')->nullable();
            $table->date('initial_date')->nullable();
            $table->date('archive_date')->nullable();
            $table->date('expiration_date_A_C')->nullable();
            $table->date('expiration_date_A_I')->nullable();
            $table->text('observations')->nullable();
            $table->string('tags')->nullable();
            $table->unsignedBigInteger('temporality_id');
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('elimination_list_id');
            $table->timestamps();

            $table->foreign('temporality_id')->references('id')->on('temporalitys')->onDelete('cascade');
            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('elimination_list_id')->references('id')->on('elimination_lists')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('eliminations');
    }
};
