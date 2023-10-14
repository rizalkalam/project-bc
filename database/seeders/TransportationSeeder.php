<?php

namespace Database\Seeders;

use App\Models\Transportation;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TransportationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Transportation::create([
            'transportation_name'=>'Kendaraan Umum'
        ]);

        Transportation::create([
            'transportation_name'=>'Kendaraan Dinas'
        ]);
    }
}
