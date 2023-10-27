<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\PublicController;
use App\Providers\RouteServiceProvider;
use App\Models\CoreKecamatan;
use App\Models\CoreSection;
use App\Models\CoreService;
use App\Models\TransServiceDisposition;
use App\Models\TransServiceDispositionParameter;
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

class PrintServiceController extends PublicController
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

        $transservicedisposition = TransServiceDisposition::where('data_state','=',0)
        ->where('created_at','>=',$start_date)
        ->where('created_at','<=',$stop_date);
        // ->where('approved_status', 1)
        // ->where('review_status', 1);
        if($section_id||$section_id!=null||$section_id!=''){
            $transservicedisposition   = $transservicedisposition->where('section_id', $section_id);
        }
        if($service_id||$service_id!=null||$service_id!=''){
            $transservicedisposition   = $transservicedisposition->where('service_id', $service_id);
        }
        $transservicedisposition   = $transservicedisposition->get();

        $coresection = CoreSection::where('data_state', 0)
        ->pluck('section_name', 'section_id');

        $coreservice = CoreService::where('data_state', 0)
        ->pluck('service_name', 'service_id');

        return view('content/PrintService/ListTransServiceDisposition',compact('transservicedisposition', 'start_date', 'end_date', 'coresection', 'coreservice', 'section_id', 'service_id'));
    }
    

    public function filter(Request $request){
        $start_date     = $request->start_date;
        $end_date       = $request->end_date;
        $section_id     = $request->section_id;
        $service_id     = $request->service_id;

        Session::put('start_date', $start_date);
        Session::put('end_date', $end_date);
        Session::put('section_id', $section_id);
        Session::put('service_id', $service_id);

        return redirect('/print-service');
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
    
    public function export($service_disposition_id){
        $transservicedisposition = TransServiceDisposition::findOrFail($service_disposition_id);

        $coreservice = CoreService::where('service_id', $transservicedisposition['service_id'])
        ->where('data_state', 0)
        ->first();

        $transservicedispositionparameter = TransServiceDispositionParameter::select('trans_service_disposition_parameter.*', 'core_service_parameter.service_parameter_description')
        ->where('service_disposition_id', $transservicedisposition['service_disposition_id'])
        ->join('core_service_parameter', 'core_service_parameter.service_parameter_id', 'trans_service_disposition_parameter.service_parameter_id')
        ->where('trans_service_disposition_parameter.data_state', 0)
        ->get();

        $transservicedispositionterm = TransServiceDispositionTerm::select('trans_service_disposition_term.*', 'core_service_term.service_term_description')
        ->where('service_disposition_id', $transservicedisposition['service_disposition_id'])
        ->join('core_service_term', 'core_service_term.service_term_id', 'trans_service_disposition_term.service_term_id')
        ->where('trans_service_disposition_term.data_state', 0)
        ->get();

        $username = User::select('name')->where('user_id','=',Auth::id())->first();

        $this->set_log(Auth::id(), $username['name'],'1089','Application.PrintService.export',$username['name'],'Export');

        $pdf = new TCPDF('P', PDF_UNIT, 'A4', true, 'UTF-8', false);

        $pdf::SetPrintHeader(false);
        $pdf::SetPrintFooter(false);

        $pdf::SetMargins(6, 6, 6, 6);
        $pdf::setImageScale(PDF_IMAGE_SCALE_RATIO);

        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
            $pdf::setLanguageArray($l);
        }


        $pdf::SetFont('helvetica', 'B', 20);
        $pdf::AddPage();
        $pdf::SetFont('helvetica', '', 10);
        $pdf::setImageScale(PDF_IMAGE_SCALE_RATIO);

        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
            $pdf::setLanguageArray($l);
        }

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
            <a style=\"color: black; text-decoration: none; font-size: 15px\">FORMULIR LAYANAN</a>
            <br/>
            <a style=\"color: black; text-decoration: none; font-size: 15px\">".Str::upper($coreservice['service_name'])."</a>
        </div>
        <br/>
        <br/>
        <br/>
        ";

        $export2    = "";
        $no         = 1;
        foreach($transservicedispositionparameter as $key => $val){
            $export2 .= "
                <a style=\"color: black; text-decoration: none;\">".$no.". ".$val['service_parameter_description']." : </a>
                <a style=\"color: #343633; text-decoration: none;\">".$val['service_disposition_parameter_value']."</a>
                <br/>
                <br/>
                <br/>
            ";
            $no++;
        }

        $export3 ="
        <br pagebreak=\"true\"/>
        <div style=\"text-align:center;\">
            <a style=\"color: black; text-decoration: none; font-size: 15px\">SYARAT DAN KETENTUAN</a>
            <br/>
            <a style=\"color: black; text-decoration: none; font-size: 15px\">".Str::upper($coreservice['service_name'])."</a>
        </div>
        <br/>
        <br/>
        <br/>
        ";

        $export4    = "";
        $no         = 1;
        foreach($transservicedispositionterm as $key => $val){
            $export4 .= "
                <a style=\"color: black; text-decoration: none;\">".$no.". ".$val['service_term_description']."</a>
            ";
            
            if($val['service_disposition_term_status']==1){
                $export4 .= "
                <a style=\"color: green; text-decoration: none;\">(v)</a>
                <br/>
                ";
            }else{
                $export4 .= "
                <a style=\"color: red; text-decoration: none;\">(x)</a>
                <br/>
                ";
            }
            $no++;
        }

        $pdf::writeHTML($export.$export2.$export3.$export4, true, false, false, false, '');

        if (ob_get_contents()) ob_end_clean();
        
        $filename = 'Disposisi Bantuan_'.$transservicedisposition['service_disposition_id'].'.pdf';
        $pdf::Output($filename, 'I');
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
        
        $transservicedisposition = TransServiceDisposition::where('data_state','=',0)
        ->where('created_at','>=',$start_date)
        ->where('created_at','<=',$stop_date);
        // ->where('approved_status', 1)
        // ->where('review_status', 1);
        if($section_id||$section_id!=null||$section_id!=''){
            $transservicedisposition   = $transservicedisposition->where('section_id', $section_id);
        }
        if($service_id||$service_id!=null||$service_id!=''){
            $transservicedisposition   = $transservicedisposition->where('service_id', $service_id);
        }
        $transservicedisposition   = $transservicedisposition->get();
        
        $spreadsheet = new Spreadsheet();

        if(count($transservicedisposition)>=0){
            $spreadsheet->getProperties()->setCreator("SIABAS")
                                         ->setLastModifiedBy("SIABAS")
                                         ->setTitle("Rekap Bantuan")
                                         ->setSubject("")
                                         ->setDescription("Rekap Bantuan")
                                         ->setKeywords("Rekap Bantuan")
                                         ->setCategory("Rekap Bantuan");
                                 
            $sheet = $spreadsheet->getActiveSheet(0);
            $spreadsheet->getActiveSheet()->setTitle("Rekap Bantuan");
            $spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
            $spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
            $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(5);
            $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(25);
            $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(25);
            $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(60);
            $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(50);
            $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(50);
            $spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(60);
    
            $spreadsheet->getActiveSheet()->mergeCells("A2:F2");
            $spreadsheet->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->getStyle('A2')->getFont()->setBold(true)->setSize(16);
            $spreadsheet->getActiveSheet()->getStyle('A4:F4')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $spreadsheet->getActiveSheet()->getStyle('A4:F4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->getStyle('H6'.':I6')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $spreadsheet->getActiveSheet()->getStyle('H6:I6')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $sheet->setCellValue('A2',"Rekap Bantuan Dari Periode ".date('d M Y', strtotime($start_date))." s.d. ".date('d M Y', strtotime($end_date)));	
            $sheet->setCellValue('A4',"No");
            $sheet->setCellValue('B4',"Tanggal");
            $sheet->setCellValue('C4',"Nama Pemohon");
            $sheet->setCellValue('D4',"Nama Bantuan");
            $sheet->setCellValue('E4',"Bagian");
            $sheet->setCellValue('F4',"Status");
            $sheet->setCellValue('H6',"Nama Bantuan");
            $sheet->setCellValue('I6',"Jumlah");
            
            $j  = 5;
            $no = 0;
            
            foreach($transservicedisposition as $key=>$val){
                if(is_numeric($key)){
                    $sheet = $spreadsheet->getActiveSheet(0);
                    $spreadsheet->getActiveSheet()->setTitle("Rekap Bantuan");
                    $spreadsheet->getActiveSheet()->getStyle('A'.$j.':F'.$j)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                    $spreadsheet->getActiveSheet()->getStyle('A'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    
                    $no++;
                    $sheet->setCellValue('A'.$j, $no);
                    $sheet->setCellValue('B'.$j, $val['created_at']);
                    $sheet->setCellValue('C'.$j, $val['service_requisition_name']);
                    $sheet->setCellValue('D'.$j, $this->getServiceName($val['service_id']));
                    $sheet->setCellValue('E'.$j, $this->getSectionName($val['section_id']));
                    if($val['service_disposition_funds_status']==1){
                        $sheet->setCellValue('F'.$j, "Dana Sudah Dicairkan");
                    }else if($val['service_disposition_funds_status']==2){
                        $sheet->setCellValue('F'.$j, "Dana Sudah Diberikan");
                    }else if($val['approved_status']==0){
                        $sheet->setCellValue('F'.$j, "Draft");
                    }else if($val['review_status']==1){ 
                        $sheet->setCellValue('F'.$j, "Disetujui Reviewer");
                    }else if($val['review_status']==2){ 
                        $sheet->setCellValue('F'.$j, "Ditolak Reviewer");
                    }else if($val['approved_status']==1){ 
                        $sheet->setCellValue('F'.$j, "Disetujui Bagian Disposisi");
                    }else if($val['approved_status']==3){ 
                        $sheet->setCellValue('F'.$j, "Ditolak Bagian Disposisi");
                    }else if($val['approved_status']==2){
                        $sheet->setCellValue('F'.$j, "Permintaan Edit");
                    } 
                }else{
                    continue;
                }
                $j++;
            }
            
            $i      = 7;
            $total  = 0;
            foreach($coreservice as $key=>$val){
                $servicedispositioncount = TransServiceDisposition::where('data_state', 0)
                ->where('service_id', $val['service_id'])
                ->count();
                if(is_numeric($key)){
                    $spreadsheet->getActiveSheet()->getStyle('H'.$i.':H'.$i)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                    $spreadsheet->getActiveSheet()->getStyle('I'.$i)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                    $no++;
                    $sheet->setCellValue('H'.$i, $val['service_name']);
                    $sheet->setCellValue('I'.$i, $servicedispositioncount);
                }else{
                    continue;
                }
                $i++;
                $total += $servicedispositioncount;
            }
            $spreadsheet->getActiveSheet()->getStyle('H'.($i).':I'.($i))->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $spreadsheet->getActiveSheet()->getStyle('I'.$i)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->setCellValue('H'.($i), "Total");
            $sheet->setCellValue('I'.($i), $total);
            
            $spreadsheet->getActiveSheet()->getStyle('H'.($i+3).':I'.($i+3))->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $spreadsheet->getActiveSheet()->getStyle('H'.($i+3).':I'.($i+3))->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->setCellValue('H'.($i+3),"Nama Bagian");
            $sheet->setCellValue('I'.($i+3),"Jumlah");
            $k = $i+4;
            foreach($coresection as $key=>$val){
                $sectiondispositioncount = TransServiceDisposition::where('data_state', 0)
                ->where('section_id', $val['section_id'])
                ->count();

                if(is_numeric($key)){
                    $spreadsheet->getActiveSheet()->getStyle('H'.$k.':I'.$k)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                    $spreadsheet->getActiveSheet()->getStyle('I'.$k)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                    $no++;
                    $sheet->setCellValue('H'.$k, $val['section_name']);
                    $sheet->setCellValue('I'.$k, $sectiondispositioncount);
                }else{
                    continue;
                }
                $k++;
            }
            $spreadsheet->getActiveSheet()->getStyle('H'.($k).':I'.($k))->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $spreadsheet->getActiveSheet()->getStyle('I'.$k)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $sheet->setCellValue('H'.($k), "Total");
            $sheet->setCellValue('I'.($k), $total);
               
            if (ob_get_contents()) ob_end_clean();
            $filename='Rekap_Bantuan_'.$start_date.'_s.d._'.$end_date.'.xls';
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="'.$filename.'"');
            header('Cache-Control: max-age=0');

            $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');
            $writer->save('php://output');
        }else{
            echo "Maaf data yang di eksport tidak ada !";
        }
    }

    public function exportRecapFundsReceived()
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
        
        $transservicedisposition = TransServiceDisposition::where('data_state','=',0)
        ->where('created_at','>=',$start_date)
        ->where('created_at','<=',$stop_date)
        ->where('service_disposition_funds_status', 2);

        if($section_id||$section_id!=null||$section_id!=''){
            $transservicedisposition   = $transservicedisposition->where('section_id', $section_id);
        }
        if($service_id||$service_id!=null||$service_id!=''){
            $transservicedisposition   = $transservicedisposition->where('service_id', $service_id);
        }
        $transservicedisposition   = $transservicedisposition->get();
        
        $corekecamatan = CoreKecamatan::where('data_state', 0)
        ->get();

        $spreadsheet = new Spreadsheet();

        if(count($transservicedisposition)>=0){
            $spreadsheet->getProperties()->setCreator("SIABAS")
                                         ->setLastModifiedBy("SIABAS")
                                         ->setTitle("Rekap Bantuan")
                                         ->setSubject("")
                                         ->setDescription("Rekap Bantuan")
                                         ->setKeywords("Rekap Bantuan")
                                         ->setCategory("Rekap Bantuan");
                                 
            $sheet = $spreadsheet->getActiveSheet(0);
            $spreadsheet->getActiveSheet()->setTitle("Rekap Bantuan");
            $spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
            $spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
            $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(5);
            $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(25);
            $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(25);
            $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(60);
            $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(50);
            $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(50);
            $spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(40);
            $spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(30);

            $spreadsheet->getActiveSheet()->mergeCells("A2:F2");
            $spreadsheet->getActiveSheet()->getStyle('A2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
            $spreadsheet->getActiveSheet()->getStyle('A2')->getFont()->setBold(true)->setSize(16);
            $spreadsheet->getActiveSheet()->getStyle('A4:F4')->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $spreadsheet->getActiveSheet()->getStyle('A4:F4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            $sheet->setCellValue('A2',"Rekap Bantuan Dari Periode ".date('d M Y', strtotime($start_date))." s.d. ".date('d M Y', strtotime($end_date)));	
            $sheet->setCellValue('A4',"No");
            $sheet->setCellValue('B4',"Tanggal");
            $sheet->setCellValue('C4',"Nama Pemohon");
            $sheet->setCellValue('D4',"Nama Bantuan");
            $sheet->setCellValue('E4',"Bagian");
            $sheet->setCellValue('F4',"Jumlah");
            
            $j  = 5;
            $no = 0;
            
            foreach($transservicedisposition as $key => $val){
                if(is_numeric($key)){
                    $sheet = $spreadsheet->getActiveSheet(0);
                    $spreadsheet->getActiveSheet()->setTitle("Rekap Bantuan");
                    $spreadsheet->getActiveSheet()->getStyle('A'.$j.':F'.$j)->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                    $spreadsheet->getActiveSheet()->getStyle('A'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $spreadsheet->getActiveSheet()->getStyle('F'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);

                    $no++;
                    $sheet->setCellValue('A'.$j, $no);
                    $sheet->setCellValue('B'.$j, $val['created_at']);
                    $sheet->setCellValue('C'.$j, $val['service_requisition_name']);
                    $sheet->setCellValue('D'.$j, $this->getServiceName($val['service_id']));
                    $sheet->setCellValue('E'.$j, $this->getSectionName($val['section_id']));
                    $sheet->setCellValue('F'.$j, 'Rp. '.number_format($val['service_disposition_amount'], 2));
                }else{
                    continue;
                }
                $j++;
            }

            $sheet->setCellValue('I4',"Kecamatan");
            $sheet->setCellValue('J4',"Jumlah");
            $j = 4;

            foreach($corekecamatan as $key => $val){
                $spreadsheet->getActiveSheet()->getStyle('J'.$j)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT);
                
                $service_disposition_amount = TransServiceDisposition::where('data_state', 0)
                ->where('created_at','>=',$start_date)
                ->where('created_at','<=',$stop_date)
                ->where('kecamatan_id', $val['kecamatan_id'])
                ->where('service_disposition_funds_status', 2);

                if($section_id||$section_id!=null||$section_id!=''){
                    $service_disposition_amount   = $service_disposition_amount->where('section_id', $section_id);
                }
                if($service_id||$service_id!=null||$service_id!=''){
                    $service_disposition_amount   = $service_disposition_amount->where('service_id', $service_id);
                }
                $service_disposition_amount   = $service_disposition_amount->sum('service_disposition_amount');

                $sheet->setCellValue('I'.$j, $val['kecamatan_name']);
                $sheet->setCellValue('J'.$j, number_format($service_disposition_amount, 2));
                $j++;
            }

            $spreadsheet->getActiveSheet()->getStyle('I4'.':J'.($j-1))->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
            $spreadsheet->getActiveSheet()->getStyle('I4:j4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

            if (ob_get_contents()) ob_end_clean();
            $filename='Rekap_Bantuan_Diberikan_'.$start_date.'_s.d._'.$end_date.'.xls';
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
