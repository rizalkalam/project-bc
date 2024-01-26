<?php

namespace App\Http\Controllers\Client;

use App\Models\User;
use App\Models\Backup;
use App\Models\Employee;
use App\Models\Assignment;
use Illuminate\Http\Request;
use App\Imports\EmployeesImport;
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
        $employee = User::where('id', $id)->first();
        $assignment = Assignment::where('user_id', $id)->delete();

        $backup = Backup::where('user_id', $id)->update([
            "employee_status" => "blank"
        ]);

        $employee->delete();

        return response()->json([
            'success' => true,
            'message' => 'Delete data success',
            // 'assignment' => $assignment
            // 'data' => $percakapan
        ]);
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
