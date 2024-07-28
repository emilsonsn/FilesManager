<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MasterPermission extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::where('email', 'master@master')->first();

        $user->update([
            'is_admin' => true,
            'read_doc' => true,
            'create_doc' => true,
            'edit_doc' => true,
            'delete_doc' => true,
            'read_temporality' => true,
            'create_temporality' => true,
            'edit_temporality' => true,
            'delete_temporality' => true,
            'read_collection' => true,
            'create_collection' => true,
            'edit_collection' => true,
            'delete_collection' => true,
            'create_projects' => true,
            'edit_elimination' => true,
            'read_elimination' => true,
            'create_elimination' => true,
            'delete_elimination' => true,
            'print_generate' => true,
        ]);
    }
}
