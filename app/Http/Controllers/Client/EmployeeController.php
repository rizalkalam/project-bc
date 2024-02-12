<?php

namespace App\Http\Controllers\Client;

use App\Models\User;
use App\Models\Backup;
use App\Models\Employee;
use App\Models\Assignment;
use Illuminate\Http\Request;
use App\Imports\EmployeesImport;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\EmployeeListResource;
use App\Http\Resources\EmployeeDetailResource;

class EmployeeController extends Controller
{
    public function index()
    {
        $employeeRole = Role::where('name', 'biasa')->first();
        $officerRole = Role::where('name', 'ppk')->first();

        if ($employeeRole) {
            $user = User::whereHas('roles', function ($query) use ($employeeRole, $officerRole) {
                $query->where('id', $employeeRole->id);
                $query->orWhere('id', $officerRole->id);
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

            $data = EmployeeListResource::collection($user);

            return response()->json([
                'message'=>'success',
                'data'=>$data
            ]);
        }

        return response()->json([
            'message'=>'failed',
            'data'=>$data
        ]);
    }

    public function detail($id)
    {
        $employeeRole = Role::where('name', 'biasa')->first();
        $officerRole = Role::where('name', 'ppk')->first();

            if ($employeeRole) {
                $employee = User::whereHas('roles', function ($query) use ($employeeRole, $officerRole) {
                    $query->where('id', $employeeRole->id);
                    $query->orWhere('id', $officerRole->id);
                })
                ->where('id', $id)
                ->with('roles:name')
                ->select([
                    'users.id',
                    'users.name',
                    'users.emp_id',
                    'users.rank',
                    'users.gol_room',
                    'users.position',
                ])->get();
            }

        $data = EmployeeDetailResource::collection($employee);

        return response()->json([
            'message'=>'success',
            'data'=>$data
        ]);
    }

    public function add(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name'=>'required',
            'emp_id'=>'required|unique:users',
            'rank'=>'required',
            'gol_room'=>'required',
            'position'=>'required',
            // 'email'=>'nullable',
            'password'=>'nullable',
            // 'role'=>'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
                'data' => [],
            ], 400);
        }

        try {
            if (empty(explode(' ',trim($request->name))[1])) {
                $custom_password = explode(' ',trim($request->name))[0].substr($request->emp_id, 0, 5);
            } else {
                $custom_password = explode(' ',trim($request->name))[0].explode(' ',trim($request->name))[1].substr($request->emp_id, 0, 5);
            }
            
            $data = User::create([
                'name'=>$request->name,
                'emp_id'=>$request->emp_id,
                'rank'=>$request->rank,
                'gol_room'=>$request->gol_room,
                'position'=>$request->position,
                // 'email'=>$request->email,
                'password'=>Hash::make($custom_password)
            ]);

            $data->assignRole('biasa');

            return response()->json([
                'message' => 'Success',
                'data' => $data,
                // 'percakapan' => $percakapan
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
            'name'=>'required',
            'emp_id'=>'required|unique:users,id,' . $id,
            'rank'=>'required',
            'gol_room'=>'required',
            'position'=>'required',
            'role'=>'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
                'data' => [],
            ],400);
        }

        try {
            // $employee = User::where('id', $id)->first();
            
            $employeeRole = Role::where('name', 'biasa')->first();
            $officerRole = Role::where('name', 'ppk')->first();

            $countHO = User::where('position', '=', 'Kepala KPPBC TMC Kudus')->count();

            if ($countHO == 1) {
                return response()->json([
                    'message' => 'failed',
                    'errors' => 'Pejabat Kepala Kantor Tidak Tersedia!!',
                ], 420);
            }

            if ($employeeRole) {
                $employee = User::whereHas('roles', function ($query) use ($employeeRole, $officerRole) {
                    $query->where('id', $employeeRole->id);
                    $query->orWhere('id', $officerRole->id);
                })
                ->with('roles:name')
                ->where('id', $id)
                ->select([
                    'users.id',
                    'users.name',
                    'users.emp_id',
                    'users.rank',
                    'users.gol_room',
                    'users.position',
                ])->first();
                }

                $role = $employee->roles()->first()->name;
            
                $employee->update([
                    'name'=>$request->name,
                    'emp_id'=>$request->emp_id,
                    'rank'=>$request->rank,
                    'gol_room'=>$request->gol_room,
                    'position'=>$request->position
                ]);

                $newRole = Role::where('name', $role);
                if (!$newRole) {
                    return response()->json(['message' => 'Peran baru tidak ditemukan'], 404);
                }

                $rolesToAssign = [$newRole]; 
                $employee->syncRoles($request->role);

                //untuk menangani kasus ketika user_id milik penanda tangan diubah
                $assignmentByHeadOfc = Assignment::where('head_officer', $id)
                ->first();

                $assignmentByPPK = Assignment::where('ppk', $id)
                ->first();

                if (!empty($assignmentByHeadOfc)) {
                    if ($employee->position == 'Kepala KPPBC TMC Kudus') {
                        DB::table('assignments')->where('head_officer', $id)->update([
                            "head_officer_status" => "active",
                            "head_officer" => $employee->id,
                            "nama_pej" => $employee->name,
                            "plt" => "kosong",
                            "plh" => " "
                        ]);
                        DB::table('backups')->where('head_officer', $id)->update([
                            "head_officer_status" => "active",
                            "head_officer" => $employee->id,
                            "nama_pej" => $employee->name,
                            "plt" => "kosong",
                            "plh" => " "
                        ]);
                    } elseif ($assignmentByHeadOfc->plt == 'kosong') {
                        if ($employee->position == 'Kepala KPPBC TMC Kudus') {
                            DB::table('assignments')->where('head_officer', $id)->update([
                                "head_officer_status" => "active",
                                "head_officer" => $employee->id,
                                "nama_pej" => $employee->name,
                                "plt" => "kosong",
                                "plh" => " "
                            ]);
                            DB::table('backups')->where('head_officer', $id)->update([
                                "head_officer_status" => "active",
                                "head_officer" => $employee->id,
                                "nama_pej" => $employee->name,
                                "plt" => "kosong",
                                "plh" => " "
                            ]);
                        } elseif ($employee->position !== 'Kepala KPPBC TMC Kudus') {
                            $non_plh = User::where('position', '=', 'Kepala KPPBC TMC Kudus')
                            ->first();

                            if ($non_plh == null) {
                                DB::table('assignments')->where('head_officer', $id)->update([
                                    "head_officer_status" => "active",
                                    "head_officer" => $employee->id,
                                    "nama_pej" => $employee->name,
                                    "plt" => "plh",
                                    "plh" => "Plh"
                                ]);
                                DB::table('backups')->where('head_officer', $id)->update([
                                    "head_officer_status" => "active",
                                    "head_officer" => $employee->id,
                                    "nama_pej" => $employee->name,
                                    "plt" => "plh",
                                    "plh" => "Plh"
                                ]);

                                return response()->json([
                                    'message' => 'Success',
                                    'data' => $employee,
                                ], 200);
                            }

                            DB::table('assignments')->where('head_officer', $id)->update([
                                "head_officer_status" => "active",
                                "head_officer" => $non_plh->id,
                                "nama_pej" => $non_plh->name,
                                "plt" => "kosong",
                                "plh" => " "
                            ]);
                            DB::table('backups')->where('head_officer', $id)->update([
                                "head_officer_status" => "active",
                                "head_officer" => $non_plh->id,
                                "nama_pej" => $non_plh->name,
                                "plt" => "kosong",
                                "plh" => " "
                            ]);
                        }
                    } elseif ($assignmentByHeadOfc->plt !== 'plh') {
                        if ($employee->position == 'Kepala KPPBC TMC Kudus') {
                            DB::table('assignments')->where('head_officer', $id)->update([
                                "head_officer_status" => "active",
                                "head_officer" => $employee->id,
                                "nama_pej" => $employee->name,
                                "plt" => "kosong",
                                "plh" => " "
                            ]);
                            DB::table('backups')->where('head_officer', $id)->update([
                                "head_officer_status" => "active",
                                "head_officer" => $employee->id,
                                "nama_pej" => $employee->name,
                                "plt" => "kosong",
                                "plh" => " "
                            ]);
                        } elseif ($employee->position !== 'Kepala KPPBC TMC Kudus') {
                            DB::table('assignments')->where('head_officer', $id)->update([
                                "head_officer_status" => "active",
                                "head_officer" => $employee->id,
                                "nama_pej" => $employee->name,
                                "plt" => "plh",
                                "plh" => "Plh"
                            ]);
                            DB::table('backups')->where('head_officer', $id)->update([
                                "head_officer_status" => "active",
                                "head_officer" => $employee->id,
                                "nama_pej" => $employee->name,
                                "plt" => "plh",
                                "plh" => "Plh"
                            ]);
                        }
                    }
                } elseif (!empty($assignmentByPPK)) {
                    DB::table('assignments')->where('ppk', $id)->update([
                        "ppk" => $employee->id,
                        "nama_ppk" => $employee->name,
                        "nip_ppk" => $employee->emp_id
                    ]);
                    DB::table('backups')->where('ppk', $id)->update([
                        "ppk" => $employee->id,
                        "nama_ppk" => $employee->name,
                        "nip_ppk" => $employee->emp_id
                    ]);
                }

            return response()->json([
                'message' => 'Success',
                'data' => $employee,
            ], 200);

        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'message' => 'failed',
                'errors' => $th->getMessage()
            ]);
        }
    }

    public function delete($id){
        //pencarian data by id
        $employee = User::where('id', $id)->first();
        $employee_id = Assignment::where('user_id', $id)->first();
        $ho_id = Assignment::where('head_officer', $id)->first();
        $ppk_id = Assignment::where('ppk', $id)->first();

        //data yang akan terhapus by id
        $backup = Backup::where('user_id', $id)->first();
        $assignment = Assignment::where('user_id', $id)->first();

        if (empty($employee_id)) {
            Assignment::where('user_id', $id)->update([
                "employee_status" => "core",
                "availability_status" => "available"
            ]);

            Backup::where('user_id', $id)->update([
                "employee_status" => "core",
                "availability_status" => "available"
            ]);
        } else {
            if (!empty($ho_id)) {
                if (!empty($ppk_id)) {
                    if ($ho_id->plt == "kosong") {
                        $employee->delete();

                        //untuk head_office default
                        $head_office = User::where('position', '=', 'Kepala KPPBC TMC Kudus')
                        ->first();

                        if (empty($head_office)) {
                            return response()->json([
                                'success' => false,
                                'message' => 'Data head_office tidak ada',
                            ], 410);
                        }

                        Assignment::where('ppk', $id)->update([
                            "ppk_status" => "non-active",
                            "ppk" => 0
                        ]);
                        Backup::where('ppk', $id)->update([
                            "ppk_status" => "non-active",
                            "ppk" => 0
                        ]);
    
                        Assignment::where('head_officer', $id)->update([
                            "head_officer_status" => "active",
                            "head_officer" => $head_office->id,
                            "nama_pej" => $head_office->name,
                            "plt" => "kosong",
                            "plh" => " "
                        ]);
                        Backup::where('head_officer', $id)->update([
                            "head_officer_status" => "active",
                            "head_officer" => $head_office->id,
                            "nama_pej" => $head_office->name,
                            "plt" => "kosong",
                            "plh" => " "
                        ]);
        
                        Assignment::where('user_id', $id)->update([
                            "employee_status" => "blank",
                            "availability_status" => "not_yet",
                            "user_id" => 0
                        ]);
            
                        Backup::where('user_id', $id)->update([
                            "employee_status" => "blank",
                            "availability_status" => "not_yet",
                            "user_id" => 0
                        ]);
            
                        $assignment->delete();
            
                        return response()->json([
                            'success' => true,
                            'message' => 'Delete data success',
                        ]);
                    }
                    //akhir logic plt = kosong

                    Assignment::where('ppk', $id)->update([
                        "ppk_status" => "non-active",
                        "ppk" => 0
                    ]);
                    Backup::where('ppk', $id)->update([
                        "ppk_status" => "non-active",
                        "ppk" => 0
                    ]);

                    Assignment::where('head_officer', $id)->update([
                        "head_officer_status" => "non-active",
                        "head_officer" => 0
                    ]);
                    Backup::where('head_officer', $id)->update([
                        "head_officer_status" => "non-active",
                        "head_officer" => 0
                    ]);
    
                    Assignment::where('user_id', $id)->update([
                        "employee_status" => "blank",
                        "availability_status" => "not_yet",
                        "user_id" => 0
                    ]);
        
                    Backup::where('user_id', $id)->update([
                        "employee_status" => "blank",
                        "availability_status" => "not_yet",
                        "user_id" => 0
                    ]);
        
                    $employee->delete();
                    $assignment->delete();
        
                    return response()->json([
                        'success' => true,
                        'message' => 'Delete data success',
                    ]);
                }
                //akhir logic !empty($ppk_id)

                if ($ho_id->plt == "kosong") {
                    $employee->delete();

                    //untuk head_office default
                    $head_office = User::where('position', '=', 'Kepala KPPBC TMC Kudus')
                    ->first();

                    if (empty($head_office)) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Data head_office tidak ada',
                        ], 410);
                    }

                    Assignment::where('ppk', $id)->update([
                        "ppk_status" => "non-active",
                        "ppk" => 0
                    ]);
                    Backup::where('ppk', $id)->update([
                        "ppk_status" => "non-active",
                        "ppk" => 0
                    ]);

                    Assignment::where('head_officer', $id)->update([
                        "head_officer_status" => "active",
                        "head_officer" => $head_office->id,
                        "nama_pej" => $head_office->name,
                        "plt" => "kosong",
                        "plh" => " "
                    ]);
                    Backup::where('head_officer', $id)->update([
                        "head_officer_status" => "active",
                        "head_officer" => $head_office->id,
                        "nama_pej" => $head_office->name,
                        "plt" => "kosong",
                        "plh" => " "
                    ]);
    
                    Assignment::where('user_id', $id)->update([
                        "employee_status" => "blank",
                        "availability_status" => "not_yet",
                        "user_id" => 0
                    ]);
        
                    Backup::where('user_id', $id)->update([
                        "employee_status" => "blank",
                        "availability_status" => "not_yet",
                        "user_id" => 0
                    ]);
        
                    $assignment->delete();
        
                    return response()->json([
                        'success' => true,
                        'message' => 'Delete data success',
                    ]);
                }
                //akhir logic plt = kosong

                Assignment::where('head_officer', $id)->update([
                    "head_officer_status" => "non-active",
                    "head_officer" => 0
                ]);
                Backup::where('head_officer', $id)->update([
                    "head_officer_status" => "non-active",
                    "head_officer" => 0
                ]);

                Assignment::where('user_id', $id)->update([
                    "employee_status" => "blank",
                    "availability_status" => "not_yet",
                    "user_id" => 0
                ]);
    
                Backup::where('user_id', $id)->update([
                    "employee_status" => "blank",
                    "availability_status" => "not_yet",
                    "user_id" => 0
                ]);
    
                $employee->delete();
                $assignment->delete();
    
                return response()->json([
                    'success' => true,
                    'message' => 'Delete data success',
                ]);
            }
            //akhir logic !empty($ho_id)

            if (!empty($ppk_id)) {
                Assignment::where('ppk', $id)->update([
                    "ppk_status" => "non-active",
                    "ppk" => 0
                ]);
                Backup::where('ppk', $id)->update([
                    "ppk_status" => "non-active",
                    "ppk" => 0
                ]);

                Assignment::where('user_id', $id)->update([
                    "employee_status" => "blank",
                    "availability_status" => "not_yet",
                    "user_id" => 0
                ]);
    
                Backup::where('user_id', $id)->update([
                    "employee_status" => "blank",
                    "availability_status" => "not_yet",
                    "user_id" => 0
                ]);
    
                $employee->delete();
                $assignment->delete();
    
                return response()->json([
                    'success' => true,
                    'message' => 'Delete data success',
                ]);
            }
            //akhir logic !empty($ppk_id)

            Assignment::where('user_id', $id)->update([
                "employee_status" => "blank",
                "availability_status" => "not_yet",
                "user_id" => 0
            ]);

            Backup::where('user_id', $id)->update([
                "employee_status" => "blank",
                "availability_status" => "not_yet",
                "user_id" => 0
            ]);

            $employee->delete();
            $assignment->delete();

            return response()->json([
                'success' => true,
                'message' => 'Delete data success',
            ]);
        }
        //akhir logic empty($employee_id)

        if (empty($ho_id)) {
            DB::table('assignments')->where('head_officer', $id)->update([
                "head_officer_status" => "active",
            ]);
            DB::table('backups')->where('head_officer', $id)->update([
                "head_officer_status" => "active",
            ]);
        } else {
            if (!empty($ppk_id)) {
                if ($ho_id->plt == "kosong") {
                    $employee->delete();

                    //untuk head_office default
                    $head_office = User::where('position', '=', 'Kepala KPPBC TMC Kudus')
                    ->first();

                    if (empty($head_office)) {
                        return response()->json([
                            'success' => false,
                            'message' => 'Data head_office tidak ada',
                        ], 410);
                    }

                    Assignment::where('ppk', $id)->update([
                        "ppk_status" => "non-active",
                        "ppk" => 0
                    ]);
                    Backup::where('ppk', $id)->update([
                        "ppk_status" => "non-active",
                        "ppk" => 0
                    ]);

                    Assignment::where('head_officer', $id)->update([
                        "head_officer_status" => "active",
                        "head_officer" => $head_office->id,
                        "nama_pej" => $head_office->name,
                        "plt" => "kosong",
                        "plh" => " "
                    ]);
                    Backup::where('head_officer', $id)->update([
                        "head_officer_status" => "active",
                        "head_officer" => $head_office->id,
                        "nama_pej" => $head_office->name,
                        "plt" => "kosong",
                        "plh" => " "
                    ]);
    
                    Assignment::where('user_id', $id)->update([
                        "employee_status" => "blank",
                        "availability_status" => "not_yet",
                        "user_id" => 0
                    ]);
        
                    Backup::where('user_id', $id)->update([
                        "employee_status" => "blank",
                        "availability_status" => "not_yet",
                        "user_id" => 0
                    ]);
        
                    $assignment->delete();
        
                    return response()->json([
                        'success' => true,
                        'message' => 'Delete data success',
                    ]);
                }
                //akhir logic plt = kosong

                Assignment::where('ppk', $id)->update([
                    "ppk_status" => "non-active",
                    "ppk" => 0
                ]);
                Backup::where('ppk', $id)->update([
                    "ppk_status" => "non-active",
                    "ppk" => 0
                ]);
                
                DB::table('assignments')->where('head_officer', $id)->update([
                    "head_officer_status" => "non-active",
                    "head_officer" => 0
                ]);
                DB::table('backups')->where('head_officer', $id)->update([
                    "head_officer_status" => "non-active",
                    "head_officer" => 0
                ]);
    
                $employee->delete();
    
                return response()->json([
                    'success' => true,
                    'message' => 'Delete data success',
                ]);
            }
            //akhir logic !empty($ppk_id)

            if ($ho_id->plt == "kosong") {
                $employee->delete();

                //untuk head_office default
                $head_office = User::where('position', '=', 'Kepala KPPBC TMC Kudus')
                ->first();

                if (empty($head_office)) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Data head_office tidak ada',
                    ], 410);
                }

                Assignment::where('ppk', $id)->update([
                    "ppk_status" => "non-active",
                    "ppk" => 0
                ]);
                Backup::where('ppk', $id)->update([
                    "ppk_status" => "non-active",
                    "ppk" => 0
                ]);

                Assignment::where('head_officer', $id)->update([
                    "head_officer_status" => "active",
                    "head_officer" => $head_office->id,
                    "nama_pej" => $head_office->name,
                    "plt" => "kosong",
                    "plh" => " "
                ]);
                Backup::where('head_officer', $id)->update([
                    "head_officer_status" => "active",
                    "head_officer" => $head_office->id,
                    "nama_pej" => $head_office->name,
                    "plt" => "kosong",
                    "plh" => " "
                ]);

                Assignment::where('user_id', $id)->update([
                    "employee_status" => "blank",
                    "availability_status" => "not_yet",
                    "user_id" => 0
                ]);
    
                Backup::where('user_id', $id)->update([
                    "employee_status" => "blank",
                    "availability_status" => "not_yet",
                    "user_id" => 0
                ]);
    
                $assignment->delete();
    
                return response()->json([
                    'success' => true,
                    'message' => 'Delete data success',
                ]);
            }
            //akhir logic plt = kosong

            DB::table('assignments')->where('head_officer', $id)->update([
                "head_officer_status" => "non-active",
                "head_officer" => 0
            ]);
            DB::table('backups')->where('head_officer', $id)->update([
                "head_officer_status" => "non-active",
                "head_officer" => 0
            ]);

            $employee->delete();

            return response()->json([
                'success' => true,
                'message' => 'Delete data success',
            ]);
        }
        //akhir logic empty($ho_id)
        
        if (empty($ppk_id)) {
            DB::table('assignments')->where('ppk', $id)->update([
                "ppk_status" => "active",
            ]);
            DB::table('backups')->where('ppk', $id)->update([
                "ppk_status" => "active",
            ]);
        } else {
            DB::table('assignments')->where('ppk', $id)->update([
                "ppk_status" => "non-active",
                "ppk" => 0
            ]);
            DB::table('backups')->where('ppk', $id)->update([
                "ppk_status" => "non-active",
                "ppk" => 0
            ]);

            $employee->delete();

            return response()->json([
                'success' => true,
                'message' => 'Delete data success',
            ]);
        }
        //akhir logic empty($ppk_id)
        
        
    }

    public function importView(Request $request){
        return view('importFile');
    }

    public function import(Request $request){
        $validator = Validator::make($request->all(),[
            'file'=> 'required|mimes:xls,xlsx'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
                'data' => [],
            ], 400);
        }

        try {
            Excel::import(new EmployeesImport, $request->file('file')->store('file_data'));

            return response()->json([
                'success' => true,
                'message' => 'Berhasil mengimpor data.'
            ]);
        } catch (\Throwable $th) {
            //throw $th;
            return response()->json([
                'message' => 'failed',
                'errors' => $th->getMessage(),
            ], 400);
        }     
    }
}
