<?php

namespace App\Http\Controllers\Client;

use Exception;
use HTMLPurifier;
use App\Models\Backup;
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
                    'tglST'=>$key->date_st ? Carbon::parse($key->date_st)->isoFormat('D MMMM Y') : null,
                    'tglSpd'=>$key->date_spd ? Carbon::parse($key->date_spd)->isoFormat('D MMMM Y') : null,
                    // 'nomorST'=> ,
                    'tglBerangkat'=>Carbon::parse($key->departure_date)->isoFormat('D MMMM Y'),
                    'tglKembali'=>Carbon::parse($key->return_date)->isoFormat('D MMMM Y'),
                    'pencairan'=>$key->dipa_search,
                    'stPjg'=>$key->no_spd,
                    'nipPpk'=>$key->nip_ppk,
                    'jenisKendaraan'=>$key->transportation !== null ? $key->transportation : '',
                    'helperPlh'=>$key->plh,
                    'namaPej'=>$key->namaPej,
                    'nipPej'=>$key->nipPej,
                    'kotaTujuan'=> $key->destination_city_1 . 
                                    ($key->destination_city_2 ? ' - ' . $key->destination_city_2 : '') . 
                                    ($key->destination_city_3 ? ' - ' . $key->destination_city_3 : ''),
                    'kotaTujuanI'=>$key->destination_city_1 !== null ? $key->destination_city_1 : '',
                    'kotaTujuanII'=>$key->destination_city_2 !== null ? $key->destination_city_2 : '',
                    'kotaTujuanIII'=>$key->destination_city_3 !== null ? $key->destination_city_3 : '',
                    'kotaTujuanIV'=>$key->destination_city_4 !== null ? $key->destination_city_4 : '',
                    'kotaTujuanV'=>$key->destination_city_5 !== null ? $key->destination_city_5 : '',
                ];

                $template_noST = 'ST-null/KBC.1002/'.Carbon::now()->format('Y');
            
                if ($key->nomor_st == '' || $key->nomor_st == null || $key->nomor_st == $template_noST) {
                    // $template->setValue('no', '[@NomorND]');
                    $dataValue['nomorST'] = '[@NomorND]';
                } else {
                    $dataValue['nomorST'] = $key->nomor_st;
                }

                if ($key->destination_city_2 == null || $key->destination_city_2 == " ") {
                    $dataValue['kotaKeTujuanII'] = $key->city_origin;
                    $dataValue['kotaKeTujuanIII'] = ' ';
                    $dataValue['kotaKeTujuanIV'] = ' ';
                    $dataValue['kotaKeTujuanV'] = ' ';
                } elseif ($key->destination_city_3 == null || $key->destination_city_3 == " ") {
                    $dataValue['kotaKeTujuanII'] = $key->destination_city_2;
                    $dataValue['kotaKeTujuanIII'] = $key->city_origin;
                    $dataValue['kotaKeTujuanIV'] = ' ';
                    $dataValue['kotaKeTujuanV'] = ' ';
                } elseif ($key->destination_city_4 == null || $key->destination_city_4 == " ") {
                    $dataValue['kotaKeTujuanII'] = $key->destination_city_2;
                    $dataValue['kotaKeTujuanIII'] = $key->destination_city_3;
                    $dataValue['kotaKeTujuanIV'] = $key->city_origin;
                    $dataValue['kotaKeTujuanV'] = ' ';
                } elseif ($key->destination_city_5 == null || $key->destination_city_5 == " ") {
                    $dataValue['kotaKeTujuanII'] = $key->destination_city_2;
                    $dataValue['kotaKeTujuanIII'] = $key->destination_city_3;
                    $dataValue['kotaKeTujuanIV'] = $key->destination_city_4;
                    $dataValue['kotaKeTujuanV'] = $key->city_origin;
                }

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
            $date_name = Carbon::parse($data->first()->update_at)->isoFormat('DMMMMY');
            $nameZip = 'print_spd_' . $date_name . '.zip';
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
        $assignment = Assignment::join('users', 'users.id', 'assignments.user_id')
        ->where('assignments.identity_number', $nomor_identitas)
        ->select([
            'assignments.no_st',
            'users.name as employee',
            'users.emp_id as nip_peg',
            'users.rank as pangkatPeg',
            'users.position as jabPeg',
            'users.gol_room as golPeg',
            'assignments.date_spd'
        ])
        ->get();

        $dataValue = []; // Inisialisasi dataValue sebagai array di luar perulangan
        $no = 1; // Inisialisasi nomor awal

        $countId = $assignment->count();

        // Load the template file
        $template = new TemplateProcessor(storage_path('app/template_st.docx'));

        if ($countId > 1) {
            foreach ($data as $key) {
                $dataValue[] = [
                    'n'=>$no++,
                    'nama' => $key->employee ,
                    'pangkat' => $key->pangkatPeg,
                    'jabatan' => $key->jabPeg,
                    'nip'=> $key->nip_peg,
                    'gol'=> $key->golPeg,
                ];
            }

            setlocale(LC_TIME, 'id_ID');
            \Carbon\Carbon::setLocale('id');
            $date_st = Carbon::parse($assignment->date_st)->isoFormat('D MMMM Y');
            
            $template->cloneRowAndSetValues('n', $dataValue);
            $template->setValue('dasar_pelaksanaanTugas', $assignment->business_trip_reason);
            $template->setValue('maksud_tujuanTugas', $assignment->implementation_tasks);
            $template->setValue('pencairan_dipa', $assignment->dipa_search);
            $template->setValue('helperPlh', $assignment->plh);
            $template->setValue('penanda_tangan', $assignment->head_officer);
            
            if ($assignment->date_st == null || $assignment->date_st == '') {
                $template->setValue('tanggal', '[@TanggalND]');
            } else {
                $template->setValue('tanggal', $date_st);
            }

            $template_noST = 'ST-null/KBC.1002/'.Carbon::now()->format('Y');
            
            if ($assignment->nomor_st == '' || $assignment->nomor_st == null || $assignment->nomor_st == $template_noST) {
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
                    'nama' => $key->employee ,
                    'pangkat' => $key->pangkatPeg,
                    'jabatan' => $key->jabPeg,
                    'nip'=> $key->nip_peg,
                    'gol'=> $key->golPeg,
                ];
            }

            setlocale(LC_TIME, 'id_ID');
            \Carbon\Carbon::setLocale('id');
            $date_st = Carbon::parse($assignment->date_st)->isoFormat('D MMMM Y');
            
            $template->cloneRowAndSetValues('n', $dataValue);
            $template->setValue('dasar_pelaksanaanTugas', $assignment->business_trip_reason);
            $template->setValue('maksud_tujuanTugas', $assignment->implementation_tasks);
            $template->setValue('pencairan_dipa', $assignment->dipa_search);
            $template->setValue('helperPlh', $assignment->plh);
            $template->setValue('penanda_tangan', $assignment->head_officer);

            if ($assignment->date_st == null || $assignment->date_st == '') {
                $template->setValue('tanggal', '[@TanggalND]');
            } else {
                $template->setValue('tanggal', $date_st);
            }

            $template_noST = 'ST-null/KBC.1002/'.Carbon::now()->format('Y');
            
            if ($assignment->nomor_st == '' || $assignment->nomor_st == null || $assignment->nomor_st == $template_noST) {
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
    }
}
