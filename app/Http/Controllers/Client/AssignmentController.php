<?php

namespace App\Http\Controllers\Client;

use App\Models\User;
use App\Models\Backup;
use App\Models\Assignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class AssignmentController extends Controller
{
    public function index()
    {
        $data = Assignment::join('users', 'users.id', 'assignments.user_id')
        ->join('users as ppk', 'ppk.id', 'assignments.input_name')
        ->select([
            'assignments.*', 
            'users.name as employee',
            'ppk.name as ppk'
        ])
        ->get();

        return response()->json([
            'message'=>'success',
            'data'=>$data
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
        $data = Backup::join('users', 'users.id', 'backups.user_id')
        ->join('units', 'units.id', 'backups.unit_id')
        ->join('transportations', 'transportations.id', 'backups.transportation_id')
        
        ->get();

        return response()->json([
            'message'=>'success',
            'data'=>$data
        ]);
    }

    public function create(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'id_pegawai'=>'required',
            'id_penginput'=>'required',
            'unit'=>'required',
            'kepala_kantor'=>'required',
            'no_ndpermohonan_st'=>'required',
            'no_st'=>'required',
            'nomor_st'=>'required',
            'tanggal_st'=>'required',
            'no_spd'=>'required',
            'tanggal_spd'=>'required',
            'tanggal_berangkat'=>'required',
            'tanggal_kembali'=>'required',
            'pencarian_dipa'=>'required',
            'tagging'=>'nullable',
            'plt'=>'required',

            'pencairan_dana'=>'required',
            'no_spyt'=>'required',
            'dasar_pelaksanaan_tugas'=>'required',
            'maksud_perjalanan_dinas'=>'required',
            'kantor_tujuan_tugas'=>'required',
            'kota_asal_tugas'=>'required',
            'kota_tujuan_tugas_1'=>'required',
            'kota_tujuan_tugas_2'=>'nullable',
            'kota_tujuan_tugas_3'=>'nullable',
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

        $requestData = [
            'user_id' => $request->id_pegawai,
            'input_name' => $request->id_penginput,
            'unit' => $request->unit,
            'kk_name' => $request->kepala_kantor,
            'ndreq_st' => $request->no_ndpermohonan_st,
            'no_st' => $request->no_st,
            'nomor_st' => $request->nomor_st,
            'date_st' => $request->tanggal_st,
            'no_spd' => $request->no_spd,
            'date_spd' => $request->tanggal_spd,
            'departure_date' => $request->tanggal_berangkat,
            'return_date' => $request->tanggal_kembali,
            'dipa_search' => $request->pencarian_dipa,
            'tagging_status'=> $request->tagging,
            'disbursement' => $request->pencairan_dana,
            'no_spyt' => $request->no_spyt,
            'implementation_tasks' => $request->dasar_pelaksanaan_tugas,
            'business_trip_reason' => $request->maksud_perjalanan_dinas,
            'destination_office' => $request->kantor_tujuan_tugas,
            'city_origin' => $request->kota_asal_tugas,
            'destination_city_1' => $request->kota_tujuan_tugas_1,
            'destination_city_2' => $request->kota_tujuan_tugas_2,
            'destination_city_3' => $request->kota_tujuan_tugas_3,
            'transportation' => $request->transportasi,
            'signature' => $request->tandatangan,
        ];

        try {
            DB::transaction(function () use ($requestData) {
                DB::table('assignments')->insert($requestData);
                DB::table('backups')->insert($requestData);
            });

            $data = Assignment::latest()->get();

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
    }

    public function edit(Request $request, $id)
    {
        $validator = Validator::make($request->all(),[
            'id_pegawai'=>'required',
            'id_penginput'=>'required',
            'unit'=>'required',
            'kepala_kantor'=>'requird',
            'no_ndpermohonan_st'=>'required',
            'no_st'=>'required',
            'nomor_st'=>'required',
            'tanggal_st'=>'required',
            'no_spd'=>'required',
            'tanggal_spd'=>'required',
            'tanggal_berangkat'=>'required',
            'tanggal_kembali'=>'required',
            'pencarian_dipa'=>'required',
            'tagging'=>'nullable',
            'plt'=>'required',

            'pencairan_dana'=>'required',
            'no_spyt'=>'required',
            'dasar_pelaksanaan_tugas'=>'required',
            'maksud_perjalanan_dinas'=>'required',
            'kantor_tujuan_tugas'=>'required',
            'kota_asal_tugas'=>'required',
            'kota_tujuan_tugas_1'=>'required',
            'kota_tujuan_tugas_2'=>'nullable',
            'kota_tujuan_tugas_3'=>'nullable',
            'id_transpostasi'=>'required',
            'tandatangan'=>'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
                'data' => [],
            ], 400);
        }

        $requestData = [
            'user_id' => $request->id_pegawai,
            'input_name' => $request->id_penginput,
            'unit' => $request->unit,
            'kk_name' => $request->kepala_kantor,
            'ndreq_st' => $request->no_ndpermohonan_st,
            'no_st' => $request->no_st,
            'nomor_st' => $request->nomor_st,
            'date_st' => $request->tanggal_st,
            'no_spd' => $request->no_spd,
            'date_spd' => $request->tanggal_spd,
            'departure_date' => $request->tanggal_berangkat,
            'return_date' => $request->tanggal_kembali,
            'dipa_search' => $request->pencarian_dipa,
            'tagging_status'=> $request->tagging,
            'plt' => $request->plt,
            'disbursement' => $requestpencairan_dana,
            'no_spyt' => $request->no_spyt,
            'implementation_tasks' => $request->dasar_pelaksanaan_tugas,
            'business_trip_reason' => $request->maksud_perjalanan_dinas,
            'destination_office' => $request->kantor_tujuan_tugas,
            'city_origin' => $request->kota_asal_tugas,
            'destination_city_1' => $request->kota_tujuan_tugas_1,
            'destination_city_2' => $request->kota_tujuan_tugas_2,
            'destination_city_3' => $request->kota_tujuan_tugas_3,
            'transportation_id' => $request->id_transpostasi,
            'signature' => $request->tandatangan,
        ];

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
