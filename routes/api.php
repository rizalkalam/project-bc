<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Client\AuthController;
use App\Http\Controllers\Client\PrintController;
use App\Http\Controllers\Client\EmployeeController;
use App\Http\Controllers\Client\AssignmentController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('/login', [AuthController::class, 'login']);

// wrong token
Route::get('/wrongtoken', [AuthController::class, 'wrongtoken'])->name('wrongtoken');

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:sanctum')->group(function () {
    //employees
    Route::group(['prefix'=>'employee'], function () {
        Route::get('/data', [EmployeeController::class, 'index']);
        Route::get('/detail/{id}', [EmployeeController::class, 'detail']);
        Route::post('/add', [EmployeeController::class, 'add'])->middleware('CheckRole:master');
        Route::post('/edit/{id}', [EmployeeController::class, 'edit'])->middleware('CheckRole:master');
        Route::delete('/delete/{id}', [EmployeeController::class, 'delete'])->middleware('CheckRole:master');

        //import employees
        Route::get('/file-import',[EmployeeController::class, 'importView'])->name('import-view')->middleware('CheckRole:master');
        Route::post('/import',[EmployeeController::class, 'import'])->name('import')->middleware('CheckRole:master');
    });

    //assignment
    Route::group(['prefix'=>'assignment'], function () {
        Route::get('/data', [AssignmentController::class, 'index']);
        Route::get('/detail/{id}', [AssignmentController::class, 'show_assignment']);
        Route::get('/ppk', [AssignmentController::class, 'show_ppk']);
        Route::get('/nonplh', [AssignmentController::class, 'show_nonplh']);
        Route::get('/backup', [AssignmentController::class, 'data_backup']);
        Route::post('/create', [AssignmentController::class, 'create']);
        Route::post('/edit/{id}', [AssignmentController::class, 'edit']);
        Route::delete('/delete/{id}', [AssignmentController::class, 'delete']);

        Route::get('/printspd/{nomor_identitas}', [PrintController::class, 'print_spd']);
        Route::get('/printst/{nomor_identitas}', [PrintController::class, 'print_st']);

        Route::post('/recore/{id}', [AssignmentController::class, 'recore']);

        Route::get('/test/{id}', [AssignmentController::class, 'test']);
        
    })->middleware('CheckRole:ppk, master');
});
