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
        Schema::create('temporalitys', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('area');
            $table->string('function');
            $table->string('sub_function');
            $table->integer('current_custody_period');
            $table->integer('intermediate_custody_period');
            $table->string('final_destination');         
            $table->unsignedBigInteger('project_id');   
            $table->timestamps();

            $table->foreign('project_id')->references('id')->on('projects')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('temporalitys');
    }
};
