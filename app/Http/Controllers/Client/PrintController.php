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
use PhpOffice\PhpWord\TemplateProcessor;


class PrintController extends Controller
{
    public function print_spd($id)
    {
        $data = Assignment::join('employees', 'employees.id', 'assignments.employee_id')
        ->join('units', 'units.id', 'assignments.unit_id')
        ->join('transportations', 'transportations.id', 'assignments.transportation_id')
        ->where('assignments.id', $id)
        ->first();

        $startDate = Carbon::parse($data->departure_date);
        $endDate = Carbon::parse($data->return_date);
        $duration = $startDate->diffInDays($endDate);

        // Load the template file
        $template = new TemplateProcessor(storage_path('app/template_spd.docx'));

        // Replace placeholders in the template with actual data
        $dataValue = [
            'spdPjg'=>'tes-datadummy',
            'namaPpk'=>'tes-datadummy',
            'namaPeg'=>$data->name,
            'nipPeg'=>$data->emp_id,
            'pangkatPeg'=>$data->rank,
            'jabPeg'=>$data->position,
            'golPeg'=>$data->gol_room,
            'maksudPd'=>'tes-datadummy',
            'kotaAsal1'=>$data->city_origin,
            'lamaTugas'=> $duration,
            'tglSpd'=>$data->date_spd,
            'tglBerangkat'=>$data->departure_date,
            'tglKembali'=>$data->reutrn_date,
            'pencairan'=>$data->dipa_search,
            'akun'=>'tes-datadummy',
            'stPjg'=>'tes-datadummy',
            'namaPpk'=>'tes-datadummy',
            'nipPpk'=>'tes-datadummy',
            'jenisKendaraan'=>$data->transportation_name,
            'kotaTujuan'=>$data->destination_city_1,
            'tglBerangkat'=>$data->departure_date,
            'helperPlh'=>'tes-datadummy',
            'namaPej'=>'tes-datadummy',
            'nipPej'=>'tes-datadummy',
            'kotaTujuanII'=>$data->destination_city_2,
            'kotaTujuanIII'=>$data->destination_city_1,
        ];
        $template->setValues($dataValue);

        // Save the modified template as a new file
        $filename = 'print_spd.docx';
        $template->saveAs(storage_path('app/' . $filename));

        // Provide the Word document as a download with the correct headers
        $headers = [
            'Content-Type' => 'application/msword',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        return response()->file(storage_path('app/' . $filename), $headers)->deleteFileAfterSend(true);
    }
}
