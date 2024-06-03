<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class MilesLogSeeder extends Seeder
{
    public function run()
    {
        $clients = [25]; // IDs dos clientes
        $programs = ['All Accor', 'Átomos', 'Caixa', 'Coopera'];
        $status = [1,2,3];

        foreach ($clients as $client_id) {
            for ($i = 0; $i < 30; $i++) {
                DB::table('miles_log')->insert([
                    'client_id' => $client_id,
                    'miles_id' => rand(1, 4),
                    'program' => $programs[array_rand($programs)],
                    'quantity' => rand(10, 100),
                    'status' => $status[rand(0, 1)],
                    'created_at' => Carbon::today()->subDays(rand(0, 30)),
                    'updated_at' => Carbon::now()
                ]);
            }
        }
    }
}
