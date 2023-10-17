<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //create_role
        Role::create(['name' => 'master']);
        Role::create(['name' => 'employee']);
        Role::create(['name' => 'viewer']);

        $master = User::create([
            'id'=>1,
            'name'=>'master',
            'email'=>'master@gmail.com',
            'password'=>'master123',
            'emp_id'=>9999999999999,
            'rank'=>'master',
            'gol_room'=>'master',
            'position'=>'master'
        ]);

        $employee = User::create([
            'id'=>2,
            'name'=>'Ari Wirasto',
            'email'=>'ari171@gmail.com',
            'password'=>'ari123',
            'emp_id'=>196012231983031002,
            'rank'=>'Penata Tk.1',
            'gol_room'=>'III/D',
            'position'=>'Kepala Kantor'
        ]);

        $employee->assignRole('employee');
        $master->assignRole('master');
    }
}
