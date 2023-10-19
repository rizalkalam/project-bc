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
        Role::create(['name' => 'biasa']);
        Role::create(['name' => 'ppk']);

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

        $master->assignRole('master');

        $officer = User::create([
            'id'=>2,
            'name'=>'Ari Wirasto',
            'email'=>'ari171@gmail.com',
            'password'=>'ari123',
            'emp_id'=>196012231983031002,
            'rank'=>'Penata Tk.1',
            'gol_room'=>'III/D',
            'position'=>'Kepala Kantor'
        ]);

        $officer->assignRole('ppk');

        $officer = User::create([
            'id'=>3,
            'name'=>'Bambang Setiawan',
            'email'=>'bambang@gmail.com',
            'password'=>'bambang',
            'emp_id'=>196012231983031999,
            'rank'=>'Penata Tk.1',
            'gol_room'=>'III/D',
            'position'=>'Kepala Kantor'
        ]);

        $officer->assignRole('ppk');

        $employee = User::create([
            'id'=>4,
            'name'=>'Galang Jati Saka',
            'email'=>'galang@gmail.com',
            'password'=>'galang',
            'emp_id'=>19601223198307,
            'rank'=>'Penata Tk.1',
            'gol_room'=>'III/D',
            'position'=>'Kepala Kantor'
        ]);

        $employee->assignRole('biasa');
    }
}
