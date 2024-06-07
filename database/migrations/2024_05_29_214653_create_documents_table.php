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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('doc_number');
            $table->string('holder_name');
            $table->text('description');
            $table->string('box')->nullable();
            $table->string('qtpasta')->nullable();
            $table->string('cabinet')->nullable();
            $table->string('drawer')->nullable();
            $table->string('classification')->nullable();
            $table->string('version')->nullable();
            $table->string('situationAC')->nullable();
            $table->string('situationAI')->nullable();
            $table->date('expiration_date_A_C')->nullable();
            $table->date('expiration_date_A_I')->nullable();
            $table->string('file')->nullable();            
            $table->unsignedBigInteger('temporality_id');
            $table->unsignedBigInteger('project_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();
            
            $table->foreign('temporality_id')->references('id')->on('temporalitys');
            $table->foreign('project_id')->references('id')->on('projects');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documents');
    }
};
