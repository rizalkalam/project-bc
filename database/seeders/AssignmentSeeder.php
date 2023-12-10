<?php

namespace Database\Seeders;

use App\Models\Assignment;
use Illuminate\Support\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AssignmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'id'=>1,
                'user_id'=>2,
                'ppk'=>3,
                'head_officer'=>5,
                // 'unit_id'=>1,
                'unit'=>'UMUM',
                'ndreq_st'=>'ND-123/2023',
                'no_st'=>'555',
                'nomor_st'=>'ST-555/KBC.1002/' . Carbon::now()->format('Y'),
                'date_st'=>'2023-10-13',
                'no_spd'=>'123',
                'date_spd'=>'2023-10-13',
                'departure_date'=>'2023-10-13',
                'return_date'=>'2023-10-14',
                'dipa_search'=>'Kantor',
                'plt'=>'plh',
                'plh'=>'plh',
                // 'tagging_status'=> '',
                //==================
                'disbursement'=>'Kantor',
                'no_spyt'=>'',
                'implementation_tasks'=>'Undangan kegiatan rapat dan seterusnya',
                'business_trip_reason'=>'Menghadiri undangan rapat koordinasi',
                'destination_office'=>'Kanwil DJBC Jawa Tengah',
                'city_origin'=>'Kudus',
                'destination_city_1'=>'Semarang',
                'destination_city_2'=>'',
                'destination_city_3'=>'',
                'transportation'=>'Becak',
                'signature'=>'KunawKunawi_196907171996031001i_',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ], 
            
            [
                'id'=>2,
                'user_id'=>2,
                'ppk'=>3,
                'head_officer'=>5,
                // 'unit_id'=>1,
                'unit'=>'UMUM',
                'ndreq_st'=>'ND-123/2023',
                'no_st'=>'555',
                'nomor_st'=>'ST-555/KBC.1002/' . Carbon::now()->format('Y'),
                'date_st'=>'2023-10-13',
                'no_spd'=>'123',
                'date_spd'=>'2023-10-13',
                'departure_date'=>'2023-10-13',
                'return_date'=>'2023-10-14',
                'dipa_search'=>'Kantor',
                'plt'=>'plh',
                'plh'=>'plh',
                // 'tagging_status'=> '',
                //=================
                'disbursement'=>'Kantor',
                'no_spyt'=>'',
                'implementation_tasks'=>'Undangan kegiatan rapat dan seterusnya',
                'business_trip_reason'=>'Menghadiri undangan rapat koordinasi',
                'destination_office'=>'Kanwil DJBC Jawa Tengah',
                'city_origin'=>'Kudus',
                'destination_city_1'=>'Semarang',
                'destination_city_2'=>'',
                'destination_city_3'=>'',
                'transportation'=>'Becak',
                'signature'=>'KunawKunawi_196907171996031001i_',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],
        ];

        DB::transaction(function () use ($data) {
            DB::table('assignments')->insert($data);
            DB::table('backups')->insert($data);
        });
    }
}
