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
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('read_elimination')->after('create_projects')->default(false);
            $table->boolean('create_elimination')->after('read_elimination')->default(false);
            $table->boolean('edit_elimination')->after('create_elimination')->default(false);
            $table->boolean('delete_elimination')->after('edit_elimination')->default(false);
            $table->boolean('print_generate')->after('delete_elimination')->default(false);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'read_elimination',
                'create_elimination',
                'edit_elimination',
                'delete_elimination',
                'print_generate',
            ]);
        });
    }
};
