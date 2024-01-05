<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Illuminate\Validation\ValidationException;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Imports\HeadingRowFormatter;

class EmployeesImport implements ToCollection, WithHeadingRow
{
    protected $existingNips = [];

    public function collection(Collection $rows)
    {
        $duplicateNips = []; // Inisialisasi array

        foreach ($rows as $row) {
            $nip = $row['nip'];

            // Check if the NIP already exists
            if (in_array($nip, $this->existingNips)) {
                $duplicateNips[] = $nip;
            } else {
                $this->existingNips[] = $nip;
            }

            if (empty(explode(' ',trim($row['nama']))[1])) {
                $custom_password = explode(' ',trim($row['nama']))[0].substr($row['nip'], 0, 5);
            } else {
                $custom_password = explode(' ',trim($row['nama']))[0].explode(' ',trim($row['nama']))[1].substr($row['nip'], 0, 5);
            }

            $employee = User::create([
                'name' => $row['nama'],
                'emp_id' => $row['nip'],
                'rank' => $row['pangkat'],
                'gol_room' => $row['gol_ruang'],
                'position' => $row['jabatan'],
                'password' => Hash::make($custom_password)
            ]);

            $employee->assignRole('biasa');
        }

        if (!empty($duplicateNips)) {
            $messages = [
                'nip' => 'Duplicate NIPs: ' . implode(', ', $duplicateNips),
            ];

            throw ValidationException::withMessages($messages);
        }
    }
}
