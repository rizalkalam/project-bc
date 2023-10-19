<?php

namespace App\Http\Controllers\Client;

use App\Models\User;
use App\Models\Employee;
use Illuminate\Http\Request;
use App\Imports\EmployeesImport;
use Spatie\Permission\Models\Role;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\EmployeeListResource;

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
            'role'=>'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
                'data' => [],
            ], 400);
        }

        try {
            $data = User::create([
                'name'=>$request->name,
                'emp_id'=>$request->emp_id,
                'rank'=>$request->rank,
                'gol_room'=>$request->gol_room,
                'position'=>$request->position,
                // 'email'=>$request->email,
                'password'=>$request->password
            ]);

            $data->assignRole($request->role);

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
            'emp_id'=>'required|unique:users,emp_id,' . $id,
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
            $employee = User::where('id', $id)->first();
            
            $employee->update([
                'name'=>$request->name,
                'emp_id'=>$request->emp_id,
                'rank'=>$request->rank,
                'gol_room'=>$request->gol_room,
                'position'=>$request->position
            ]);

            $employee->assignRole($request->role);

            return response()->json([
                'message' => 'Success',
                'data' => $employee
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
        $employee->delete();

        return response()->json([
            'success' => true,
            'message' => 'Delete data success',
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
