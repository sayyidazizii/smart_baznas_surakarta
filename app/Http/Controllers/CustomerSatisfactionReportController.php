<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\PublicController;
use App\Providers\RouteServiceProvider;
use App\Models\RelationCustomerSatisfaction;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Elibyy\TCPDF\Facades\TCPDF;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Str;

class CustomerSatisfactionReportController extends PublicController
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
        
        $relationcustomersatisfication = RelationCustomerSatisfaction::where('customer_satisfaction_date','>=',$start_date)
        ->where('customer_satisfaction_date','<=',$stop_date)
        ->get();

        return view('content/CustomerSatisfactionReport/ListCustomerSatisfactionReport', compact('relationcustomersatisfication', 'start_date', 'end_date'));
    }    

    public function filter(Request $request){
        $start_date     = $request->start_date;
        $end_date       = $request->end_date;

        Session::put('start_date', $start_date);
        Session::put('end_date', $end_date);

        return redirect('/customer-satisfaction-report');
    }  

    public function getNoAmount($customer_satisfaction_date){
        $amount = RelationCustomerSatisfaction::select('customer_satisfaction_status')
        ->where('customer_satisfaction_date', $customer_satisfaction_date)
        ->where('customer_satisfaction_status', 0)
        ->get();

        return count($amount);
    }

    public function getYesAmount($customer_satisfaction_date){
        $amount = RelationCustomerSatisfaction::select('customer_satisfaction_status')
        ->where('customer_satisfaction_date', $customer_satisfaction_date)
        ->where('customer_satisfaction_status', 1)
        ->get();

        return count($amount);
    }

    public function getUserName($user_id){
        $user = User::where('data_state', 0)
        ->where('user_id', $user_id)
        ->first();

        return $user['name'];
    }

    public function exportCustomerSatisfactionReport()
    {
        if(!Session::get('start_date')){
            $start_date     = date('Y-m-d');
        }else{
            $start_date     = Session::get('start_date');
        }

        if(!Session::get('end_date')){
            $end_date       = date('Y-m-d');
        }else{
            $end_date       = Session::get('end_date');
        }
        $spreadsheet = new Spreadsheet();

        $spreadsheet->getProperties()->setCreator("SIABAS")
                                        ->setLastModifiedBy("SIABAS")
                                        ->setTitle("Kepuasan Pelanggan")
                                        ->setSubject("")
                                        ->setDescription("Kepuasan Pelanggan")
                                        ->setKeywords("Kepuasan Pelanggan")
                                        ->setCategory("Kepuasan Pelanggan");
                                
        $sheet = $spreadsheet->getActiveSheet(0);
        $spreadsheet->getActiveSheet()->setTitle("Kepuasan Pelanggan");
        $spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
        $spreadsheet->getActiveSheet()->getPageSetup()->setFitToWidth(1);
        $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(5);

        $spreadsheet->getActiveSheet()->getStyle('B2')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
        $spreadsheet->getActiveSheet()->getStyle('B2')->getFont()->setBold(true)->setSize(16);
        
        $sheet->setCellValue('B2',"Kepuasan Pelanggan Periode ".date('d M Y', strtotime($start_date))." s.d. ".date('d M Y', strtotime($end_date)));	
        $sheet->setCellValue('B4',"No");
        $sheet->setCellValue('B5',"1.");

        $temp_date  = $start_date;
        $last_date  = date('Y-m-d', strtotime("+1 day", strtotime($end_date)));
        $n_total    = 0;
        $y_total    = 0;
        $row        = 3;

        while($temp_date != $last_date){
            $day        = date('d', strtotime($temp_date));
            $n_amount   = $this->getNoAmount($temp_date);
            $y_amount   = $this->getYesAmount($temp_date);
            $n_total   += $n_amount;
            $y_total   += $y_amount;

            $sheet->setCellValue(Coordinate::stringFromColumnIndex($row).'4',$day."_N");
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($row+1).'4',$day."_Y");

            $sheet->setCellValue(Coordinate::stringFromColumnIndex($row).'5', $n_amount);
            $sheet->setCellValue(Coordinate::stringFromColumnIndex($row+1).'5', $y_amount);

            $spreadsheet->getActiveSheet()->getColumnDimension(Coordinate::stringFromColumnIndex($row))->setWidth(8);
            $spreadsheet->getActiveSheet()->getColumnDimension(Coordinate::stringFromColumnIndex($row+1))->setWidth(8);

            $temp_date = date('Y-m-d', strtotime("+1 day", strtotime($temp_date)));
            $row      += 2;
        }

        $spreadsheet->getActiveSheet()->mergeCells("B2:".Coordinate::stringFromColumnIndex($row+2)."2");

        $spreadsheet->getActiveSheet()->getStyle("B4:".Coordinate::stringFromColumnIndex($row+2)."5")->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        $spreadsheet->getActiveSheet()->getStyle("B4:".Coordinate::stringFromColumnIndex($row+2)."5")->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        $spreadsheet->getActiveSheet()->getColumnDimension(Coordinate::stringFromColumnIndex($row))->setWidth(20);
        $spreadsheet->getActiveSheet()->getColumnDimension(Coordinate::stringFromColumnIndex($row+1))->setWidth(20);
        $spreadsheet->getActiveSheet()->getColumnDimension(Coordinate::stringFromColumnIndex($row+2))->setWidth(20);

        $sheet->setCellValue(Coordinate::stringFromColumnIndex($row).'4',"Total Tidak Puas");
        $sheet->setCellValue(Coordinate::stringFromColumnIndex($row+1).'4',"Total Puas");
        $sheet->setCellValue(Coordinate::stringFromColumnIndex($row+2).'4',"Total Kunjungan");

        $sheet->setCellValue(Coordinate::stringFromColumnIndex($row).'5', $n_total);
        $sheet->setCellValue(Coordinate::stringFromColumnIndex($row+1).'5', $y_total);
        $sheet->setCellValue(Coordinate::stringFromColumnIndex($row+2).'5', ($n_total+$y_total));
            
        // ob_clean();
        $filename='Kepuasan_Pelanggan_'.$start_date.'_s.d._'.$end_date.'.xls';
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$filename.'"');
        header('Cache-Control: max-age=0');

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');
        $writer->save('php://output');
    }
}
