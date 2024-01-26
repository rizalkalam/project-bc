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
        ->orderBy('assignments.identity_number')
        ->select([
            'assignments.*', 
            'users.name as employee',
            'ppk.name as ppk',
            'head_officer.name as head_officer',
            'assignments.identity_number as nomor_identitas'
        ])
        ->get();

        $data->makeHidden('identity_number');

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
        $data = Backup::select([
            'backups.*',
            'backups.identity_number as nomor_identitas'
        ])->get();

        $data->makeHidden('identity_number');

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
            'employee.id as id_pegawai',
            'employee.name as employee',
            'assignments.identity_number as nomor_identitas',
            'ppk.id as id_ppk',
            'ppk.name as ppk',
            'head_officer.id as id_head_officer',
            'head_officer.name as head_officer'
        ])
        ->first();

        $data->makeHidden('identity_number');

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

        if ($request->tanggal_kembali >= $request->tanggal_berangkat) {
            if ($request->plt == 'plh') {
                $requestData = [
                    'user_id' => $request->id_pegawai,
                    'identity_number' =>$request->nomor_identitas,
                    'ppk' => $request->id_ppk,
                    'head_officer' => $request->penanda_tangan,
                    'unit' => $request->unit,
                    'ndreq_st' => $request->no_ndpermohonan_st,
                    'no_st' => $request->no_st !== null ? $request->no_st : " ",
                    'nomor_st' => 'ST-' . $request->no_st . '/KBC.1002/' . Carbon::now()->format('Y') !== 'ST-/KBC.1002/' . Carbon::now()->format('Y') ? $request->nomor_st : '',
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
                    'destination_city_1' => $request->kota_tujuan_tugas_1 !== null ? $request->kota_tujuan_tugas_1 : " ",
                    'destination_city_2' => $request->kota_tujuan_tugas_2 !== null ? $request->kota_tujuan_tugas_2 : " ",
                    'destination_city_3' => $request->kota_tujuan_tugas_3 !== null ? $request->kota_tujuan_tugas_3 : " ",
                    'destination_city_4' => $request->kota_tujuan_tugas_4 !== null ? $request->kota_tujuan_tugas_4 : " ",
                    'destination_city_5' => $request->kota_tujuan_tugas_5 !== null ? $request->kota_tujuan_tugas_5 : " ",
                    'transportation' => $request->transportasi,
                    'signature' => $request->tandatangan,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),

                    //untuk mengatasi id user/pegawai sudah tidak tersedia
                    'jabPeg' => User::find($request->get('user_id'))->position,
                    'pangkatPeg' => User::find($request->get('user_id'))->rank,
                    'golPeg' => User::find($request->get('user_id'))->gol_room,
                    'nip_peg' => User::find($request->get('user_id'))->emp_id,
                    'nip_ppk' => User::find($request->get('ppk'))->emp_id,
                    'employee' => User::find($request->get('user_id'))->name,
                    'nama_pej' => User::find($request->get('head_officer'))->name,
                ];
            } else {
                $requestData = [
                    'user_id' => $request->id_pegawai,
                    'identity_number' =>$request->nomor_identitas,
                    'ppk' => $request->id_ppk,
                    'head_officer' => $head_office->id,
                    'unit' => $request->unit,
                    'ndreq_st' => $request->no_ndpermohonan_st,
                    'no_st' => $request->no_st,
                    'nomor_st' => 'ST-' . $request->no_st . '/KBC.1002/' . Carbon::now()->format('Y') !== 'ST-/KBC.1002/2023' ? $request->nomor_st : '',
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
                    'destination_city_1' => $request->kota_tujuan_tugas_1 !== null ? $request->kota_tujuan_tugas_1 : " ",
                    'destination_city_2' => $request->kota_tujuan_tugas_2 !== null ? $request->kota_tujuan_tugas_2 : " ",
                    'destination_city_3' => $request->kota_tujuan_tugas_3 !== null ? $request->kota_tujuan_tugas_3 : " ",
                    'destination_city_4' => $request->kota_tujuan_tugas_4 !== null ? $request->kota_tujuan_tugas_4 : " ",
                    'destination_city_5' => $request->kota_tujuan_tugas_5 !== null ? $request->kota_tujuan_tugas_5 : " ",
                    'transportation' => $request->transportasi,
                    'signature' => $request->tandatangan,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),

                    //untuk mengatasi id user/pegawai sudah tidak tersedia
                    'jabPeg' => User::find($request->get('user_id'))->position,
                    'pangkatPeg' => User::find($request->get('user_id'))->rank,
                    'golPeg' => User::find($request->get('user_id'))->gol_room,
                    'nip_peg' => User::find($request->get('user_id'))->emp_id,
                    'nip_ppk' => User::find($request->get('ppk'))->emp_id,
                    'employee' => User::find($request->get('user_id'))->name,
                    'nama_pej' => User::find($request->get('head_officer'))->name,
                ];
            }
            try {
                $identityNumber = Backup::where('identity_number', $request->nomor_identitas)->value('identity_number');
                $userId = Backup::where('user_id', $request->id_pegawai)->value('user_id');

                $existingData = Backup::where('identity_number', $request->nomor_identitas)
                ->where('user_id', $request->id_pegawai)
                ->first();
                
                if ($existingData) {
                    return response()->json([
                        'message' => 'Conflict',
                        // 'data' => $requestData,
                    ], 500);
                } else {
                    $data = Assignment::create($requestData);
    
                    $data_assignment = Assignment::join('users', 'users.id', 'assignments.user_id')
                    ->join('users as ppk', 'ppk.id', 'assignments.ppk')
                    ->join('users as head_officer', 'head_officer.id', 'assignments.head_officer')
                    ->latest('created_at')
                    ->select([
                        'assignments.*',
                        'users.name as employee',
                        'users.emp_id as nip_peg',
                        'users.rank as pangkatPeg',
                        'users.gol_room as golPeg',
                        'users.position as jabPeg',
                        'ppk.name as ppk',
                        'ppk.emp_id as nip_ppk',
                        'head_officer.name as namaPej',
                        'head_officer.emp_id as nipPej'
                    ])
                    ->get();

                    $reqBackup = $requestData;
                    $reqBackup['employee'] = $data_assignment->first()->employee;
                    $reqBackup['ppk'] = $data_assignment->first()->ppk;
                    $reqBackup['jabPeg'] = $data_assignment->first()->jabPeg;
                    $reqBackup['pangkatPeg'] = $data_assignment->first()->pangkatPeg;
                    $reqBackup['golPeg'] = $data_assignment->first()->golPeg;
                    $reqBackup['nip_peg'] = $data_assignment->first()->nip_peg;
                    $reqBackup['nip_ppk'] = $data_assignment->first()->nip_ppk;
                    $reqBackup['nama_pej'] = $data_assignment->first()->namaPej;

                    $backup = Backup::create($reqBackup);

                    return response()->json([
                        'message' => 'Data Assignment success created',
                        'data' => $requestData,    
                    ], 200);
                }
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
            'id_ppk'=>'required',
            'nomor_identitas'=>'required',
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
                $requestST = [
                    'no_st' => $request->no_st !== null ? $request->no_st : " ",
                    'nomor_st' => $request->no_st ? 'ST-' . $request->no_st . '/KBC.1002/' . Carbon::now()->format('Y') : null,
                    'date_st' => $request->tanggal_st,
                    'date_spd' => $request->tanggal_spd,
                ];
                $requestData = [
                    'identity_number' => $request->nomor_identitas,
                    'ppk' => $request->id_ppk,
                    'head_officer' => $request->penanda_tangan,
                    'unit' => $request->unit,
                    'ndreq_st' => $request->no_ndpermohonan_st,

                    'no_spd' => $request->no_spd,
                    'departure_date' => $request->tanggal_berangkat,
                    'return_date' => $request->tanggal_kembali,
                    'dipa_search' => $request->pencarian_dipa,
                    'tagging_status'=> $request->tagging_status,
                    'plt' => $request->plt,
                    'plh' => 'Plh',
                    'disbursement' => $request->pencairan_dana,
                    'no_spyt' => $request->no_spyt,
                    'implementation_tasks' => $request->dasar_pelaksanaan_tugas,
                    'business_trip_reason' => $request->maksud_perjalanan_dinas,
                    'destination_office' => $request->kantor_tujuan_tugas,
                    'city_origin' => $request->kota_asal_tugas,
                    'destination_city_1' => $request->kota_tujuan_tugas_1 !== 'undefined' ? $request->kota_tujuan_tugas_1 : " ",
                    'destination_city_2' => isset($request->kota_tujuan_tugas_2) && $request->kota_tujuan_tugas_2 !== 'null'
                    ? $request->kota_tujuan_tugas_2 : null,                
                    'destination_city_3' => isset($request->kota_tujuan_tugas_3) && $request->kota_tujuan_tugas_3 !== 'null'
                    ? $request->kota_tujuan_tugas_3 : null,
                    'destination_city_4' => isset($request->kota_tujuan_tugas_4) && $request->kota_tujuan_tugas_4 !== 'null'
                    ? $request->kota_tujuan_tugas_4 : null,
                    'destination_city_5' => isset($request->kota_tujuan_tugas_5) && $request->kota_tujuan_tugas_5 !== 'null'
                    ? $request->kota_tujuan_tugas_5 : null,
                    'transportation' => $request->transportasi,
                    'signature' => $request->tandatangan,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),

                    //untuk mengatasi id user/pegawai sudah tidak tersedia
                    'jabPeg' => User::find($request->get('user_id'))->position,
                    'pangkatPeg' => User::find($request->get('user_id'))->rank,
                    'golPeg' => User::find($request->get('user_id'))->gol_room,
                    'nip_peg' => User::find($request->get('user_id'))->emp_id,
                    'nip_ppk' => User::find($request->get('ppk'))->emp_id,
                    'employee' => User::find($request->get('user_id'))->name,
                    'nama_pej' => User::find($request->get('head_officer'))->name,
                ];
            } else {
                $requestST = [
                    'no_st' => $request->no_st !== null ? $request->no_st : " ",
                    'nomor_st' => $request->no_st ? 'ST-' . $request->no_st . '/KBC.1002/' . Carbon::now()->format('Y') : null,
                    'date_st' => $request->tanggal_st,
                    'date_spd' => $request->tanggal_spd,
                ];
                $requestData = [
                    'identity_number' => $request->nomor_identitas,
                    'ppk' => $request->id_ppk,
                    'head_officer' => $head_office->id,
                    'unit' => $request->unit,
                    'ndreq_st' => $request->no_ndpermohonan_st,
                    
                    'no_spd' => $request->no_spd,
                    'departure_date' => $request->tanggal_berangkat,
                    'return_date' => $request->tanggal_kembali,
                    'dipa_search' => $request->pencarian_dipa,
                    'tagging_status'=> $request->tagging_status,
                    'plt' => $request->plt,
                    'plh' => ' ',
                    'disbursement' => $request->pencairan_dana,
                    'no_spyt' => $request->no_spyt,
                    'implementation_tasks' => $request->dasar_pelaksanaan_tugas,
                    'business_trip_reason' => $request->maksud_perjalanan_dinas,
                    'destination_office' => $request->kantor_tujuan_tugas,
                    'city_origin' => $request->kota_asal_tugas,
                    'destination_city_1' => $request->kota_tujuan_tugas_1 !== 'undefined' ? $request->kota_tujuan_tugas_1 : " ",
                    'destination_city_2' => isset($request->kota_tujuan_tugas_2) && $request->kota_tujuan_tugas_2 !== 'null'
                    ? $request->kota_tujuan_tugas_2 : null,                
                    'destination_city_3' => isset($request->kota_tujuan_tugas_3) && $request->kota_tujuan_tugas_3 !== 'null'
                    ? $request->kota_tujuan_tugas_3 : null,
                    'destination_city_4' => isset($request->kota_tujuan_tugas_4) && $request->kota_tujuan_tugas_4 !== 'null'
                    ? $request->kota_tujuan_tugas_4 : null,
                    'destination_city_5' => isset($request->kota_tujuan_tugas_5) && $request->kota_tujuan_tugas_5 !== 'null'
                    ? $request->kota_tujuan_tugas_5 : null,
                    'transportation' => $request->transportasi,
                    'signature' => $request->tandatangan,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),

                    //untuk mengatasi id user/pegawai sudah tidak tersedia
                    'jabPeg' => User::find($request->get('user_id'))->position,
                    'pangkatPeg' => User::find($request->get('user_id'))->rank,
                    'golPeg' => User::find($request->get('user_id'))->gol_room,
                    'nip_peg' => User::find($request->get('user_id'))->emp_id,
                    'nip_ppk' => User::find($request->get('ppk'))->emp_id,
                    'employee' => User::find($request->get('user_id'))->name,
                    'nama_pej' => User::find($request->get('head_officer'))->name,
                ];
            }
    
            try {
                $data_identity = Assignment::where('id', $id)->first();
                $data = Assignment::where('assignments.id', $id)->update($requestData);
    
                $data_assignment = Assignment::join('users', 'users.id', 'assignments.user_id')
                ->join('users as ppk', 'ppk.id', 'assignments.ppk')
                ->join('users as head_officer', 'head_officer.id', 'assignments.head_officer')
                ->where('assignments.id', $id)
                ->select([
                    'assignments.*',
                    'users.name as employee',
                    'users.emp_id as nip_peg',
                    'users.rank as pangkatPeg',
                    'users.gol_room as golPeg',
                    'users.position as jabPeg',
                    'ppk.name as ppk',
                    'ppk.emp_id as nip_ppk',
                    'head_officer.name as namaPej',
                    'head_officer.emp_id as nipPej'
                ])
                ->get();

                $reqBackup = $requestData;
                $reqBackup['employee'] = $data_assignment->first()->employee;
                $reqBackup['ppk'] = $data_assignment->first()->ppk;
                $reqBackup['jabPeg'] = $data_assignment->first()->jabPeg;
                $reqBackup['pangkatPeg'] = $data_assignment->first()->pangkatPeg;
                $reqBackup['golPeg'] = $data_assignment->first()->golPeg;
                $reqBackup['nip_peg'] = $data_assignment->first()->nip_peg;
                $reqBackup['nip_ppk'] = $data_assignment->first()->nip_ppk;
                $reqBackup['nama_pej'] = $data_assignment->first()->namaPej;

                $backup = Backup::where('backups.id', $id)->update($reqBackup);
                
                DB::transaction(function () use ($requestST, $data_identity){
                    DB::table('assignments')->where('identity_number', $data_identity->identity_number)->update($requestST);
                    DB::table('backups')->where('identity_number', $data_identity->identity_number)->update($requestST);
                });
    
                return response()->json([
                    'message' => 'Data Assignment edited',
                    'data' => $reqBackup
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

    public function recore( $id)
    {
        $backup = Backup::where('id', $id)->get();

        $dataBackup = [
            'user_id' => $backup->first()->user_id,
            'identity_number' =>$backup->first()->identity_number,
            'ppk' => $backup->first()->ppk,
            'head_officer' => $backup->first()->head_officer,
            'unit' => $backup->first()->unit,
            'ndreq_st' => $backup->first()->ndreq_st,
            'no_st' => $backup->first()->no_st,
            'nomor_st' => 'ST-' . $backup->first()->no_st . '/KBC.1002/' . Carbon::now()->format('Y') !== 'ST-/KBC.1002/2023' ? $backup->first()->nomor_st : '',
            'date_st' => $backup->first()->date_st,
            'no_spd' => $backup->first()->no_spd,
            'date_spd' => $backup->first()->date_spd,
            'departure_date' => $backup->first()->departure_date,
            'return_date' => $backup->first()->return_date,
            'dipa_search' => $backup->first()->dipa_search,
            'tagging_status'=> $backup->first()->tagging_status,
            'plt' => $backup->first()->plt,
            'plh' => $backup->first()->plh,
            'disbursement' => $backup->first()->disbursement,
            'no_spyt' => $backup->first()->no_spyt,
            'implementation_tasks' => $backup->first()->implementation_tasks,
            'business_trip_reason' => $backup->first()->business_trip_reason,
            'destination_office' => $backup->first()->kantor_tujuan_tugas,
            'city_origin' => $backup->first()->kota_asal_tugas,
            'destination_city_1' => $backup->first()->destination_city_1 !== null ? $backup->first()->destination_city_1 : " ",
            'destination_city_2' => $backup->first()->destination_city_2 !== null ? $backup->first()->destination_city_2 : " ",
            'destination_city_3' => $backup->first()->destination_city_3 !== null ? $backup->first()->destination_city_3 : " ",
            'destination_city_4' => $backup->first()->destination_city_4 !== null ? $backup->first()->destination_city_4 : " ",
            'destination_city_5' => $backup->first()->destination_city_5 !== null ? $backup->first()->destination_city_5 : " ",
            'transportation' => $backup->first()->transportation,
            'signature' => $backup->first()->signature,
            'created_at' => $backup->first()->created_at,
            'updated_at' => $backup->first()->updated_at,

            //untuk mengatasi id user/pegawai sudah tidak tersedia
            'jabPeg' => $backup->first()->jabPeg,
            'pangkatPeg' => $backup->first()->pangkatPeg,
            'golPeg' => $backup->first()->golPeg,
            'nip_peg' => $backup->first()->nip_peg,
            'nip_ppk' => $backup->first()->nip_ppk,
            'employee' => $backup->first()->employee,
            'nama_pej' => $backup->first()->nama_pej,
        ];

        try {
            $existingData = Assignment::where('identity_number', $backup->first()->identity_number)
                ->where('user_id', $backup->first()->user_id)
            ->first();
            
            if ($existingData) {
                return response()->json([
                    'message' => 'Conflict',
                    // 'data' => $requestData,
                ], 500);
            } else {
                $data = Assignment::create($dataBackup);

                return response()->json([
                    'message' => 'Data Backup success recore',
                    'data' => $dataBackup,    
                ], 200);
            }
            
            
        } catch (\Throwable $th) {
            return response()->json([
                'message' => 'failed',
                'errors' => $th->getMessage(),
            ], 400);
        }
    }
}
