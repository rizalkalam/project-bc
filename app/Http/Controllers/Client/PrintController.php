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
    public function print_spd($nomor_identitas)
    {
        $data = Assignment::join('users', 'users.id', 'assignments.user_id')
        ->join('users as ppk', 'ppk.id', 'assignments.ppk')
        ->join('users as head_officer', 'head_officer.id', 'assignments.head_officer')
        ->where('assignments.identity_number', $nomor_identitas)
        ->select([
            'assignments.*',
            'users.name as employee',
            'users.emp_id as nip_peg',
            'users.rank as pangkatPeg',
            'users.gol_room as golPeg',
            'users.position as jabPeg',
            'ppk.name as ppk',
            'ppk.emp_id as nip_ppk',
            'head_officer.name as namaPej',
            'head_officer.emp_id as nipPej'
        ])
        ->get();

        $countId = $data->count();

            $tempDir = storage_path('app/temp_docx/');
            if (!file_exists($tempDir)) {
                mkdir($tempDir, 0755, true);
            }
            
            

            // Replace placeholders in the template with actual data
            foreach ($data as $key) {
                // Load the template file
                $templateST = new TemplateProcessor(storage_path('app/template_spd.docx'));

                $startDate = Carbon::parse($key->departure_date);
                $endDate = Carbon::parse($key->return_date)->addDay();
                $duration = $startDate->diffInDays($endDate);
                setlocale(LC_TIME, 'id_ID');
                \Carbon\Carbon::setLocale('id');
                $dataValue = [
                    'spdPjg'=>$key->no_spd,
                    'namaPpk'=>$key->ppk,
                    'namaPeg'=>$key->employee,
                    'nipPeg'=>$key->nip_peg,
                    'pangkatPeg'=>$key->pangkatPeg,
                    'jabPeg'=>$key->jabPeg,
                    'golPeg'=>$key->golPeg,
                    'maksudPd'=>$key->business_trip_reason,
                    'kotaAsal1'=>$key->city_origin,
                    'lamaTugas'=> $duration,
                    'tglSpd'=>Carbon::parse($key->date_spd)->isoFormat('D MMMM Y'),
                    'tglBerangkat'=>Carbon::parse($key->departure_date)->isoFormat('D MMMM Y'),
                    'tglKembali'=>Carbon::parse($key->return_date)->isoFormat('D MMMM Y'),
                    'pencairan'=>$key->dipa_search,
                    'stPjg'=>$key->no_st,
                    'nipPpk'=>$key->nip_ppk,
                    'jenisKendaraan'=>$key->transportation,
                    'kotaTujuan'=> $key->destination_city_1 . 
                                    ($key->destination_city_2 ? ' - ' . $key->destination_city_2 : '') . 
                                    ($key->destination_city_3 ? ' - ' . $key->destination_city_3 : ''),
                    'helperPlh'=>$key->plh,
                    'namaPej'=>$key->namaPej,
                    'nipPej'=>$key->nipPej,
                    'kotaTujuanI'=>$key->destination_city_1 !== null ? $key->destination_city_1 : '',
                    'kotaTujuanII'=>$key->destination_city_2 !== null ? $key->destination_city_2 : '',
                    'kotaTujuanIII'=>$key->destination_city_3 !== null ? $key->destination_city_3 : '',
                    'kotaTujuanIV'=>$key->destination_city_4 !== null ? $key->destination_city_4 : '',
                    'kotaTujuanV'=>$key->destination_city_5 !== null ? $key->destination_city_5 : '',
                ];
                // $template->setValues($dataValue);
        
                // Set values for the template
                $templateST->setValues($dataValue);

                // Save the modified template as a temporary document
                $tempFilename = $tempDir . 'spd' . $key->employee . '.docx';
                $templateST->saveAs($tempFilename);
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

                return response()->download($zipFilename, $nameZip, $headers)->deleteFileAfterSend(true);
                // return response()->json([
                //     'message' => 'Data Assignment success created',
                //     'data' => $dataValue
                // ], 200);
            } else {
                return response()->json(['message' => 'Failed to create ZIP archive'], 500);
            }
    }

    public function print_st($nomor_identitas)
    {
        $data = Assignment::join('users', 'users.id', 'assignments.user_id')
        // ->join('units', 'units.id', 'assignments.unit_id')
        // ->join('transportations', 'transportations.id', 'assignments.transportation_id')
        ->where('assignments.identity_number', $nomor_identitas)
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
        $template = new TemplateProcessor(storage_path('app/template_st.docx'));

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

            $assignment = Assignment::join('users as head_officer', 'head_officer.id', 'assignments.head_officer')
            ->where('assignments.identity_number', $nomor_identitas)
            ->select([
                'assignments.*', 
                'head_officer.name as head_officer',
                'assignments.identity_number as nomor_identitas'
            ])
            ->first();

            setlocale(LC_TIME, 'id_ID');
            \Carbon\Carbon::setLocale('id');
            $date_st = Carbon::parse($assignment->date_st)->isoFormat('D MMMM Y');
            
            $template->cloneRowAndSetValues('n', $dataValue);
            $template->setValue('dasar_pelaksanaanTugas', $assignment->business_trip_reason);
            $template->setValue('maksud_tujuanTugas', $assignment->implementation_tasks);
            $template->setValue('helperPlh', $assignment->plh);
            $template->setValue('penanda_tangan', $assignment->head_officer);
            
            if ($assignment->date_st == null) {
                $template->setValue('tanggal', '[@TanggalND]');
            } else {
                $template->setValue('tanggal', $date_st);
            }

            if ($assignment->nomor_st == '') {
                $template->setValue('no', '[@NomorND]');
            } else {
                $template->setValue('no', $assignment->nomor_st);
            }

            // Save the modified templa$template as a new file
            $filename = 'print_st2.docx';
            $template->saveAs(storage_path('app/' . $filename));

            // Provide the Word document as a download with the correct headers
            $headers = [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
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

            $assignment = Assignment::join('users as head_officer', 'head_officer.id', 'assignments.head_officer')
            ->where('assignments.identity_number', $nomor_identitas)
            ->select([
                'assignments.*', 
                'head_officer.name as head_officer',
                'assignments.identity_number as nomor_identitas'
            ])
            ->first();

            setlocale(LC_TIME, 'id_ID');
            \Carbon\Carbon::setLocale('id');
            $date_st = Carbon::parse($assignment->date_st)->isoFormat('D MMMM Y');
            
            $template->cloneRowAndSetValues('n', $dataValue);
            $template->setValue('dasar_pelaksanaanTugas', $assignment->business_trip_reason);
            $template->setValue('maksud_tujuanTugas', $assignment->implementation_tasks);
            $template->setValue('helperPlh', $assignment->plh);
            $template->setValue('penanda_tangan', $assignment->head_officer);

            if ($assignment->date_st == null) {
                $template->setValue('tanggal', '[@TanggalND]');
            } else {
                $template->setValue('tanggal', $date_st);
            }
            
            if ($assignment->nomor_st == '') {
                $template->setValue('no', '[@NomorND]');
            } else {
                $template->setValue('no', $assignment->nomor_st);
            }

            // Save the modified template as a new file
            $filename = 'print_st1.docx';
            $template->saveAs(storage_path('app/' . $filename));

            // Provide the Word document as a download with the correct headers
            $headers = [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            return response()->file(storage_path('app/' . $filename), $headers)->deleteFileAfterSend(true);
        }

        // $startDate = Carbon::parse($data->departure_date);
        // $endDate = Carbon::parse($data->return_date);
        // $duration = $startDate->diffInDays($endDate);
    }
}
