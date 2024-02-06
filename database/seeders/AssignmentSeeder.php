<?php

namespace Database\Seeders;

use App\Models\User;
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
        $ppk_id = 3;
        $head_officer = 5;
        $plh = 2;
        $data = [
            [
                'id'=>1,
                'identity_number'=>123,
                'user_id'=>2,
                'ppk'=> $ppk_id,
                'head_officer'=> $plh,
                // 'unit_id'=>1,
                'unit'=>'UMUM',
                'ndreq_st'=>'ND-123/2023',
                'no_st'=>'555',
                'nomor_st'=>'ST-555/KBC.1002/' . Carbon::now()->format('Y'),
                'date_st'=>'2023-10-13',
                'no_spd'=>'123',
                'date_spd'=>'2023-10-13',
                'departure_date'=>'2023-10-13',
                'return_date'=>'2023-10-13',
                'dipa_search'=>'Kantor',
                'plt'=>'plh',
                'plh'=>'Plh',
                'tagging_status'=> 'default',
                //==================
                'disbursement'=>'Kantor',
                'no_spyt'=>'',
                'implementation_tasks'=>'Undangan kegiatan rapat dan seterusnya',
                'business_trip_reason'=>'Menghadiri undangan rapat koordinasi',
                'destination_office'=>'Kanwil DJBC Jawa Tengah',
                'city_origin'=>'Kudus',
                'destination_city_1'=>'Semarang',
                'destination_city_2'=>'Sragen',
                'destination_city_3'=>null,
                'transportation'=>'Becak',
                'signature'=>'KunawKunawi_196907171996031001i_',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'employee_status' => 'core',
                'ppk_status' => 'active',
                'head_officer_status' => 'active',
                'availability_status' => 'available',

                //untuk mengatasi id user/pegawai sudah tidak tersedia
                'jabPeg' => User::find(2)->position,
                'pangkatPeg' => User::find(2)->rank,
                'golPeg' => User::find(2)->gol_room,
                'nip_peg' => User::find(2)->emp_id,
                'nip_ppk' => User::find($ppk_id)->emp_id,
                'employee' => User::find(2)->name,
                'nama_pej' => User::find($plh)->name,
                'nama_ppk' => User::find($ppk_id)->name,
            ], 
            
            [
                'id'=>2,
                'identity_number'=>234,
                'user_id'=>4,
                'ppk'=> $ppk_id,
                'head_officer'=> $head_officer,
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
                'plt'=>'kosong',
                'plh'=>' ',
                'tagging_status'=> 'default',
                //=================
                'disbursement'=>'Kantor',
                'no_spyt'=>'',
                'implementation_tasks'=>'Undangan kegiatan rapat dan seterusnya',
                'business_trip_reason'=>'Menghadiri undangan rapat koordinasi',
                'destination_office'=>'Kanwil DJBC Jawa Tengah',
                'city_origin'=>'Kudus',
                'destination_city_1'=>'Semarang',
                'destination_city_2'=>null,
                'destination_city_3'=>null,
                'transportation'=>'Becak',
                'signature'=>'KunawKunawi_196907171996031001i_',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'employee_status' => 'core',
                'ppk_status' => 'active',
                'head_officer_status' => 'active',
                'availability_status' => 'available',

                //untuk mengatasi id user/pegawai sudah tidak tersedia
                'jabPeg' => User::find(4)->position,
                'pangkatPeg' => User::find(4)->rank,
                'golPeg' => User::find(4)->gol_room,
                'nip_peg' => User::find(4)->emp_id,
                'nip_ppk' => User::find($ppk_id)->emp_id,
                'employee' => User::find(4)->name,
                'nama_pej' => User::find($head_officer)->name,
                'nama_ppk' => User::find($ppk_id)->name,
            ],
            [
                'id'=>3,
                'identity_number'=>234,
                'user_id'=>6,
                'ppk'=> $ppk_id,
                'head_officer'=> $head_officer,
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
                'plt'=>'kosong',
                'plh'=>' ',
                'tagging_status'=> 'default',
                //=================
                'disbursement'=>'Kantor',
                'no_spyt'=>'',
                'implementation_tasks'=>'Undangan kegiatan rapat dan seterusnya',
                'business_trip_reason'=>'Menghadiri undangan rapat koordinasi',
                'destination_office'=>'Kanwil DJBC Jawa Tengah',
                'city_origin'=>'Kudus',
                'destination_city_1'=>'Semarang',
                'destination_city_2'=>null,
                'destination_city_3'=>null,
                'transportation'=>'Becak',
                'signature'=>'KunawKunawi_196907171996031001i_',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'employee_status' => 'core',
                'ppk_status' => 'active',
                'head_officer_status' => 'active',
                'availability_status' => 'available',

                //untuk mengatasi id user/pegawai sudah tidak tersedia
                'jabPeg' => User::find(6)->position,
                'pangkatPeg' => User::find(6)->rank,
                'golPeg' => User::find(6)->gol_room,
                'nip_peg' => User::find(6)->emp_id,
                'nip_ppk' => User::find($ppk_id)->emp_id,
                'employee' => User::find(6)->name,
                'nama_pej' => User::find($head_officer)->name,
                'nama_ppk' => User::find($ppk_id)->name,
            ],
            [
                'id'=>4,
                'identity_number'=>333,
                'user_id'=>5,
                'ppk'=> $ppk_id,
                'head_officer'=> $head_officer,
                // 'unit_id'=>1,
                'unit'=>'UMUM',
                'ndreq_st'=>'ND-125/2025',
                'no_st'=>'555',
                'nomor_st'=>'ST-555/KBC.1002/' . Carbon::now()->format('Y'),
                'date_st'=>'2025-10-15',
                'no_spd'=>'125',
                'date_spd'=>'2025-10-15',
                'departure_date'=>'2025-10-15',
                'return_date'=>'2025-10-14',
                'dipa_search'=>'Kantor',
                'plt'=>'kosong',
                'plh'=>' ',
                'tagging_status'=> 'default',
                //=================
                'disbursement'=>'Kantor',
                'no_spyt'=>'',
                'implementation_tasks'=>'Undangan kegiatan rapat dan seterusnya',
                'business_trip_reason'=>'Menghadiri undangan rapat koordinasi',
                'destination_office'=>'Kanwil DJBC Jawa Tengah',
                'city_origin'=>'Kudus',
                'destination_city_1'=>'Semarang',
                'destination_city_2'=>null,
                'destination_city_5'=>null,
                'transportation'=>'Becak',
                'signature'=>'KunawKunawi_195907171995051001i_',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
                'employee_status' => 'core',
                'ppk_status' => 'active',
                'head_officer_status' => 'active',
                'availability_status' => 'available',

                //untuk mengatasi id user/pegawai sudah tidak tersedia
                'jabPeg' => User::find(5)->position,
                'pangkatPeg' => User::find(5)->rank,
                'golPeg' => User::find(5)->gol_room,
                'nip_peg' => User::find(5)->emp_id,
                'nip_ppk' => User::find($ppk_id)->emp_id,
                'employee' => User::find(5)->name,
                'nama_pej' => User::find($head_officer)->name,
                'nama_ppk' => User::find($ppk_id)->name,
            ],
        ];

        Assignment::insert($data);
    }
}
