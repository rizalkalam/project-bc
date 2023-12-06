<?php

namespace App\Http\Controllers\Client;

use Exception;
use HTMLPurifier;
use HTMLPurifier_Config;
use App\Models\Assignment;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\View;
use PhpOffice\PhpWord\Shared\ZipArchive;
use PhpOffice\PhpWord\TemplateProcessor;


class PrintController extends Controller
{
    public function print_spd($no_st)
    {
        $data = Assignment::join('users', 'users.id', 'assignments.user_id')
        ->join('users as ppk', 'ppk.id', 'assignment.input_name')
        ->where('assignments.no_st', $no_st)
        ->select([
            'assignments.*',
            'users.name as employee',
            'users.emp_id as nip_peg',
            'ppk.name as ppk',
            'ppk.emp_id as nip_ppk'
        ])
        ->get();

        $countId = $data->count();

        if ($countId > 1) {
            // Create a temporary directory to store individual documents
            $tempDir = storage_path('app/temp_docx/');
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }

            // Replace placeholders in the template with actual data
            foreach ($data as $key) {
                // Load the template file
                $template = new TemplateProcessor(storage_path('app/template_spd.docx'));

                $startDate = Carbon::parse($key->departure_date);
                $endDate = Carbon::parse($key->return_date);
                $duration = $startDate->diffInDays($endDate);
                $dataValue = [
                    'spdPjg'=>'tes-datadummy',
                    'namaPpk'=>$key->ppk,
                    'namaPeg'=>$key->employee,
                    'nipPeg'=>$key->nip_peg,
                    'pangkatPeg'=>$key->rank,
                    'jabPeg'=>$key->position,
                    'golPeg'=>$key->gol_room,
                    'maksudPd'=>$key->businesss_trip_reason,
                    'kotaAsal1'=>$key->city_origin,
                    'lamaTugas'=> $duration,
                    'tglSpd'=>$key->date_spd,
                    'tglBerangkat'=>$key->departure_date,
                    'tglKembali'=>$key->reutrn_date,
                    'pencairan'=>$key->dipa_search,
                    'akun'=>'tes-keydummy',
                    'stPjg'=>'tes-keydummy',
                    'nipPpk'=>$key->nip_ppk,
                    'jenisKendaraan'=>$key->transportation_name,
                    'kotaTujuan'=>$key->destination_city_1,
                    'tglBerangkat'=>$key->departure_date,
                    'helperPlh'=>'tes-keydummy',
                    'namaPej'=>'test-keydummy',
                    'nipPej'=>'tes-keydummy',
                    'kotaTujuanII'=>$key->destination_city_2,
                    'kotaTujuanIII'=>$key->destination_city_1,
                ];
                // $template->setValues($dataValue);
        
                // Set values for the template
                $template->setValues($dataValue);

                // Save the modified template as a temporary document
                $tempFilename = $tempDir . 'spd' . $key->name . '.docx';
                $template->saveAs($tempFilename);
                // $template->saveAs(storage_path('app/' . $filename));
            }

            // Create a ZIP archive and add all temporary documents to it
            $zip = new ZipArchive();
            $nameZip = 'print_spd' . $data->first()->no_st . '.zip';
            $zipFilename = storage_path('app/print_spd' . $nameZip);
            if ($zip->open($zipFilename, ZipArchive::CREATE) === TRUE) {
                $tempFiles = glob($tempDir . '*.docx');
                foreach ($tempFiles as $tempFile) {
                    $zip->addFile($tempFile, basename($tempFile));
                }
                $zip->close();
            }

            // Clean up temporary documents
            foreach ($tempFiles as $tempFile) {
                unlink($tempFile);
            }
            rmdir($tempDir);

            // Provide the ZIP archive as a download with the correct headers
            if (file_exists($zipFilename)) {
                $headers = [
                    'Content-Type' => 'application/zip',
                    'Content-Disposition' => 'attachment; filename="' . $nameZip . '"',
                ];

                return response()->download($zipFilename, $nameZip, $headers);
            } else {
                return response()->json(['message' => 'Failed to create ZIP archive'], 500);
            }
        }

        // Load the template file
        $template = new TemplateProcessor(storage_path('app/template_spd.docx'));

        foreach ($data as $key) {
            $startDate = Carbon::parse($key->departure_date);
            $endDate = Carbon::parse($key->return_date);
            $duration = $startDate->diffInDays($endDate);
            $dataValue = [
                'spdPjg'=>'tes-datadummy',
                'namaPpk'=>'tes-datadummy',
                'namaPeg'=>$key->name,
                'nipPeg'=>$key->emp_id,
                'pangkatPeg'=>$key->rank,
                'jabPeg'=>$key->position,
                'golPeg'=>$key->gol_room,
                'maksudPd'=>'tes-keydummy',
                'kotaAsal1'=>$key->city_origin,
                'lamaTugas'=> $duration,
                'tglSpd'=>$key->date_spd,
                'tglBerangkat'=>$key->departure_date,
                'tglKembali'=>$key->reutrn_date,
                'pencairan'=>$key->dipa_search,
                'akun'=>'tes-keydummy',
                'stPjg'=>'tes-keydummy',
                'namaPpk'=>'tes-keydummy',
                'nipPpk'=>'tes-keydummy',
                'jenisKendaraan'=>$key->transportation_name,
                'kotaTujuan'=>$key->destination_city_1,
                'tglBerangkat'=>$key->departure_date,
                'helperPlh'=>'tes-keydummy',
                'namaPej'=>'test-keydummy',
                'nipPej'=>'tes-keydummy',
                'kotaTujuanII'=>$key->destination_city_2,
                'kotaTujuanIII'=>$key->destination_city_1,
            ];
        }

        $template->setValues($dataValue);

        // Save the modified template as a new file
        $filename = 'print_spd' . $data->first()->name .'.docx';
        $template->saveAs(storage_path('app/' . $filename));

        // Provide the Word document as a download with the correct headers
        $headers = [
            'Content-Type' => 'application/msword',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        return response()->file(storage_path('app/' . $filename), $headers)->deleteFileAfterSend(true);

        
    }

    public function print_st($no_st)
    {
        $data = Assignment::join('users', 'users.id', 'assignments.user_id')
        ->join('units', 'units.id', 'assignments.unit_id')
        ->join('transportations', 'transportations.id', 'assignments.transportation_id')
        ->where('assignments.no_st', $no_st)
        ->select([
            'assignments.no_st',
            'users.name',
            'users.emp_id',
            'users.rank',
            'users.position',
            'users.gol_room',
            'assignments.date_spd'
        ])
        ->get();

        $dataValue = []; // Inisialisasi dataValue sebagai array di luar perulangan
        $no = 1; // Inisialisasi nomor awal

        $countId = $data->count();

        // Load the template file
        $template1 = new TemplateProcessor(storage_path('app/ST-1.docx'));
        $template2 = new TemplateProcessor(storage_path('app/ST-2.docx'));

        if ($countId > 1) {
            foreach ($data as $key) {
                $dataValue[] = [
                    'n'=>$no++,
                    'nama' => $key->name,
                    'pangkat' => $key->rank,
                    'jabatan' => $key->position,
                    'nip'=> $key->emp_id,
                    'gol'=> $key->gol_room,
                ];
            }
            
            $template2->cloneRowAndSetValues('n', $dataValue);
            $template2->setValue('no', '1329/KBC.1002/2023');
            $template2->setValue('tanggal', '10/08/2023');

            // Save the modified templa$template2 as a new file
            $filename = 'print_st2.docx';
            $template2->saveAs(storage_path('app/' . $filename));

            // Provide the Word document as a download with the correct headers
            $headers = [
                'Content-Type' => 'application/msword',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            return response()->file(storage_path('app/' . $filename), $headers)->deleteFileAfterSend(true);
            
        } else {
            foreach ($data as $key) {
                $dataValue[] = [
                    'n'=>'',
                    'nama' => $key->name,
                    'pangkat' => $key->rank,
                    'jabatan' => $key->position,
                    'nip'=> $key->emp_id,
                    'gol'=> $key->gol_room,
                ];
            }
            
            $template1->cloneRowAndSetValues('n', $dataValue);
            $template1->setValue('no', '1329/KBC.1002/2023');
            $template1->setValue('tanggal', '10/08/2023');

            // Save the modified template as a new file
            $filename = 'print_st1.docx';
            $template1->saveAs(storage_path('app/' . $filename));

            // Provide the Word document as a download with the correct headers
            $headers = [
                'Content-Type' => 'application/msword',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            return response()->file(storage_path('app/' . $filename), $headers)->deleteFileAfterSend(true);
        }

        // $startDate = Carbon::parse($data->departure_date);
        // $endDate = Carbon::parse($data->return_date);
        // $duration = $startDate->diffInDays($endDate);
    }
}
