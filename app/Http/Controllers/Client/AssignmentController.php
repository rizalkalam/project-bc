<?php

namespace App\Http\Controllers\Client;

use App\Models\User;
use App\Models\Backup;
use App\Models\Assignment;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class AssignmentController extends Controller
{
    public function index()
    {
        $data = Assignment::join('users', 'users.id', 'assignments.user_id')
        ->join('users as ppk', 'ppk.id', 'assignments.ppk')
        ->join('users as head_officer', 'head_officer.id', 'assignments.head_officer')
        ->select([
            'assignments.*', 
            'users.name as employee',
            'ppk.name as ppk',
            'head_officer.name as head_officer'
        ])
        ->get();

        return response()->json([
            'message'=>'success',
            'data'=>$data
        ]);
    }

    public function show_nonplh()
    {
        $head_office = User::where('position', '=', 'Kepala KPPBC TMC Kudus')
        ->select([
            'users.id',
            'users.name',
            'users.emp_id',
            'users.rank',
            'users.gol_room',
            'users.position',
        ])->get();

        return response()->json([
            'message'=>'success',
            'data'=>$head_office
        ]);
    }

    public function show_ppk()
    {
        $officerRole = Role::where('name', 'ppk')->first();

        if ($officerRole) {
            $user = User::whereHas('roles', function ($query) use ($officerRole) {
                $query->where('id', $officerRole->id);
            })
            ->with('roles:name')
            ->select([
                'users.id',
                'users.name',
                'users.emp_id',
                'users.rank',
                'users.gol_room',
                'users.position',
            ])->get();

            return response()->json([
                'message'=>'success',
                'data'=>$user
            ]);

        }

        return response()->json([
            'message'=>'failed',
            'data'=>$user
        ]);
    }

    public function data_backup()
    {
        $data = Backup::get();

        return response()->json([
            'message'=>'success',
            'data'=>$data
        ]);
    }

    public function show_assignment($id)
    {
        $data = Assignment::join('users as employee', 'employee.id', 'assignments.user_id')
        ->join('users as ppk', 'ppk.id', 'assignments.ppk')
        ->join('users as head_officer', 'head_officer.id', 'assignments.head_officer')
        ->where('assignments.id', $id)
        ->select([
            'assignments.*', 
            // 'assignments.user_id as id_pegawai',
            // 'users.name as employee',
            'employee.id as id_pegawai',
            'employee.name as employee',
            'ppk.id as id_ppk',
            'ppk.name as ppk',
            'head_officer.id as id_head_officer',
            'head_officer.name as head_officer'
        ])
        ->first();

        if (!empty($data)) {
            return response()->json([
                'message'=>'success',
                'data'=>$data
            ]);
        }
        return response()->json([
            'message'=> 'failed',
            'data'=> 'data tidak ada'
        ]);
    }

    public function create(Request $request)
    {
        $head_office = User::where('position', '=', 'Kepala KPPBC TMC Kudus')
        ->first();

        // $validator = Validator::make($request->all(),[
        //     'id_pegawai'=>'required',
        //     'id_ppk'=>'required',
        //     'penanda_tangan'=>'nullable', // hanya ketika ada plh
        //     'unit'=>'required',
        //     'no_ndpermohonan_st'=>'required',
        //     'no_st'=>'required',
        //     'nomor_st'=>'required',
        //     'tanggal_st'=>'required',
        //     'no_spd'=>'required',
        //     'tanggal_spd'=>'required',
        //     'tanggal_berangkat'=>'required',
        //     'tanggal_kembali'=>'required',
        //     'pencarian_dipa'=>'required',
        //     'tagging'=>'nullable',
        //     'plt'=>'required',
        //     'tagging'=>'required',

        //     'pencairan_dana'=>'required',
        //     'no_spyt'=>'required',
        //     'dasar_pelaksanaan_tugas'=>'required',
        //     'maksud_perjalanan_dinas'=>'required',
        //     'kantor_tujuan_tugas'=>'required',
        //     'kota_asal_tugas'=>'required',
        //     'kota_tujuan_tugas_1'=>'required',
        //     'kota_tujuan_tugas_2'=>'nullable',
        //     'kota_tujuan_tugas_3'=>'nullable',
        //     'transportasi'=>'required',
        //     'tandatangan'=>'required'
        // ]);

        // if ($validator->fails()) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => $validator->errors(),
        //         'data' => [],
        //         // 'tes' => $head_office->id
        //     ], 400);
        // }

        if ($request->tanggal_kembali >= $request->tanggal_berangkat) {
            if ($request->plt == 'plh') {
                $requestData = [
                    'user_id' => $request->id_pegawai,
                    'ppk' => $request->id_ppk,
                    'head_officer' => $request->penanda_tangan,
                    'unit' => $request->unit,
                    'ndreq_st' => $request->no_ndpermohonan_st,
                    'no_st' => $request->no_st,
                    'nomor_st' => 'ST-' . $request->no_st . '/KBC.1002/' . Carbon::now()->format('Y'),
                    'date_st' => $request->tanggal_st,
                    'no_spd' => $request->no_spd,
                    'date_spd' => $request->tanggal_spd,
                    'departure_date' => $request->tanggal_berangkat,
                    'return_date' => $request->tanggal_kembali,
                    'dipa_search' => $request->pencarian_dipa,
                    'tagging_status'=> $request->tagging,
                    'plt' => $request->plt,
                    'plh' => 'Plh',
                    'disbursement' => $request->pencairan_dana,
                    'no_spyt' => $request->no_spyt,
                    'implementation_tasks' => $request->dasar_pelaksanaan_tugas,
                    'business_trip_reason' => $request->maksud_perjalanan_dinas,
                    'destination_office' => $request->kantor_tujuan_tugas,
                    'city_origin' => $request->kota_asal_tugas,
                    'destination_city_1' => $request->kota_tujuan_tugas_1,
                    'destination_city_2' => $request->kota_tujuan_tugas_2,
                    'destination_city_3' => $request->kota_tujuan_tugas_3,
                    'destination_city_4' => $request->kota_tujuan_tugas_4,
                    'destination_city_5' => $request->kota_tujuan_tugas_5,
                    'transportation' => $request->transportasi,
                    'signature' => $request->tandatangan,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ];
            } else {
                $requestData = [
                    'user_id' => $request->id_pegawai,
                    'ppk' => $request->id_ppk,
                    'head_officer' => $head_office->id,
                    'unit' => $request->unit,
                    'ndreq_st' => $request->no_ndpermohonan_st,
                    'no_st' => $request->no_st,
                    'nomor_st' => 'ST-' . $request->no_st . '/KBC.1002/' . Carbon::now()->format('Y'),
                    'date_st' => $request->tanggal_st,
                    'no_spd' => $request->no_spd,
                    'date_spd' => $request->tanggal_spd,
                    'departure_date' => $request->tanggal_berangkat,
                    'return_date' => $request->tanggal_kembali,
                    'dipa_search' => $request->pencarian_dipa,
                    'tagging_status'=> $request->tagging,
                    'plt' => $request->plt,
                    'plh' => '',
                    'disbursement' => $request->pencairan_dana,
                    'no_spyt' => $request->no_spyt,
                    'implementation_tasks' => $request->dasar_pelaksanaan_tugas,
                    'business_trip_reason' => $request->maksud_perjalanan_dinas,
                    'destination_office' => $request->kantor_tujuan_tugas,
                    'city_origin' => $request->kota_asal_tugas,
                    'destination_city_1' => $request->kota_tujuan_tugas_1,
                    'destination_city_2' => $request->kota_tujuan_tugas_2,
                    'destination_city_3' => $request->kota_tujuan_tugas_3,
                    'destination_city_4' => $request->kota_tujuan_tugas_4,
                    'destination_city_5' => $request->kota_tujuan_tugas_5,
                    'transportation' => $request->transportasi,
                    'signature' => $request->tandatangan,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ];
            }
            try {
                DB::transaction(function () use ($requestData) {
                    DB::table('assignments')->insert($requestData);
                    DB::table('backups')->insert($requestData);
                });
    
                $data = Assignment::latest('created_at')->first();
    
                return response()->json([
                    'message' => 'Data Assignment success created',
                    'data' => $data
                ], 200);
            } catch (\Throwable $th) {
                //throw $th;
                return response()->json([
                    'message' => 'failed',
                    'errors' => $th->getMessage(),
                ], 400);
            }

        } else {
            return response()->json([
                'message' => 'failed',
                'errors' => 'tanggal kembali kurang dari tanggal berangkat',
            ], 400);
        }
        

        
    }

    public function edit(Request $request, $id)
    {
        $head_office = User::where('position', '=', 'Kepala KPPBC TMC Kudus')
        ->first();

        $validator = Validator::make($request->all(),[
            'id_pegawai'=>'required',
            'id_ppk'=>'required',
            // 'penanda_tangan'=>'required',
            'unit'=>'required',
            'no_ndpermohonan_st'=>'required',
            'no_st'=>'required',
            // 'nomor_st'=>'required',
            'tanggal_st'=>'required',
            'no_spd'=>'required',
            'tanggal_spd'=>'required',
            'tanggal_berangkat'=>'required',
            'tanggal_kembali'=>'required',
            'pencarian_dipa'=>'required',
            'tagging'=>'nullable',
            'plt'=>'required',
            // 'plh'=>'required',

            'pencairan_dana'=>'required',
            'no_spyt'=>'required',
            'dasar_pelaksanaan_tugas'=>'required',
            'maksud_perjalanan_dinas'=>'required',
            'kantor_tujuan_tugas'=>'required',
            'kota_asal_tugas'=>'required',
            'kota_tujuan_tugas_1'=>'required',
            'kota_tujuan_tugas_2'=>'nullable',
            'kota_tujuan_tugas_3'=>'nullable',
            'kota_tujuan_tugas_4'=>'nullable',
            'kota_tujuan_tugas_5'=>'nullable',
            'transportasi'=>'required',
            'tandatangan'=>'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
                'data' => [],
            ], 400);
        }

        if ($request->tanggal_kembali >= $request->tanggal_berangkat) {
            if ($request->plt == 'plh') {
                $requestData = [
                    'user_id' => $request->id_pegawai,
                    'ppk' => $request->id_ppk,
                    'head_officer' => $request->penanda_tangan,
                    'unit' => $request->unit,
                    'ndreq_st' => $request->no_ndpermohonan_st,
                    'no_st' => $request->no_st,
                    'nomor_st' => 'ST-' . $request->no_st . '/KBC.1002' .  Carbon::now()->format('Y'),
                    'date_st' => $request->tanggal_st,
                    'no_spd' => $request->no_spd,
                    'date_spd' => $request->tanggal_spd,
                    'departure_date' => $request->tanggal_berangkat,
                    'return_date' => $request->tanggal_kembali,
                    'dipa_search' => $request->pencarian_dipa,
                    'tagging_status'=> $request->tagging,
                    'plt' => $request->plt,
                    'plh' => 'Plh',
                    'disbursement' => $request->pencairan_dana,
                    'no_spyt' => $request->no_spyt,
                    'implementation_tasks' => $request->dasar_pelaksanaan_tugas,
                    'business_trip_reason' => $request->maksud_perjalanan_dinas,
                    'destination_office' => $request->kantor_tujuan_tugas,
                    'city_origin' => $request->kota_asal_tugas,
                    'destination_city_1' => $request->kota_tujuan_tugas_1 !== 'undefined' ? $request->kota_tujuan_tugas_1 : null,
                    'destination_city_2' => $request->kota_tujuan_tugas_2 !== 'undefined' ? $request->kota_tujuan_tugas_2 : null,
                    'destination_city_3' => $request->kota_tujuan_tugas_3 !== 'undefined' ? $request->kota_tujuan_tugas_3 : null,
                    'destination_city_4' => $request->kota_tujuan_tugas_4 !== 'undefined' ? $request->kota_tujuan_tugas_4 : null,
                    'destination_city_5' => $request->kota_tujuan_tugas_5 !== 'undefined' ? $request->kota_tujuan_tugas_5 : null,
                    'transportation' => $request->transportasi,
                    'signature' => $request->tandatangan,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ];
            } else {
                $requestData = [
                    'user_id' => $request->id_pegawai,
                    'ppk' => $request->id_ppk,
                    'head_officer' => $head_office->id,
                    'unit' => $request->unit,
                    'ndreq_st' => $request->no_ndpermohonan_st,
                    'no_st' => $request->no_st,
                    'nomor_st' => 'ST-' . $request->no_st . '/KBC.1002' .  Carbon::now()->format('Y'),
                    'date_st' => $request->tanggal_st,
                    'no_spd' => $request->no_spd,
                    'date_spd' => $request->tanggal_spd,
                    'departure_date' => $request->tanggal_berangkat,
                    'return_date' => $request->tanggal_kembali,
                    'dipa_search' => $request->pencarian_dipa,
                    'tagging_status'=> $request->tagging,
                    'plt' => $request->plt,
                    'plh' => 'Plh',
                    'disbursement' => $request->pencairan_dana,
                    'no_spyt' => $request->no_spyt,
                    'implementation_tasks' => $request->dasar_pelaksanaan_tugas,
                    'business_trip_reason' => $request->maksud_perjalanan_dinas,
                    'destination_office' => $request->kantor_tujuan_tugas,
                    'city_origin' => $request->kota_asal_tugas,
                    'destination_city_1' => $request->kota_tujuan_tugas_1,
                    'destination_city_2' => $request->kota_tujuan_tugas_2,
                    'destination_city_3' => $request->kota_tujuan_tugas_3,
                    'destination_city_4' => $request->kota_tujuan_tugas_4,
                    'destination_city_5' => $request->kota_tujuan_tugas_5,
                    'transportation' => $request->transportasi,
                    'signature' => $request->tandatangan,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ];
            }
    
            try {
                DB::transaction(function () use ($requestData, $id){
                    DB::table('assignments')->where('id', $id)->update($requestData);
                    DB::table('backups')->where('id', $id)->update($requestData);
                });
    
                $data = Assignment::where('id', $id)->first();
    
                return response()->json([
                    'message' => 'Data Assignment edited',
                    'data' => $data
                ], 200);
    
            } catch (\Throwable $th) {
                //throw $th;
                return response()->json([
                    'message' => 'failed',
                    'errors' => $th->getMessage(),
                ], 400);
            }
        } else {
            return response()->json([
                'message' => 'failed',
                'errors' => 'tanggal kembali kurang dari tanggal berangkat',
            ], 400);
        }
    }

    public function delete($id)
    {
        $assignment = Assignment::where('id', $id)->first();
        $assignment->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Data Assignment deleted',
        ]);
    }
}
