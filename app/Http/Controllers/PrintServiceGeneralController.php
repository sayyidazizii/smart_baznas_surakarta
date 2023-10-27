<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\PublicController;
use App\Providers\RouteServiceProvider;
use App\Models\CoreSection;
use App\Models\CoreService;
use App\Models\CoreServiceGeneralPriority;
use App\Models\TransServiceDisposition;
use App\Models\TransServiceDispositionParameter;
use App\Models\TransServiceGeneral;
use App\Models\TransServiceGeneralParameter;
use App\Models\TransServiceDispositionTerm;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Elibyy\TCPDF\Facades\TCPDF;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Str;

class PrintServiceGeneralController extends PublicController
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        if(!Session::get('start_date')){
            $start_date     = date('Y-m-d');
        }else{
            $start_date = Session::get('start_date');
        }

        if(!Session::get('end_date')){
            $end_date     = date('Y-m-d');
            $stop_date = date('Y-m-d', strtotime($end_date . ' +1 day'));
        }else{
            $end_date = Session::get('end_date');
            $stop_date = date('Y-m-d', strtotime($end_date . ' +1 day'));
        }

        $section_id = Session::get('section_id');
        $service_id = Session::get('service_id');

        $transservicegeneral = TransServiceGeneral::where('data_state','=',0)
        ->where('created_at','>=',$start_date)
        ->where('created_at','<=',$stop_date)
        ->where('service_general_status', 1);
        if($section_id||$section_id!=null||$section_id!=''){
            $transservicegeneral   = $transservicegeneral->where('section_id', $section_id);
        }
        if($service_id||$service_id!=null||$service_id!=''){
            $transservicegeneral   = $transservicegeneral->where('service_id', $service_id);
        }
        $transservicegeneral   = $transservicegeneral->get();

        $coresection = CoreSection::where('data_state', 0)
        ->pluck('section_name', 'section_id');

        $coreservice = CoreService::where('data_state', 0)
        ->pluck('service_name', 'service_id');

        return view('content/PrintServiceGeneral/ListTransServiceGeneral',compact('transservicegeneral', 'start_date', 'end_date', 'coresection', 'coreservice', 'section_id', 'service_id'));
    }
    

    public function filter(Request $request){
        $start_date     = $request->start_date;
        $end_date       = $request->end_date;
        $section_id     = $request->section_id;
        $service_id     = $request->service_id;

        Session::put('start_date', $start_date);
        Session::put('end_date', $end_date);

        return redirect('/print-service-general');
    }

    public function addReset()
    {
        Session::forget('sess_servicetoken');
        Session::forget('data_coresection');

        return redirect('/section/add');
    }

    public function getServiceName($service_id){
        $service = CoreService::where('data_state', 0)
        ->where('service_id', $service_id)
        ->first();

        return $service['service_name'];
    }

    public function getSectionName($section_id){
        $section = CoreSection::where('data_state', 0)
        ->where('section_id', $section_id)
        ->first();

        return $section['section_name'];
    }

    public function getPriorityName($priority_id){
        $priority = CoreServiceGeneralPriority::where('data_state', 0)
        ->where('service_general_priority_id', $priority_id)
        ->first();

        return $priority['service_general_priority_name'];
    }
    
    public function export($service_general_id){
        $transservicegeneral = TransServiceGeneral::findOrFail($service_general_id);

        $transservicegeneralparameter = TransServiceGeneralParameter::select('trans_service_general_parameter.*', 'core_service_general_parameter.service_general_parameter_name')
        ->where('trans_service_general_parameter.service_general_id', $service_general_id)
        ->join('core_service_general_parameter', 'core_service_general_parameter.service_general_parameter_id', 'trans_service_general_parameter.general_parameter_id')
        ->where('trans_service_general_parameter.data_state', 0)
        ->get();

        $username = User::select('name')->where('user_id','=',Auth::id())->first();

        $this->set_log(Auth::id(), $username['name'],'1089','Application.PrintService.export',$username['name'],'Export');


        // create new PDF document
        $pdf = new TCPDF('P', PDF_UNIT, 'A4', true, 'UTF-8', false);

        $pdf::SetPrintHeader(false);
        $pdf::SetPrintFooter(false);

        $pdf::SetMargins(6, 6, 6, 6); // put space of 10 on top

        // set image scale factor
        $pdf::setImageScale(PDF_IMAGE_SCALE_RATIO);

        // set some language-dependent strings (optional)
        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
            $pdf::setLanguageArray($l);
        }

        // ---------------------------------------------------------

        // set font
        $pdf::SetFont('helvetica', 'B', 20);

        // add a page
        $pdf::AddPage();

        /*$pdf::Write(0, 'Example of HTML tables', '', 0, 'L', true, 0, false, false, 0);*/

        $pdf::SetFont('helvetica', '', 10);
        // set image scale factor

        $pdf::setImageScale(PDF_IMAGE_SCALE_RATIO);

        

        // set some language-dependent strings (optional)
        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
            $pdf::setLanguageArray($l);
        }

        // -----------------------------------------------------------------------------

        

        $export = "
        <div style=\"text-align:center;\">
            <a style=\"color: green; text-decoration: none; font-size: 25px\">BAZNAS</a>
            <br/>
            <a style=\"color: black; text-decoration: none;\">Badan Amil Zakat Nasional</a>
            <br/>
            <a style=\"color: green; text-decoration: none; font-size: 15px\">KOTA SURAKARTA</a>
        </div>
        <hr style=\"width:100%; text-align:left !important; \"></hr>
        <br></br>
        <div style=\"text-align:center;\">
            <a style=\"color: black; text-decoration: none; font-size: 15px\">FORMULIR SURAT UMUM</a>
            <br/>
        </div>
        <br/>
        <br/>
        <br/>
        ";

        $export2 = "";
        $no = 1;
        foreach($transservicegeneralparameter as $key => $val){
            $export2 .= "
                <a style=\"color: black; text-decoration: none;\">".$no.". ".$val['service_general_parameter_name']." : </a>
                <a style=\"color: #343633; text-decoration: none;\">".$val['service_general_parameter_value']."</a>
                <br/>
                <br/>
                <br/>
            ";
            $no++;
        }

        $pdf::writeHTML($export.$export2, true, false, false, false, '');

        if (ob_get_contents()) ob_end_clean();
        // -----------------------------------------------------------------------------
        
        //Close and output PDF document
        $filename = 'Surat Umum_'.$transservicegeneral['service_general_id'].'.pdf';
        $pdf::Output($filename, 'I');

        //============================================================+
        // END OF FILE
        //============================================================+
    }
    public function exportRecap()
    {
        if(!Session::get('start_date')){
            $start_date     = date('Y-m-d');
        }else{
            $start_date     = Session::get('start_date');
        }

        if(!Session::get('end_date')){
            $end_date     = date('Y-m-d');
            $stop_date = date('Y-m-d', strtotime($end_date . ' +1 day'));
        }else{
            $end_date = Session::get('end_date');
            $stop_date = date('Y-m-d', strtotime($end_date . ' +1 day'));
        }

        if(!Session::get('section_id')){
            $section_id      = '';
        }else{
            $section_id      = Session::get('section_id');
        }

        if(!Session::get('service_id')){
            $service_id      = '';
        }else{
            $service_id      = Session::get('service_id');
        }

        $coreservice = CoreService::where('data_state', 0)->get();
        $coresection = CoreSection::where('data_state', 0)->get();
        
        $transservicegeneral = TransServiceGeneral::where('data_state','=',0)
        ->where('created_at','>=',$start_date)
        ->where('created_at','<=',$stop_date);
        // ->where('service_general_status', '!=', 0);
        $transservicegeneral   = $transservicegeneral->get();
        
        $spreadsheet = new Spreadsheet();

        if(count($transservicegeneral)>=0){
            $spreadsheet->getProperties()->setCreator("SIABAS")
                                         ->setLastModifiedBy("SIABAS")
                                         ->setTitle("Rekap Surat Umum")
                                         ->setSubject("")
                                         ->setDescription("Rekap Surat Umum")
                                         ->setKeywords("Rekap Surat Umum")
                                         ->setCategory("Rekap Surat Umum");
                                 
            $sheet = $spreadsheet->getActiveSheet(0);
            $spreadsheet->getActiveSheet()->setTitle("Rekap Surat Umum");
            $spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
            $spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
            $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(5);
            $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(25);
            $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(25);
            $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(60);
            $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(50);
            $spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(60);
    
            $spreadsheet->getActiveSheet()->mergeCells("B2:E2");
            $spreadsheet->getActiveSheet()->getStyle('B2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->getStyle('B2')->getFont()->setBold(true)->setSize(16);

            $spreadsheet->getActiveSheet()->getStyle('B4:F4')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $spreadsheet->getActiveSheet()->getStyle('B4:E4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->getStyle('H6'.':I6')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $spreadsheet->getActiveSheet()->getStyle('H6:I6')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            
            $sheet->setCellValue('B2',"Rekap Surat Umum Dari Periode ".date('d M Y', strtotime($start_date))." s.d. ".date('d M Y', strtotime($end_date)));	
            $sheet->setCellValue('B4',"No");
            $sheet->setCellValue('C4',"Tanggal");
            $sheet->setCellValue('D4',"Nama Instansi");
            $sheet->setCellValue('E4',"Prioritas");
            $sheet->setCellValue('F4',"Status");
            $sheet->setCellValue('H6',"Status");
            $sheet->setCellValue('I6',"Jumlah");
            
            
            $j=5;
            $no=0;
            $approve_total = 0;
            $disapprove_total = 0;
            $draft_total = 0;
            foreach($transservicegeneral as $key=>$val){

                if(is_numeric($key)){
                    
                    $sheet = $spreadsheet->getActiveSheet(0);
                    $spreadsheet->getActiveSheet()->setTitle("Rekap Layanan");
                    $spreadsheet->getActiveSheet()->getStyle('B'.$j.':F'.$j)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            
                    $spreadsheet->getActiveSheet()->getStyle('B'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);


                        $no++;
                        $sheet->setCellValue('B'.$j, $no);
                        $sheet->setCellValue('C'.$j, $val['created_at']);
                        $sheet->setCellValue('D'.$j, $val['service_general_agency']);
                        $sheet->setCellValue('E'.$j, $this->getPriorityName($val['service_general_priority']));
                        if($val['service_general_status'] == 1){
                            $sheet->setCellValue('F'.$j, 'Disetujui');
                            $approve_total+=1;
                        }else if($val['service_general_status'] == 2){
                            $sheet->setCellValue('F'.$j, 'Ditolak');
                            $disapprove_total+=1;
                        }else{
                            $sheet->setCellValue('F'.$j, 'Draft');
                            $draft_total+=1;
                        }
                        
                    
                }else{
                    continue;
                }
                $j++;
        
            }

            $sheet->setCellValue('H7',"Disetujui");
            $sheet->setCellValue('H8',"Ditolak");
            $sheet->setCellValue('H9',"Draft");
            $sheet->setCellValue('I7',$approve_total);
            $sheet->setCellValue('I8',$disapprove_total);
            $sheet->setCellValue('I9',$draft_total);
            $i=10;
            
            $spreadsheet->getActiveSheet()->getStyle('H7:I'.($i))->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $spreadsheet->getActiveSheet()->getStyle('I7:I'.$i)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->setCellValue('H'.($i), "Total");
            $sheet->setCellValue('I'.($i), $approve_total+$disapprove_total+$draft_total);
               
            if (ob_get_contents()) ob_end_clean();
            $filename='Rekap_Surat_Umum_'.$start_date.'_s.d._'.$end_date.'.xls';
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="'.$filename.'"');
            header('Cache-Control: max-age=0');

            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');
            $writer->save('php://output');
        }else{
            echo "Maaf data yang di eksport tidak ada !";
        }

    }
}
