<?php

namespace App\Http\Controllers\Client;

use App\Models\Assignment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

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

    public function create()
    {
        $validator = Validator::make($request->all(),[
            
        ]);
    }
}
