<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ClientsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for ($i = 0; $i < 10; $i++) {
            DB::table('clients')->insert([
                'name' => Str::random(10),
                'email' => Str::random(10) . '@example.com',
                'phone' => '123-456-7890',
                'instagram' => '@' . Str::random(10),
                'accession_date' => Carbon::now(),
                'renew_date' => Carbon::now(),
                'nearby_airports' => Str::random(10),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}
