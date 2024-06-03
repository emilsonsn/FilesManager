<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProgramSeeder extends Seeder
{
    public function run()
    {
        $programs = [
            'All Accor',
            'Átomos',
            'Caixa',
            'Coopera',
            'Curtaí',
            'Esfera',
            'Iupp',
            'LatamPass',
            'Livelo',
            'Loop',
            'Nubank Rewards',
            'Qatar Privilege Club',
            'Safra Rewards',
            'Sicredi',
            'Sisprime',
            'Smiles',
            'TAP Miles&Go',
            'TudoAzul',
            'Unicred',
            // Adicione mais programas se necessário
        ];

        foreach ($programs as $program) {
            DB::table('programs')->insert([
                'name' => $program,
                'image' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
