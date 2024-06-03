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
        Schema::create('document_collections', function (Blueprint $table) {
            $table->id();
            $table->date('loan_date');
            $table->string('loan_author');
            $table->string('loan_receiver');
            $table->string('return_date')->nullable();
            $table->string('return_author')->nullable();
            $table->string('receiver_author')->nullable();
            $table->unsignedBigInteger('document_id');
            $table->unsignedBigInteger('user_id');
            $table->timestamps();

            $table->foreign('document_id')->references('id')->on('documents')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_collections');
    }
};
