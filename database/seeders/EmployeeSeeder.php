<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Employee;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class EmployeeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //master untuk kepentingan login
        User::create([
            'id'=>'1',
            'name'=>'master',
            'email'=>'master@gmail.com',
            'password'=>'master123',
        ]);

        // Employee::create([
        //     'id'=>1,
        //     'name'=>'Ari Wirasto',
        //     'emp_id'=>196012231983031002,
        //     'rank'=>'Penata Tk.1',
        //     'gol_room'=>'III/D',
        //     'position'=>'Kepala Kantor'
        // ]);
    }
}
