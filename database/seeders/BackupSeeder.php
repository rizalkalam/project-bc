<?php

namespace Database\Seeders;

use App\Models\Backup;
use App\Models\Assignment;
use Illuminate\Support\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class BackupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $assignment1 = Assignment::join('users', 'users.id', 'assignments.user_id')
        ->join('users as ppk', 'ppk.id', 'assignments.ppk')
        ->join('users as head_officer', 'head_officer.id', 'assignments.head_officer')
        ->where('assignments.id', 1)->select([
            'assignments.*', 
            'users.name as employee',
            'users.rank as pangkatPeg',
            'users.gol_room as golPeg',
            'users.position as jabPeg',
            'users.emp_id as nip_peg',
            'ppk.name as ppk',
            'ppk.emp_id as nip_ppk',
            'head_officer.name as head_officer',
            'assignments.identity_number as nomor_identitas'
        ])
        ->get();

        $assignment2 = Assignment::join('users', 'users.id', 'assignments.user_id')
        ->join('users as ppk', 'ppk.id', 'assignments.ppk')
        ->join('users as head_officer', 'head_officer.id', 'assignments.head_officer')
        ->where('assignments.id', 2)->select([
            'assignments.*', 
            'users.name as employee',
            'users.rank as pangkatPeg',
            'users.gol_room as golPeg',
            'users.position as jabPeg',
            'users.emp_id as nip_peg',
            'ppk.name as ppk',
            'ppk.emp_id as nip_ppk',
            'head_officer.name as head_officer',
            'assignments.identity_number as nomor_identitas'
        ])
        ->get();

        $assignment3 = Assignment::join('users', 'users.id', 'assignments.user_id')
        ->join('users as ppk', 'ppk.id', 'assignments.ppk')
        ->join('users as head_officer', 'head_officer.id', 'assignments.head_officer')
        ->where('assignments.id', 3)->select([
            'assignments.*', 
            'users.name as employee',
            'users.rank as pangkatPeg',
            'users.gol_room as golPeg',
            'users.position as jabPeg',
            'users.emp_id as nip_peg',
            'ppk.name as ppk',
            'ppk.emp_id as nip_ppk',
            'head_officer.name as head_officer',
            'assignments.identity_number as nomor_identitas'
        ])
        ->get();
        
        $data = [
            [
                'id'=>1,
                'identity_number'=>123,
                'user_id'=>2,
                'ppk'=>$assignment1->first()->ppk,
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
                'return_date'=>'2023-10-13',
                'dipa_search'=>'Kantor',
                'plt'=>'plh',
                'plh'=>'plh',
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

                //tambahan assignment
                'pangkatPeg' => $assignment1->first()->pangkatPeg,
                'golPeg' => $assignment1->first()->golPeg,
                'jabPeg' => $assignment1->first()->jabPeg,
                'nip_ppk' => $assignment1->first()->nip_ppk,
                'nip_peg' => $assignment1->first()->nip_peg,
                'employee' => $assignment1->first()->employee
            ], 
            
            [
                'id'=>2,
                'identity_number'=>234,
                'user_id'=>2,
                'ppk'=>$assignment2->first()->ppk,
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

                //tambahan assignment
                'pangkatPeg' => $assignment2->first()->pangkatPeg,
                'golPeg' => $assignment2->first()->golPeg,
                'jabPeg' => $assignment2->first()->jabPeg,
                'nip_ppk' => $assignment2->first()->nip_ppk,
                'nip_peg' => $assignment2->first()->nip_peg,
                'employee' => $assignment2->first()->employee
            ],
            [
                'id'=>3,
                'identity_number'=>234,
                'user_id'=>5,
                'ppk'=>$assignment3->first()->ppk,
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

                //tambahan assignment
                'pangkatPeg' => $assignment3->first()->pangkatPeg,
                'golPeg' => $assignment3->first()->golPeg,
                'jabPeg' => $assignment3->first()->jabPeg,
                'nip_ppk' => $assignment3->first()->nip_ppk,
                'nip_peg' => $assignment3->first()->nip_peg,
                'employee' => $assignment3->first()->employee
            ],
        ];

        Backup::insert($data);
    }
}
