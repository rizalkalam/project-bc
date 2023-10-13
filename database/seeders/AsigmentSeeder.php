<?php

namespace Database\Seeders;

use App\Models\Assignment;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AsigmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Assignment::create([
            'employee_id'=>1,
            'unit'=>'10A',
            'ndreq_st'=>'ND-123',
            'no_st'=>'444',
            'date_st'=>'21/09/2023',
            'no_spd'=>'ND-123',
            'date_spd'=>'21/09/2023',
            'departure_date'=>'23/09/2023',
            'return_date'=>'24/09/2023',
            'dipa_search'=>'Kantor',
            'implementation_tasks'=>'Undangan kegiatan rapat dan seterusnya',
            'business_trip_reason'=>'Menghadiri undangan rapat koordinasi',
            'destionation_office'=>'Kanto DJBC Jawa Tengah',
            'city_origin'=>'Kudus',
            'destination_city_1'=>'Semarang',
            'transport'=>'Kendaraan Umum',
            'signature'=>'Tes123.png'
        ]);
    }
}
