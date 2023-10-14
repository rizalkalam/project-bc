<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Unit::create([
            'unit_name'=>'UMUM'
        ]);

        Unit::create([
            'unit_name'=>'P2'
        ]);

        Unit::create([
            'unit_name'=>'PERBEN'
        ]);

        Unit::create([
            'unit_name'=>'PKC'
        ]);

        Unit::create([
            'unit_name'=>'KIP'
        ]);
    }
}
