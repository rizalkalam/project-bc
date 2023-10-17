<?php

namespace App\Imports;

use App\Models\Employee;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class EmployeesImport implements ToCollection, WithValidation, WithStartRow 
{
    // /**
    // * @param array $row
    // *
    // * @return \Illuminate\Database\Eloquent\Model|null
    // */

    public function startRow(): int
    {
        return 2; // Baris awal yang ingin Anda gunakan (misalnya, baris ke-2)
    }
    
    public function rules(): array
    {
        return [
            '*.0' => 'required',
            '*.1' => 'required|unique:users,emp_id',
            '*.2' => 'required',
            '*.3' => 'required',
            '*.4' => 'required',
        ];
    }

    public function customValidationMessages(): array
    {
        return [
            'required' => 'Kolom :attribute wajib diisi.',
            'unique' => 'Nilai :attribute sudah ada sebelumnya.',
            'regex' => 'Kolom :attribute tidak valid.'
        ];
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $row) {
            // Data validation for each row is handled by the rules() method.
            // You can add additional custom validation logic here if needed.
            // If a row doesn't pass validation, it won't be imported.
            Employee::create([
                'name' => $row[0],
                'emp_id' => $row[1],
                'rank' => $row[2],
                'gol_room' => $row[3],
                'position' => $row[4]
            ]);
        }
    }
}
