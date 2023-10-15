<?php

namespace App\Http\Controllers\Client;

use App\Models\Backup;
use App\Models\Assignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class AssignmentController extends Controller
{
    public function index()
    {
        $data = Assignment::join('employees', 'employees.id', 'assignments.employee_id')
        ->join('units', 'units.id', 'assignments.unit_id')
        ->join('transportations', 'transportations.id', 'assignments.transportation_id')
        ->select([
            'assignments.id',
            'units.unit_name',
            'assignments.implementation_tasks',
            'assignments.no_st',
            'assignments.date_st',
            'employees.name',
            'assignments.business_trip_reason'
        ])
        ->get();

        return response()->json([
            'message'=>'success',
            'data'=>$data
        ]);
    }

    public function data_backup()
    {
        $data = Backup::join('employees', 'employees.id', 'backups.employee_id')
        ->join('units', 'units.id', 'backups.unit_id')
        ->join('transportations', 'transportations.id', 'backups.transportation_id')
        ->select([
            'backups.id',
            'units.unit_name',
            'backups.implementation_tasks',
            'backups.no_st',
            'backups.date_st',
            'employees.name',
            'backups.business_trip_reason'
        ])
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
            'id_unit'=>'required',
            'no_ndpermohonan_st'=>'required',
            'no_st'=>'required',
            'tanggal_st'=>'required',
            'no_spd'=>'required',
            'tanggal_spd'=>'required',
            'tanggal_berangkat'=>'required',
            'tanggal_kembali'=>'required',
            'pencarian_dipa'=>'required',
            'tagging'=>'nullable',

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
            'employee_id' => $request->id_pegawai,
            'unit_id' => $request->id_unit,
            'ndreq_st' => $request->no_ndpermohonan_st,
            'no_st' => $request->no_st,
            'date_st' => $request->tanggal_st,
            'no_spd' => $request->no_spd,
            'date_spd' => $request->tanggal_spd,
            'departure_date' => $request->tanggal_berangkat,
            'return_date' => $request->tanggal_kembali,
            'dipa_search' => $request->pencarian_dipa,
            'tagging_status'=> $request->tagging,
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
            'id_unit'=>'required',
            'no_ndpermohonan_st'=>'required',
            'no_st'=>'required',
            'tanggal_st'=>'required',
            'no_spd'=>'required',
            'tanggal_spd'=>'required',
            'tanggal_berangkat'=>'required',
            'tanggal_kembali'=>'required',
            'pencarian_dipa'=>'required',
            'tagging'=>'nullable',

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
            'employee_id' => $request->id_pegawai,
            'unit_id' => $request->id_unit,
            'ndreq_st' => $request->no_ndpermohonan_st,
            'no_st' => $request->no_st,
            'date_st' => $request->tanggal_st,
            'no_spd' => $request->no_spd,
            'date_spd' => $request->tanggal_spd,
            'departure_date' => $request->tanggal_berangkat,
            'return_date' => $request->tanggal_kembali,
            'dipa_search' => $request->pencarian_dipa,
            'tagging_status'=> $request->tagging,
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
