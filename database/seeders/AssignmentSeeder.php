<?php

namespace Database\Seeders;

use App\Models\Assignment;
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
                'id'=>1,
                'employee_id'=>1,
                'unit_id'=>1,
                'ndreq_st'=>'ND-123/2023',
                'no_st'=>'555',
                'date_st'=>'2023-10-13',
                'no_spd'=>'123',
                'date_spd'=>'2023-10-13',
                'departure_date'=>'2023-10-13',
                'return_date'=>'2023-10-14',
                'dipa_search'=>'Kantor',
                // 'tagging_status'=> '',
                //==================
                'implementation_tasks'=>'Undangan kegiatan rapat dan seterusnya',
                'business_trip_reason'=>'Menghadiri undangan rapat koordinasi',
                'destination_office'=>'Kanwil DJBC Jawa Tengah',
                'city_origin'=>'Kudus',
                'destination_city_1'=>'Semarang',
                'destination_city_2'=>'',
                'destination_city_3'=>'',
                'transportation_id'=>2,
                'signature'=>'KunawKunawi_196907171996031001i_'
        ];

        DB::transaction(function () use ($data) {
            DB::table('assignments')->insert($data);
            DB::table('backups')->insert($data);
        });
    }
}
