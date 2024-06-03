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
            $table->boolean('is_admin')->default(false)->after('password');

            $table->boolean('read_doc')->default(false)->after('is_admin');
            $table->boolean('create_doc')->default(false)->after('read_doc');
            $table->boolean('edit_doc')->default(false)->after('create_doc');
            $table->boolean('delete_doc')->default(false)->after('edit_doc');
            
            $table->boolean('read_temporality')->default(false)->after('delete_doc');
            $table->boolean('create_temporality')->default(false)->after('read_temporality');
            $table->boolean('edit_temporality')->default(false)->after('create_temporality');
            $table->boolean('delete_temporality')->default(false)->after('edit_temporality');

            $table->boolean('read_collection')->default(false)->after('delete_temporality');
            $table->boolean('create_collection')->default(false)->after('read_collection');
            $table->boolean('edit_collection')->default(false)->after('create_collection');
            $table->boolean('delete_collection')->default(false)->after('edit_collection');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'is_admin',
                'read_doc',
                'create_doc',
                'edit_doc',
                'delete_doc',
                'read_temporality',
                'create_temporality',
                'edit_temporality',
                'delete_temporality',
                'read_collection',
                'create_collection',
                'edit_collection',
                'delete_collection',
            ]);
        });
    }
};
