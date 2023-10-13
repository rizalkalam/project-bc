<?php

namespace App\Http\Controllers\Client;

use App\Models\Employee;
use Illuminate\Http\Request;
use App\Imports\EmployeesImport;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Storage;

class EmployeeController extends Controller
{
    public function index()
    {
        $data = Employee::get();

        return response()->json([
            'message'=>'success',
            'data'=>$data
        ]);
    }

    public function add(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name'=>'required',
            'emp_id'=>'required|unique:employees',
            'rank'=>'required',
            'gol_room'=>'required',
            'position'=>'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
                'data' => [],
            ], 400);
        }

        try {
            $data = Employee::create([
                'name'=>$request->name,
                'emp_id'=>$request->emp_id,
                'rank'=>$request->rank,
                'gol_room'=>$request->gol_room,
                'position'=>$request->position
            ]);

            return response()->json([
                'message' => 'Success',
                'data' => $data,
                // 'percakapan' => $percakapan
            ]);   
            
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
            'emp_id'=>'required|unique:employees,emp_id,' . $id,
            'rank'=>'required',
            'gol_room'=>'required',
            'position'=>'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => $validator->errors(),
                'data' => [],
            ],400);
        }

        try {
            $employee = Employee::where('id', $id)->first();
            
            $employee->update([
                'name'=>$request->name,
                'emp_id'=>$request->emp_id,
                'rank'=>$request->rank,
                'gol_room'=>$request->gol_room,
                'position'=>$request->position
            ]);

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
        $employee = Employee::where('id', $id)->first();
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
        // Excel::import(new ImportGuru,
        //               $request->file('file')->store('file_data'));
        // return redirect()->back();

        Excel::import(new EmployeesImport, $request->file('file')->store('file_data'));

        return response()->json([
            'success' => true,
            'message' => 'berhasil import data'
        ]);
    }
}