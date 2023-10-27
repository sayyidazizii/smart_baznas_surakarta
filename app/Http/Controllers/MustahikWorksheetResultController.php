<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use App\Models\SystemLogUser;
use App\Models\MustahikWorksheetRequisition;
use App\Models\MustahikWorksheetResult;
use App\Models\MustahikWorksheet;
use App\Models\MustahikWorksheetItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Elibyy\TCPDF\Facades\TCPDF;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Str;

class MustahikWorksheetResultController extends Controller
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
    public function index(){
        Session::forget('sess_mustahikworksheetresult');
        Session::forget('sess_mustahikworksheetresultedit');
        if(!Session::get('start_date')){
            $start_date = date('Y-m-d');
        }else{
            $start_date = Session::get('start_date');
        }

        if(!Session::get('end_date')){
            $end_date   = date('Y-m-d');
            $stop_date  = date('Y-m-d', strtotime($end_date . ' +1 day'));
        }else{
            $end_date   = Session::get('end_date');
            $stop_date  = date('Y-m-d', strtotime($end_date . ' +1 day'));
        }

        $mustahikworksheetresult = MustahikWorksheetResult::select('mustahik_worksheet_result.worksheet_result_id', 'mustahik_worksheet_result.worksheet_result_date', 'trans_service_requisition.service_id', 'trans_service_requisition.service_requisition_no', 'trans_service_requisition.service_requisition_name', 'trans_service_requisition.created_at', 'core_service.service_name')
        ->join('mustahik_worksheet_requisition', 'mustahik_worksheet_requisition.worksheet_requisition_id', 'mustahik_worksheet_result.worksheet_requisition_id')
        ->join('trans_service_requisition', 'trans_service_requisition.service_requisition_id', 'mustahik_worksheet_requisition.service_requisition_id')
        ->join('core_service', 'core_service.service_id', 'trans_service_requisition.service_id')
        ->where('mustahik_worksheet_result.data_state', 0)
        ->where('trans_service_requisition.created_at','>=',$start_date)
        ->where('trans_service_requisition.created_at','<=',$stop_date)
        ->get();

        return view('content/MustahikWorksheetResult/ListMustahikWorksheetResult',compact('mustahikworksheetresult', 'start_date', 'end_date'));
    }

    public function filter(Request $request){
        $start_date     = $request->start_date;
        $end_date       = $request->end_date;

        Session::put('start_date', $start_date);
        Session::put('end_date', $end_date);

        return redirect('/mustahik-worksheet-result');
    }

    public function addReset($service_requisition_id){
        Session::forget('sess_servicerequisitiontokenedit');

        return redirect('/trans-service-disposition/add/'.$service_requisition_id);
    }

    public function set_log($user_id, $username, $id, $class, $pk, $remark){

		date_default_timezone_set("Asia/Jakarta");

		$log = array(
			'user_id'		=>	$user_id,
			'username'		=>	$username,
			'id_previllage'	=> 	$id,
			'class_name'	=>	$class,
			'pk'			=>	$pk,
			'remark'		=> 	$remark,
			'log_stat'		=>	'1',
			'log_time'		=>	date("Y-m-d G:i:s")
		);
		return SystemLogUser::create($log);
	}

    public function printMustahikWorksheetResult($worksheet_result_id){
        $worksheet = MustahikWorksheetResult::select('mustahik_worksheet_requisition.service_id')
        ->join('mustahik_worksheet_requisition', 'mustahik_worksheet_requisition.worksheet_requisition_id', 'mustahik_worksheet_result.worksheet_requisition_id')
        ->where('mustahik_worksheet_result.worksheet_result_id', $worksheet_result_id)
        ->first();

        if($worksheet['service_id'] == 7){
            $this->printMustahikWorksheetResultRLTH($worksheet_result_id);
        }else if($worksheet['service_id'] == 1){
            $this->printMustahikWorksheetResultMasjid($worksheet_result_id);
        }else if($worksheet['service_id'] == 6){
            $this->printMustahikWorksheetResultModalUsaha($worksheet_result_id);        
        }else{
            $msg = "Tidak Ada Format Mustahik Pelayanan ini";
            return redirect('/mustahik-worksheet-result')->with('msg',$msg);
        }
    }

    public function printMustahikWorksheetResultRLTH($worksheet_result_id){
        $mustahikworksheetresult = MustahikWorksheetResult::select('mustahik_worksheet_result.worksheet_result_data', 'mustahik_worksheet_result.worksheet_result_date', 'mustahik_worksheet_result.user_id', 'mustahik_worksheet_requisition.service_id', 'mustahik_worksheet_requisition.worksheet_requisition_id')
        ->join('mustahik_worksheet_requisition', 'mustahik_worksheet_requisition.worksheet_requisition_id', 'mustahik_worksheet_result.worksheet_requisition_id')
        ->where('mustahik_worksheet_result.worksheet_result_id', $worksheet_result_id)
        ->first();

        $mustahikworksheet = MustahikWorksheet::select('worksheet_id', 'worksheet_no', 'worksheet_name', 'worksheet_type', 'worksheet_code')
        ->where('service_id', $mustahikworksheetresult['service_id'])
        ->get();

        $worksheetresultdata = json_decode($mustahikworksheetresult['worksheet_result_data']);

        $data = array();

        foreach($mustahikworksheet as $key => $val){
            if($val['worksheet_type'] == 1 || $val['worksheet_type'] == 3 || $val['worksheet_type'] == 4){
                $data[$val['worksheet_code']] = '';
            }else if($val['worksheet_type'] == 2){
                $data[$val['worksheet_code']] = array();
                $mustahikworksheetitem = MustahikWorksheetItem::select('worksheet_item_id', 'worksheet_id', 'section_name', 'worksheet_item_name', 'worksheet_item_code')
                ->where('worksheet_id', $val['worksheet_id'])
                ->get();

                foreach($mustahikworksheetitem as $keyy => $vall){
                    $data[$val['worksheet_code']][$vall['worksheet_item_code']] = '';
                }
            }
        }

        foreach($data as $key => $val){
            foreach($worksheetresultdata as $keyy => $vall){
                if($key == $vall->worksheet_code){
                    if(isset($vall->value)){
                        $data[$key] = $vall->value;
                    }else if(isset($vall->worksheetitem)){
                        foreach($vall->worksheetitem as $keyyy => $valll){
                            $data[$key][$valll->worksheet_item_code] = $valll->value;
                        }
                    }
                }
            }
        }

        // print_r($data);exit;

        $username = User::select('name', 'full_name')
        ->where('user_id','=',Auth::id())
        ->first();

        $surveyor = User::select('name', 'full_name')
        ->where('user_id','=',$mustahikworksheetresult['user_id'])
        ->first();

        $this->set_log(Auth::id(), $username['name'],'1089','Application.MustahikWorksheetResult.print',$username['name'],'Print');


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

        $path = public_path('resources/img/logosmart/logobaznas01.png');

        $export = "
        <br></br>
        <div style=\"text-align:center;\">
            <br/>
            <br/>
            <br/>
            <br/>
            <br/>
            <br/>
        </div>
        
        <table cellspacing=\"0\" cellpadding=\"2\" border=\"1\">
            <tr width = \"100%\" style=\"background-color: #bababa;\">
                <td width=\"100%\" colspan='3'><div style=\"text-align: center;\"><a style=\"color:black; font-weight:bold; text-decoration:none;\">FORM SURVEY RENOVASI RUMAH TIDAK LAYAK HUNI</a></div></td>
            </tr>
            <tr width = \"100%\">
                <td width=\"20%\"><div style=\"text-align: left; font-weight: bold;\">Nama Pengaju</div></td>
                <td width=\"3%\"><div style=\"text-align: left; font-weight: bold;\">:</div></td>
                <td width=\"77%\"><div style=\"text-align: left;\">".$data['service_requisition_name']."</div></td>
            </tr>
            <tr width = \"100%\" >
                <td width=\"20%\"><div style=\"text-align: left; font-weight: bold;\">Nomor Telepon / HP</div></td>
                <td width=\"3%\"><div style=\"text-align: left; font-weight: bold;\">:</div></td>
                <td width=\"77%\"><div style=\"text-align: left;\">".$data['service_requisition_phone']."</div></td>
            </tr>
            <tr width = \"100%\" >
                <td width=\"20%\"><div style=\"text-align: left; font-weight: bold;\">Alamat</div></td>
                <td width=\"3%\"><div style=\"text-align: left; font-weight: bold;\">:</div></td>
                <td width=\"77%\"><div style=\"text-align: left;\">".$data['service_requisition_address']."</div></td>
            </tr>
        </table>
        
        <br/>
        <br/>
        <table>
            <tr>
                <td width = \"65%\">
                    <table cellspacing=\"0\" cellpadding=\"1\" border=\"1\" style=\"float:left; width:500px\">
                        <tr width = \"100%\">";
                        if($data['worksheet_application_letter'] == true){
                            $export .="<td width = \"10%\" style=\"text-align: center; font-weight: bold;\"> <span style=\"font-family:zapfdingbats;\">4</span></td>";
                        }else{
                            $export .="<td width = \"10%\" style=\"text-align: center; font-weight: bold;\"></td>";
                        }
                            $export .="<td width = \"80%\">Surat Permohonan</td>
                        </tr>
                        <tr width = \"100%\">";
                        if($data['worksheet_sktm'] == true){
                            $export .="<td width = \"10%\" style=\"text-align: center; font-weight: bold;\"> <span style=\"font-family:zapfdingbats;\">4</span></td>";
                        }else{
                            $export .="<td width = \"10%\" style=\"text-align: center; font-weight: bold;\"></td>";
                        }
                            $export .="
                            <td width = \"80%\">Surat Keterangan Tidak Mampu / Fotocopy Kartu Saraswati</td>
                        </tr>
                        <tr width = \"100%\">";
                        if($data['worksheet_ktp'] == true){
                            $export .="<td width = \"10%\" style=\"text-align: center; font-weight: bold;\"> <span style=\"font-family:zapfdingbats;\">4</span></td>";
                        }else{
                            $export .="<td width = \"10%\" style=\"text-align: center; font-weight: bold;\"></td>";
                        }
                            $export .="
                            <td width = \"80%\">Fotocopy KTP Pemohon</td>
                        </tr>
                        <tr width = \"100%\">";
                        if($data['worksheet_kk'] == true){
                            $export .="<td width = \"10%\" style=\"text-align: center; font-weight: bold;\"> <span style=\"font-family:zapfdingbats;\">4</span></td>";
                        }else{
                            $export .="<td width = \"10%\" style=\"text-align: center; font-weight: bold;\"></td>";
                        }
                            $export .="
                            <td width = \"80%\">Fotocopy KK Permohonan</td>
                        </tr>
                        <tr width = \"100%\">";
                        if($data['worksheet_ownership_document'] == true){
                            $export .="<td width = \"10%\" style=\"text-align: center; font-weight: bold;\"> <span style=\"font-family:zapfdingbats;\">4</span></td>";
                        }else{
                            $export .="<td width = \"10%\" style=\"text-align: center; font-weight: bold;\"></td>";
                        }
                            $export .="
                            <td width = \"80%\">Dokumen Kepemilikan Rumah / Fotocopy SHM</td>
                        </tr>
                        <tr width = \"100%\">";
                        if($data['worksheet_occupancy_agreement'] == true){
                            $export .="<td width = \"10%\" style=\"text-align: center; font-weight: bold;\"> <span style=\"font-family:zapfdingbats;\">4</span></td>";
                        }else{
                            $export .="<td width = \"10%\" style=\"text-align: center; font-weight: bold;\"></td>";
                        }
                            $export .="
                            <td width = \"80%\">Surat Perjanjian Menempati Lahan(Format Dari BAZNAS)(Magersari)</td>
                        </tr>
                        <tr width = \"100%\">";
                        if($data['worksheet_land_owner'] == true){
                            $export .="<td width = \"10%\" style=\"text-align: center; font-weight: bold;\"> <span style=\"font-family:zapfdingbats;\">4</span></td>";
                        }else{
                            $export .="<td width = \"10%\" style=\"text-align: center; font-weight: bold;\"></td>";
                        }
                            $export .="
                            <td width = \"80%\">Fotocopy KTP Pemilik Lahan(Magersari)</td>
                        </tr>

                    </table>
                </td>
                <td width = \"35%\">
                    <table cellspacing=\"0\" cellpadding=\"2\" border=\"2\" style=\"float:right; width:240px\">
                        <tr>
                            <th style=\"text-align: center; font-weight: bold;\">Jumlah Jiwa</th>
                            <th style=\"text-align: center; font-weight: bold;\">Kode Ashnaf</th>
                        </tr>
                        <tr>
                            <td>
                                <br/>
                                <br/>
                                <div style=\"text-align: center; font-weight: bold; font-size: 25\">".$data['worksheet_occupant']."</div>
                                <br/>
                                <br/>
                            </td>
                            <td>
                                <br/>
                                <br/>";
                                foreach($data['worksheet_ashnaf'] as $key => $val){
                                    if($key == 'fakir_ashnaf'){
                                        if($val == true){
                                            $ashnaf = "<div style=\"text-align: center; font-weight: bold; font-size: 25\">Fakir</div>";
                                        }
                                    }
                                    if($key == 'poor_ashnaf'){
                                        if($val == true){
                                            $ashnaf = "<div style=\"text-align: center; font-weight: bold; font-size: 25\">Miskin</div>";
                                        }
                                    }
                                }
                                $export .= $ashnaf;
                                $export .= "
                                <br/>
                                <br/>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <br>
        <br>
        <table cellspacing=\"0\" cellpadding=\"2\" border=\"1\">
            <tr width = \"100%\" style=\"background-color: #bababa;\">
                <td width=\"5%\"><div style=\"text-align: center;\"><a style=\"color:black; font-weight:bold; text-decoration:none;\">No</a></div></td>
                <td width=\"20%\"><div style=\"text-align: center;\"><a style=\"color:black; font-weight:bold; text-decoration:none;\">Indeks Rumah</a></div></td>
                <td width=\"30%\"><div style=\"text-align: center;\"><a style=\"color:black; font-weight:bold; text-decoration:none;\">Jenis Bangunan</a></div></td>
                <td width=\"20%\"><div style=\"text-align: center;\"><a style=\"color:black; font-weight:bold; text-decoration:none;\">Kondisi Bangunan</a></div></td>
                <td width=\"25%\"><div style=\"text-align: center;\"><a style=\"color:black; font-weight:bold; text-decoration:none;\">Catatan</a></div></td>
            </tr>
            <tr width = \"100%\">
                <td width=\"5%\"><div style=\"text-align: center;\">1</div></td>
                <td width=\"20%\"><div style=\"text-align: left;\">Ukuran Rumah</div></td>
                <td width=\"30%\">".$data['worksheet_home_size_type'].' m2'."</td>
                <td width=\"20%\">".$data['worksheet_home_size_condition']."</td>
                <td width=\"25%\"><div style=\"text-align: left;\">".$data['worksheet_home_size_remark']."</div></td>
            </tr>
            <tr width = \"100%\" >
                <td width=\"5%\"><div style=\"text-align: center;\">2</div></td>
                <td width=\"20%\"><div style=\"text-align: left;\">Dinding Rumah</div></td>
                <td width=\"30%\">";
                $no = 0;
                foreach($data['worksheet_wall'] as $key => $val){
                    if($key != 'wall_decent' && $key != 'wall_not_decent'){
                        $item_name = MustahikWorksheetItem::select('worksheet_item_name')
                        ->where('worksheet_item_code', $key)
                        ->first();
                        if($no == 2){
                            if($val == true){
                                $export .= "<br><input type=\"checkbox\" name=\"box\" value=\"1\" readonly=\"true\" checked=\"checked\"/>".$item_name['worksheet_item_name'];
                            }else{
                                $export .= "<br><input type=\"checkbox\" name=\"box\" value=\"1\" readonly=\"true\"/>".$item_name['worksheet_item_name'];
                            }
                        }else{
                            if($val == true){
                                $export .= "<input type=\"checkbox\" name=\"box\" value=\"1\" readonly=\"true\" checked=\"checked\"/>".$item_name['worksheet_item_name'];
                            }else{
                                $export .= "<input type=\"checkbox\" name=\"box\" value=\"1\" readonly=\"true\"/>".$item_name['worksheet_item_name'];
                            }
                        }
                        $no++;
                    }
                }
                $export .= "
                </td>
                <td width=\"20%\">";
                    foreach($data['worksheet_wall'] as $key => $val){
                        if($key == 'wall_decent'){
                            if($val == true){
                                $export .= "<input type=\"checkbox\" name=\"box\" value=\"1\" readonly=\"true\" checked=\"checked\"/>Layak<br>";
                            } else {
                                $export .= "<input type=\"checkbox\" name=\"box\" value=\"1\" readonly=\"true\"/>Layak<br>";
                            }
                        }
                        if($key == 'wall_not_decent'){
                            if($val == true){
                                $export .= "<input type=\"checkbox\" name=\"box\" value=\"1\" readonly=\"true\" checked=\"checked\"/>Tidak Layak";
                            } else {
                                $export .= "<input type=\"checkbox\" name=\"box\" value=\"1\" readonly=\"true\"/>Tidak Layak";
                            }
                        }
                    }
                $export .= 
                "</td>
                <td width=\"25%\"><div style=\"text-align: left;\">".$data['worksheet_wall_remark']."</div></td>
            </tr>
            <tr width = \"100%\" >
                <td width=\"5%\"><div style=\"text-align: center;\">3</div></td>
                <td width=\"20%\"><div style=\"text-align: left;\">Lantai</div></td>
                <td width=\"30%\">";
                $no = 0;
                foreach($data['worksheet_floor'] as $key => $val){
                    if($key != 'floor_decent' && $key != 'floor_not_decent'){
                        $item_name = MustahikWorksheetItem::select('worksheet_item_name')
                        ->where('worksheet_item_code', $key)
                        ->first();
                        if($no == 2){
                            if($val == true){
                                $export .= "<br><input type=\"checkbox\" name=\"box\" value=\"1\" readonly=\"true\" checked=\"checked\"/>".$item_name['worksheet_item_name'];
                            }else{
                                $export .= "<br><input type=\"checkbox\" name=\"box\" value=\"1\" readonly=\"true\"/>".$item_name['worksheet_item_name'];
                            }
                        }else{
                            if($val == true){
                                $export .= "<input type=\"checkbox\" name=\"box\" value=\"1\" readonly=\"true\" checked=\"checked\"/>".$item_name['worksheet_item_name'];
                            }else{
                                $export .= "<input type=\"checkbox\" name=\"box\" value=\"1\" readonly=\"true\"/>".$item_name['worksheet_item_name'];
                            }
                        }
                        $no++;
                    }
                }
                $export .= "
                </td>
                <td width=\"20%\">";
                foreach($data['worksheet_floor'] as $key => $val){
                    if($key == 'floor_decent'){
                        if($val == true){
                            $export .= "<input type=\"checkbox\" name=\"box\" value=\"1\" readonly=\"true\" checked=\"checked\"/>Layak<br>";
                        } else {
                            $export .= "<input type=\"checkbox\" name=\"box\" value=\"1\" readonly=\"true\"/>Layak<br>";
                        }
                    }
                    if($key == 'floor_not_decent'){
                        if($val == true){
                            $export .= "<input type=\"checkbox\" name=\"box\" value=\"1\" readonly=\"true\" checked=\"checked\"/>Tidak Layak";
                        } else {
                            $export .= "<input type=\"checkbox\" name=\"box\" value=\"1\" readonly=\"true\"/>Tidak Layak";
                        }
                    }
                }
            $export .= 
            "</td>
                <td width=\"25%\"><div style=\"text-align: left;\">".$data['worksheet_floor_remark']."</div></td>
            </tr>
            <tr width = \"100%\" >
                <td width=\"5%\"><div style=\"text-align: center;\">4</div></td>
                <td width=\"20%\"><div style=\"text-align: left;\">Atap</div></td>
                <td width=\"30%\">";
                $no = 0;
                foreach($data['worksheet_roof'] as $key => $val){
                    if($key != 'roof_decent' && $key != 'roof_not_decent'){
                        $item_name = MustahikWorksheetItem::select('worksheet_item_name')
                        ->where('worksheet_item_code', $key)
                        ->first();
                        if($no == 2){
                            if($val == true){
                                $export .= "<br><input type=\"checkbox\" name=\"box\" value=\"1\" readonly=\"true\" checked=\"checked\"/>".$item_name['worksheet_item_name'];
                            }else{
                                $export .= "<br><input type=\"checkbox\" name=\"box\" value=\"1\" readonly=\"true\"/>".$item_name['worksheet_item_name'];
                            }
                        }else{
                            if($val == true){
                                $export .= "<input type=\"checkbox\" name=\"box\" value=\"1\" readonly=\"true\" checked=\"checked\"/>".$item_name['worksheet_item_name'];
                            }else{
                                $export .= "<input type=\"checkbox\" name=\"box\" value=\"1\" readonly=\"true\"/>".$item_name['worksheet_item_name'];
                            }
                        }
                        $no++;
                    }
                }
                $export .= "
                </td>
                <td width=\"20%\">";
                foreach($data['worksheet_roof'] as $key => $val){
                    if($key == 'roof_decent'){
                        if($val == true){
                            $export .= "<input type=\"checkbox\" name=\"box\" value=\"1\" readonly=\"true\" checked=\"checked\"/>Layak<br>";
                        } else {
                            $export .= "<input type=\"checkbox\" name=\"box\" value=\"1\" readonly=\"true\"/>Layak<br>";
                        }
                    }
                    if($key == 'roof_not_decent'){
                        if($val == true){
                            $export .= "<input type=\"checkbox\" name=\"box\" value=\"1\" readonly=\"true\" checked=\"checked\"/>Tidak Layak";
                        } else {
                            $export .= "<input type=\"checkbox\" name=\"box\" value=\"1\" readonly=\"true\"/>Tidak Layak";
                        }
                    }
                }
            $export .= 
            "</td>
                <td width=\"25%\"><div style=\"text-align: left;\">".$data['worksheet_roof_remark']."</div></td>
            </tr>
            <tr width = \"100%\" >
                <td width=\"5%\"><div style=\"text-align: center;\">5</div></td>
                <td width=\"20%\"><div style=\"text-align: left;\">Sanitasi</div></td>
                <td width=\"30%\">";
                $no = 0;
                foreach($data['worksheet_sanitation'] as $key => $val){
                    if($key != 'sanitation_decent' && $key != 'sanitation_not_decent'){
                        $item_name = MustahikWorksheetItem::select('worksheet_item_name')
                        ->where('worksheet_item_code', $key)
                        ->first();
                        if($no == 2){
                            if($val == true){
                                $export .= "<br><input type=\"checkbox\" name=\"box\" value=\"1\" readonly=\"true\" checked=\"checked\"/>".$item_name['worksheet_item_name'];
                            }else{
                                $export .= "<br><input type=\"checkbox\" name=\"box\" value=\"1\" readonly=\"true\"/>".$item_name['worksheet_item_name'];
                            }
                        }else{
                            if($val == true){
                                $export .= "<input type=\"checkbox\" name=\"box\" value=\"1\" readonly=\"true\" checked=\"checked\"/>".$item_name['worksheet_item_name'];
                            }else{
                                $export .= "<input type=\"checkbox\" name=\"box\" value=\"1\" readonly=\"true\"/>".$item_name['worksheet_item_name'];
                            }
                        }
                        $no++;
                    }
                }
                $export .= "
                </td>
                <td width=\"20%\">";
                foreach($data['worksheet_sanitation'] as $key => $val){
                    if($key == 'sanitation_decent'){
                        if($val == true){
                            $export .= "<input type=\"checkbox\" name=\"box\" value=\"1\" readonly=\"true\" checked=\"checked\"/>Layak<br>";
                        } else {
                            $export .= "<input type=\"checkbox\" name=\"box\" value=\"1\" readonly=\"true\"/>Layak<br>";
                        }
                    }
                    if($key == 'sanitation_not_decent'){
                        if($val == true){
                            $export .= "<input type=\"checkbox\" name=\"box\" value=\"1\" readonly=\"true\" checked=\"checked\"/>Tidak Layak";
                        } else {
                            $export .= "<input type=\"checkbox\" name=\"box\" value=\"1\" readonly=\"true\"/>Tidak Layak";
                        }
                    }
                }
            $export .= 
            "</td>
                <td width=\"25%\"><div style=\"text-align: left;\">".$data['worksheet_sanitation_remark']."</div></td>
            </tr>
            <tr width = \"100%\" >
                <td width=\"5%\"><div style=\"text-align: center;\">6</div></td>
                <td width=\"20%\"><div style=\"text-align: left;\">Listrik</div></td>
                <td width=\"30%\">";
                $no = 0;
                foreach($data['worksheet_electricity'] as $key => $val){
                    if($key != 'electricity_decent' && $key != 'electricity_not_decent'){
                        $item_name = MustahikWorksheetItem::select('worksheet_item_name')
                        ->where('worksheet_item_code', $key)
                        ->first();
                        if($no == 2){
                            if($val == true){
                                $export .= "<br><input type=\"checkbox\" name=\"box\" value=\"1\" readonly=\"true\" checked=\"checked\"/>".$item_name['worksheet_item_name'];
                            }else{
                                $export .= "<br><input type=\"checkbox\" name=\"box\" value=\"1\" readonly=\"true\"/>".$item_name['worksheet_item_name'];
                            }
                        }else{
                            if($val == true){
                                $export .= "<input type=\"checkbox\" name=\"box\" value=\"1\" readonly=\"true\" checked=\"checked\"/>".$item_name['worksheet_item_name'];
                            }else{
                                $export .= "<input type=\"checkbox\" name=\"box\" value=\"1\" readonly=\"true\"/>".$item_name['worksheet_item_name'];
                            }
                        }
                        $no++;
                    }
                }
                $export .= "
                </td>
                <td width=\"20%\">";
                foreach($data['worksheet_electricity'] as $key => $val){
                    if($key == 'electricity_decent'){
                        if($val == true){
                            $export .= "<input type=\"checkbox\" name=\"box\" value=\"1\" readonly=\"true\" checked=\"checked\"/>Layak<br>";
                        } else {
                            $export .= "<input type=\"checkbox\" name=\"box\" value=\"1\" readonly=\"true\"/>Layak<br>";
                        }
                    }
                    if($key == 'electricity_not_decent'){
                        if($val == true){
                            $export .= "<input type=\"checkbox\" name=\"box\" value=\"1\" readonly=\"true\" checked=\"checked\"/>Tidak Layak";
                        } else {
                            $export .= "<input type=\"checkbox\" name=\"box\" value=\"1\" readonly=\"true\"/>Tidak Layak";
                        }
                    }
                }
            $export .= 
            "</td>
                <td width=\"25%\"><div style=\"text-align: left;\">".$data['worksheet_electricity_remark']."</div></td>
            </tr>
            <tr width = \"100%\" >
                <td width=\"5%\"><div style=\"text-align: center;\">7</div></td>
                <td width=\"20%\"><div style=\"text-align: left;\">Kepemilikan Rumah</div></td>
                <td width=\"30%\">";
                $no = 0;
                foreach($data['worksheet_ownership'] as $key => $val){
                    if($key != 'ownership_decent' && $key != 'ownership_not_decent'){
                        $item_name = MustahikWorksheetItem::select('worksheet_item_name')
                        ->where('worksheet_item_code', $key)
                        ->first();
                        if($no == 2){
                            if($val == true){
                                $export .= "<br><input type=\"checkbox\" name=\"box\" value=\"1\" readonly=\"true\" checked=\"checked\"/>".$item_name['worksheet_item_name'];
                            }else{
                                $export .= "<br><input type=\"checkbox\" name=\"box\" value=\"1\" readonly=\"true\"/>".$item_name['worksheet_item_name'];
                            }
                        }else{
                            if($val == true){
                                $export .= "<input type=\"checkbox\" name=\"box\" value=\"1\" readonly=\"true\" checked=\"checked\"/>".$item_name['worksheet_item_name'];
                            }else{
                                $export .= "<input type=\"checkbox\" name=\"box\" value=\"1\" readonly=\"true\"/>".$item_name['worksheet_item_name'];
                            }
                        }
                        $no++;
                    }
                }
                $export .= "
                </td>
                <td width=\"20%\">";
                foreach($data['worksheet_ownership'] as $key => $val){
                    if($key == 'ownership_decent'){
                        if($val == true){
                            $export .= "<input type=\"checkbox\" name=\"box\" value=\"1\" readonly=\"true\" checked=\"checked\"/>Layak<br>";
                        } else {
                            $export .= "<input type=\"checkbox\" name=\"box\" value=\"1\" readonly=\"true\"/>Layak<br>";
                        }
                    }
                    if($key == 'ownership_not_decent'){
                        if($val == true){
                            $export .= "<input type=\"checkbox\" name=\"box\" value=\"1\" readonly=\"true\" checked=\"checked\"/>Tidak Layak";
                        } else {
                            $export .= "<input type=\"checkbox\" name=\"box\" value=\"1\" readonly=\"true\"/>Tidak Layak";
                        }
                    }
                }
            $export .= 
            "</td>
                <td width=\"25%\"><div style=\"text-align: left;\">".$data['worksheet_ownership_remark']."</div></td>
            </tr>
        </table>
        <br>
        <br>
        <table cellspacing=\"0\" cellpadding=\"2\" border=\"1\">
            <tr width = \"100%\" style=\"background-color: #bababa;\">
                <td width=\"5%\"><div style=\"text-align: center;\"><a style=\"color:black; font-weight:bold; text-decoration:none;\">No</a></div></td>
                <td width=\"25%\"><div style=\"text-align: center;\"><a style=\"color:black; font-weight:bold; text-decoration:none;\">Pendapatan Keluarga</a></div></td>
                <td width=\"33%\"><div style=\"text-align: center;\"><a style=\"color:black; font-weight:bold; text-decoration:none;\">Bulanan</a></div></td>
                <td width=\"37%\"><div style=\"text-align: center;\"><a style=\"color:black; font-weight:bold; text-decoration:none;\">Total</a></div></td>
            </tr>
            <tr width = \"100%\">
                <td width=\"5%\"><div style=\"text-align: center;\">1</div></td>
                <td width=\"25%\"><div style=\"text-align: left;\">Usaha pokok suami</div></td>
                <td width=\"25%\"><div style=\"text-align: left;\">Rp ".number_format((int)$data['worksheet_husband_business_monthly'],2)."</div></td>
                <td width=\"8%\"><div style=\"text-align: left; \">/ Bulan</div></td>
                <td width=\"29%\"><div style=\"text-align: left;\">Rp ".number_format((int)$data['worksheet_husband_business_yearly'],2)."</div></td>
                <td width=\"8%\"><div style=\"text-align: left;\">/ Tahun</div></td>
            </tr>
            <tr width = \"100%\">
                <td width=\"5%\"><div style=\"text-align: center;\">2</div></td>
                <td width=\"25%\"><div style=\"text-align: left;\">Usaha pokok istri</div></td>
                <td width=\"25%\"><div style=\"text-align: left;\">Rp ".number_format((int)$data['worksheet_wife_business_monthly'],2)."</div></td>
                <td width=\"8%\"><div style=\"text-align: left; \">/ Bulan</div></td>
                <td width=\"29%\"><div style=\"text-align: left;\">Rp ".number_format((int)$data['worksheet_wife_business_yearly'],2)."</div></td>
                <td width=\"8%\"><div style=\"text-align: left;\">/ Tahun</div></td>
            </tr>
            <tr width = \"100%\">
                <td width=\"5%\"><div style=\"text-align: center;\">4</div></td>
                <td width=\"25%\"><div style=\"text-align: left;\">Usaha dari orang tua</div></td>
                <td width=\"25%\"><div style=\"text-align: left;\">Rp ".number_format((int)$data['worksheet_parents_monthly'],2)."</div></td>
                <td width=\"8%\"><div style=\"text-align: left; \">/ Bulan</div></td>
                <td width=\"29%\"><div style=\"text-align: left;\">Rp ".number_format((int)$data['worksheet_parents_yearly'],2)."</div></td>
                <td width=\"8%\"><div style=\"text-align: left;\">/ Tahun</div></td>
            </tr>
            <tr width = \"100%\">
                <td width=\"5%\"><div style=\"text-align: center;\">5</div></td>
                <td width=\"25%\"><div style=\"text-align: left;\">Dari anak / menantu</div></td>
                <td width=\"25%\"><div style=\"text-align: left;\">Rp ".number_format((int)$data['worksheet_childs_monthly'],2)."</div></td>
                <td width=\"8%\"><div style=\"text-align: left; \">/ Bulan</div></td>
                <td width=\"29%\"><div style=\"text-align: left;\">Rp ".number_format((int)$data['worksheet_childs_yearly'],2)."</div></td>
                <td width=\"8%\"><div style=\"text-align: left;\">/ Tahun</div></td>
            </tr>
            <tr width = \"100%\">
                <td width=\"5%\"><div style=\"text-align: center;\">6</div></td>
                <td width=\"25%\"><div style=\"text-align: left;\">Penghasilan lainnya</div></td>
                <td width=\"25%\"><div style=\"text-align: left;\">Rp ".number_format((int)$data['worksheet_other_monthly'],2)."</div></td>
                <td width=\"8%\"><div style=\"text-align: left; \">/ Bulan</div></td>
                <td width=\"29%\"><div style=\"text-align: left;\">Rp ".number_format((int)$data['worksheet_other_yearly'],2)."</div></td>
                <td width=\"8%\"><div style=\"text-align: left;\">/ Tahun</div></td>
            </tr>
        </table>
        <br>
        <br>
        <table cellspacing=\"0\" cellpadding=\"2\" border=\"1\">
        <tr width = \"100%\" style=\"background-color: #bababa;\">
            <td width=\"100%\"><div style=\"text-align: center;\"><a style=\"color:black; font-weight:bold; text-decoration:none;\">Catatan Surveyor</a></div></td>
        </tr>
        <tr width = \"100%\">
            <td width=\"100%\"><div style=\"text-align: left;\"><a style=\"color:black; text-decoration:none;\">".$data['surveyor_remark']."</a></div></td>
        </tr>
        </table>
        <br>
        <br>
        <table cellspacing=\"0\" cellpadding=\"2\" border=\"0\">
            <tr width = \"100%\">
                <td width=\"65%\"><div style=\"text-align: center;\"><a style=\"color:black; text-decoration:none;\"></a></div></td>
                <td width=\"35%\"><div style=\"text-align: center;\"><a style=\"color:black; text-decoration:none;\">Surakarta,".date('d-m-Y', strtotime($mustahikworksheetresult['worksheet_result_date']))."</a></div></td>
            </tr>
            <tr width = \"100%\">
                <td width=\"35%\"><div style=\"text-align: center;\"><a style=\"color:black; text-decoration:none;\">Wakil Ketua II</a></div></td>
                <td width=\"30%\"><div style=\"text-align: center;\"><a style=\"color:black; text-decoration:none;\">Kepala Pelaksana</a></div></td>
                <td width=\"35%\"><div style=\"text-align: center;\"><a style=\"color:black; text-decoration:none;\">Petugas Survei</a></div></td>
            </tr>
            <tr width = \"100%\">
                <td width=\"35%\"><div style=\"text-align: center;\"></div>
                    <br/>
                    <div style=\"text-align: center;\">Drs. Sarwaka</div>
                </td>
                <td width=\"30%\"><div style=\"text-align: center;\"></div>
                    <br/>
                    <div style=\"text-align: center;\">Dewi Purwantiningsih, SE</div>
                </td>
                <td width=\"35%\"><div style=\"text-align: center;\"></div>
                    <br/>
                    <div style=\"text-align: center;\">".$surveyor['full_name']."</div>
                </td>
            </tr>
        </table>
        ";

        $pdf::writeHTML($export, true, false, false, false, '');
        
        $pdf::Image( $path, 4, 4, 25, 25, 'PNG', '', 'LT', false, 300, 'C', false, false, 1, false, false, false);
        
        $path = public_path('storage/worksheet-image/'.$mustahikworksheetresult['worksheet_requisition_id'].'/');
        $exportattachment = "
        <br pagebreak=\"true\"/>
        <div width=\"100%\" style=\"text-align: center; font-weight:bold; font-size:14;\">Foto Rumah Atas Nama ".$data['worksheet_photos_name']."</div>
        <br>
        <div style=\"font-size:12;\">Tampak Depan</div>
        <br>
        <table cellspacing=\"0\" cellpadding=\"2\" border=\"1\">
            <tr width = \"100%\">
                <td width=\"100%\"><div style=\"text-align: center;\"></div>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                </td>
            </tr>
        </table>
        <br>
        <div style=\"font-size:12;\">Tampak Samping Kanan</div>
        <br>
        <table cellspacing=\"0\" cellpadding=\"2\" border=\"1\">
            <tr width = \"100%\">
                <td width=\"100%\"><div style=\"text-align: center;\"></div>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                </td>
            </tr>
        </table>
        ";
        $pdf::writeHTML($exportattachment, true, false, false, false, '');

        $path = public_path('storage/worksheet-image/'.$mustahikworksheetresult['worksheet_requisition_id'].'/');
        if($data['worksheet_photos_front'] != ''){
            $pdf::Image($path.$data['worksheet_photos_front'], 20, 36, 100, 100, '', '', 'LT', false, 300, 'C', false, false, 1, false, false, false);
        }
        if($data['worksheet_photos_right'] != ''){
            $pdf::Image($path.$data['worksheet_photos_right'], 20, 153, 100, 100, '', '', 'LT', false, 300, 'C', false, false, 1, false, false, false);
        }
        // $worksheet_photos_front = base64_decode(preg_replace('#^data:image/[^;]+;base64,#', '', $data['worksheet_photos_front']));
        // $pdf::Image( "@".$worksheet_photos_front, 20, 36, 100, 100, '', '', 'LT', false, 300, 'C', false, false, 1, false, false, false);
        
        $exportattachment2 = "
        <br pagebreak=\"true\"/>
        <div width=\"100%\" style=\"text-align: center; font-weight:bold; font-size:14;\">Foto Rumah</div>
        <br>
        <div style=\"font-size:12;\">Tampak Samping Kiri</div>
        <br>
        <table cellspacing=\"0\" cellpadding=\"2\" border=\"1\">
            <tr width = \"100%\">
                <td width=\"100%\"><div style=\"text-align: center;\"></div>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                </td>
            </tr>
        </table>
        <br>
        <div style=\"font-size:12;\">Tampak Belakang</div>
        <br>
        <table cellspacing=\"0\" cellpadding=\"2\" border=\"1\">
            <tr width = \"100%\">
                <td width=\"100%\"><div style=\"text-align: center;\"></div>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                </td>
            </tr>
        </table>
        ";

        $pdf::writeHTML($exportattachment2, true, false, false, false, '');
        if($data['worksheet_photos_left'] != ''){
            $pdf::Image($path.$data['worksheet_photos_left'], 20, 36, 100, 100, '', '', 'LT', false, 300, 'C', false, false, 1, false, false, false);
        }
        if($data['worksheet_photos_back'] != ''){
            $pdf::Image($path.$data['worksheet_photos_back'], 20, 153, 100, 100, '', '', 'LT', false, 300, 'C', false, false, 1, false, false, false);
        }
        
        $exportattachment3 = "
        <br pagebreak=\"true\"/>
        <div width=\"100%\" style=\"text-align: center; font-weight:bold; font-size:14;\">Foto Rumah</div>
        <br>
        <div style=\"font-size:12;\">Tampak Bagian Dalam</div>
        <br>
        <table cellspacing=\"0\" cellpadding=\"2\" border=\"1\">
            <tr width = \"100%\">
                <td width=\"100%\"><div style=\"text-align: center;\"></div>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                </td>
            </tr>
        </table>
        <br>
        <div style=\"font-size:12;\">Tampak Bagian MCK</div>
        <br>
        <table cellspacing=\"0\" cellpadding=\"2\" border=\"1\">
            <tr width = \"100%\">
                <td width=\"100%\"><div style=\"text-align: center;\"></div>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                </td>
            </tr>
        </table>
        ";

        $pdf::writeHTML($exportattachment3, true, false, false, false, '');
        if($data['worksheet_photos_inside'] != ''){
            $pdf::Image($path.$data['worksheet_photos_inside'], 20, 36, 100, 100, '', '', 'LT', false, 300, 'C', false, false, 1, false, false, false);
        }
        if($data['worksheet_photos_mck'] != ''){
            $pdf::Image($path.$data['worksheet_photos_mck'], 20, 153, 100, 100, '', '', 'LT', false, 300, 'C', false, false, 1, false, false, false);
        }

        
        $exportattachment4 = "
        <br pagebreak=\"true\"/>
        <div width=\"100%\" style=\"text-align: center; font-weight:bold; font-size:14;\">Foto Rumah</div>
        <br>
        <div style=\"font-size:12;\">Gambar Lainnya 1</div>
        <br>
        <table cellspacing=\"0\" cellpadding=\"2\" border=\"1\">
            <tr width = \"100%\">
                <td width=\"100%\"><div style=\"text-align: center;\"></div>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                </td>
            </tr>
        </table>
        <br>
        <div style=\"font-size:12;\">Gambar Lainnya 2</div>
        <br>
        <table cellspacing=\"0\" cellpadding=\"2\" border=\"1\">
            <tr width = \"100%\">
                <td width=\"100%\"><div style=\"text-align: center;\"></div>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                </td>
            </tr>
        </table>
        ";

        $pdf::writeHTML($exportattachment4, true, false, false, false, '');
        if($data['other_image_1'] != ''){
            $pdf::Image($path.$data['other_image_1'], 20, 36, 100, 100, '', '', 'LT', false, 300, 'C', false, false, 1, false, false, false);
        }
        if($data['other_image_2'] != ''){
            $pdf::Image($path.$data['other_image_2'], 20, 153, 100, 100, '', '', 'LT', false, 300, 'C', false, false, 1, false, false, false);
        }

        
        $exportattachment5 = "
        <br pagebreak=\"true\"/>
        <div width=\"100%\" style=\"text-align: center; font-weight:bold; font-size:14;\">Foto Bersama Responden</div>
        <br>
        <div style=\"font-size:12;\"></div>
        <br>
        <table cellspacing=\"0\" cellpadding=\"2\" border=\"1\">
            <tr width = \"100%\">
                <td width=\"100%\"><div style=\"text-align: center;\"></div>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                </td>
            </tr>
        </table>
        <br>
        ";

        $pdf::writeHTML($exportattachment5, true, false, false, false, '');
        if($data['surveyor_respondent_photos'] != ''){
            $pdf::Image($path.$data['surveyor_respondent_photos'], 20, 36, 100, 100, '', '', 'LT', false, 300, 'C', false, false, 1, false, false, false);
        }

        // ob_clean();

        $filename = 'Penilaian Mustahik_'.$worksheet_result_id.'.pdf';
        $pdf::Output($filename, 'I');
    }
    
    public function printMustahikWorksheetResultMasjid($worksheet_result_id){
        $mustahikworksheetresult = MustahikWorksheetResult::select('mustahik_worksheet_result.worksheet_result_data', 'mustahik_worksheet_result.worksheet_result_date', 'mustahik_worksheet_result.user_id', 'mustahik_worksheet_requisition.service_id', 'mustahik_worksheet_requisition.worksheet_requisition_id')
        ->join('mustahik_worksheet_requisition', 'mustahik_worksheet_requisition.worksheet_requisition_id', 'mustahik_worksheet_result.worksheet_requisition_id')
        ->where('mustahik_worksheet_result.worksheet_result_id', $worksheet_result_id)
        ->first();

        $mustahikworksheet = MustahikWorksheet::select('worksheet_id', 'worksheet_no', 'worksheet_name', 'worksheet_type', 'worksheet_code')
        ->where('service_id', $mustahikworksheetresult['service_id'])
        ->get();

        $worksheetresultdata = json_decode($mustahikworksheetresult['worksheet_result_data']);

        $data = array();

        foreach($mustahikworksheet as $key => $val){
            if($val['worksheet_type'] == 1 || $val['worksheet_type'] == 3 || $val['worksheet_type'] == 4){
                $data[$val['worksheet_code']] = '';
            }else if($val['worksheet_type'] == 2){
                $data[$val['worksheet_code']] = array();
                $mustahikworksheetitem = MustahikWorksheetItem::select('worksheet_item_id', 'worksheet_id', 'section_name', 'worksheet_item_name', 'worksheet_item_code')
                ->where('worksheet_id', $val['worksheet_id'])
                ->get();

                foreach($mustahikworksheetitem as $keyy => $vall){
                    $data[$val['worksheet_code']][$vall['worksheet_item_code']] = '';
                }
            }
        }

        foreach($data as $key => $val){
            foreach($worksheetresultdata as $keyy => $vall){
                if($key == $vall->worksheet_code){
                    if(isset($vall->value)){
                        $data[$key] = $vall->value;
                    }else if(isset($vall->worksheetitem)){
                        foreach($vall->worksheetitem as $keyyy => $valll){
                            $data[$key][$valll->worksheet_item_code] = $valll->value;
                        }
                    }
                }
            }
        }
        
        $username = User::select('name', 'full_name')
        ->where('user_id','=',Auth::id())
        ->first();

        $surveyor = User::select('name', 'full_name')
        ->where('user_id','=',$mustahikworksheetresult['user_id'])
        ->first();

        $this->set_log(Auth::id(), $username['name'],'1089','Application.MustahikWorksheetResult.print',$username['name'],'Print');


        $pdf = new TCPDF('P', PDF_UNIT, 'A4', true, 'UTF-8', false);

        $pdf::SetPrintHeader(false);
        $pdf::SetPrintFooter(false);

        $pdf::SetMargins(20, 6, 20, 6);

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

        $path       = public_path('resources/img/logosmart/logobaznas01.png');
        $audited    = public_path('resources/img/audited.png');
        $wqa        = public_path('resources/img/wqa.png');

        $export = "
        <br></br>
        <div style=\"text-align:center;\">
            <br/>
            <br/>
            <br/>
            <br/>
        </div>
        
        <table cellspacing=\"0\" cellpadding=\"2\" border=\"1\">
            <tr width = \"100%\">";
            if($data['application_letter'] == true){
                $export .="<td width = \"10%\" style=\"text-align: center; font-weight: bold;\"> <span style=\"font-family:zapfdingbats;\">4</span></td>";
            }else{
                $export .="<td width = \"10%\" style=\"text-align: center; font-weight: bold;\"></td>";
            }
                $export .="<td width = \"40%\">Surat Permohonan</td>
            </tr>
            <tr width = \"100%\">";
            if($data['takmir_structural'] == true){
                $export .="<td width = \"10%\" style=\"text-align: center; font-weight: bold;\"> <span style=\"font-family:zapfdingbats;\">4</span></td>";
            }else{
                $export .="<td width = \"10%\" style=\"text-align: center; font-weight: bold;\"></td>";
            }
                $export .="<td width = \"40%\">Susunan Pengurus Takmir</td>
            </tr>
            <tr width = \"100%\">";
            if($data['rab'] == true){
                $export .="<td width = \"10%\" style=\"text-align: center; font-weight: bold;\"> <span style=\"font-family:zapfdingbats;\">4</span></td>";
            }else{
                $export .="<td width = \"10%\" style=\"text-align: center; font-weight: bold;\"></td>";
            }
                $export .="<td width = \"40%\">RAB</td>
            </tr>
            <tr width = \"100%\">";
            if($data['fc_wakaf'] == true){
                $export .="<td width = \"10%\" style=\"text-align: center; font-weight: bold;\"> <span style=\"font-family:zapfdingbats;\">4</span></td>";
            }else{
                $export .="<td width = \"10%\" style=\"text-align: center; font-weight: bold;\"></td>";
            }
                $export .="<td width = \"40%\">FC Sertifikat Wakaf / Bukti Proses Wakaf</td>
            </tr>
            <tr width = \"100%\">";
            if($data['masjid_photos'] == true){
                $export .="<td width = \"10%\" style=\"text-align: center; font-weight: bold;\"> <span style=\"font-family:zapfdingbats;\">4</span></td>";
            }else{
                $export .="<td width = \"10%\" style=\"text-align: center; font-weight: bold;\"></td>";
            }
                $export .="<td width = \"40%\">Foto Masjid</td>
            </tr>
        </table>
        <table cellspacing=\"0\" cellpadding=\"2\" border=\"0\">
            <tr width = \"100%\">
                <td width=\"5%\">
                    <div style=\"font-weight:bold\">I.</div>
                </td>
                <td width=\"95%\">
                    <div style=\"font-weight:bold\">IDENTITAS TAKMIR</div>
                </td>
            </tr>
            <tr width = \"100%\">
                <td width=\"5%\">
                </td>
                <td width=\"5%\">1.</td>
                <td width=\"40%\">Nama</td>
                <td width=\"3%\">:</td>
                <td width=\"47%\">".$data['takmir_name']."</td>
            </tr>
            <tr width = \"100%\">
                <td width=\"5%\">
                </td>
                <td width=\"5%\">2.</td>
                <td width=\"40%\">Alamat</td>
                <td width=\"3%\">:</td>
                <td width=\"47%\">".$data['takmir_address']."</td>
            </tr>
            <tr width = \"100%\">
                <td width=\"5%\">
                </td>
                <td width=\"5%\">3.</td>
                <td width=\"40%\">Kedudukan dalam Kepengurusan</td>
                <td width=\"3%\">:</td>
                <td width=\"47%\">".$data['takmir_position']."</td>
            </tr>
            <tr width = \"100%\">
                <td width=\"5%\">
                </td>
                <td width=\"5%\">4.</td>
                <td width=\"40%\">No. HP</td>
                <td width=\"3%\">:</td>
                <td width=\"47%\">".$data['takmir_phone_number']."</td>
            </tr>
            <tr width = \"100%\">
                <td width=\"5%\">
                    <div style=\"font-weight:bold\">II.</div>
                </td>
                <td width=\"95%\">
                    <div style=\"font-weight:bold\">IDENTITAS MASJID</div>
                </td>
            </tr>
            <tr width = \"100%\">
                <td width=\"5%\">
                </td>
                <td width=\"5%\">1.</td>
                <td width=\"40%\">Nama Masjid</td>
                <td width=\"3%\">:</td>
                <td width=\"47%\">".$data['mosque_name']."</td>
            </tr>
            <tr width = \"100%\">
                <td width=\"5%\">
                </td>
                <td width=\"5%\">2.</td>
                <td width=\"40%\">Ijin Masjid</td>
                <td width=\"3%\">:</td>
                <td width=\"47%\">";
                if($data['mosque_permission']['permission']){
                    $export .= "Ada";
                }else{
                    $export .= "Tidak Ada";
                }
                $export .="</td>
            </tr>
            <tr width = \"100%\">
                <td width=\"5%\">
                </td>
                <td width=\"5%\">3.</td>
                <td width=\"40%\">Sertifikat Wakaf</td>
                <td width=\"3%\">:</td>
                <td width=\"47%\">";
                if($data['mosque_wakaf_certificate']['certificate']){
                    $export .= "Ada";
                }else{
                    $export .= "Tidak Ada";
                }
                $export .="</td>
            </tr>
            <tr width = \"100%\">
                <td width=\"5%\">
                </td>
                <td width=\"5%\">4.</td>
                <td width=\"40%\">Jumlah KK Miskin di Lokasi Masjid</td>
                <td width=\"3%\">:</td>
                <td width=\"47%\">".$data['mosque_poor_KK']."</td>
            </tr>
            <tr width = \"100%\">
                <td width=\"5%\">
                </td>
                <td width=\"5%\">5.</td>
                <td width=\"40%\">Luas Masjid</td>
                <td width=\"3%\">:</td>
                <td width=\"47%\">".$data['mosque_area']." m2</td>
            </tr>
            <tr width = \"100%\">
                <td width=\"5%\">
                </td>
                <td width=\"5%\">6.</td>
                <td width=\"40%\">Jumlah Jamaah</td>
                <td width=\"3%\">:</td>
                <td width=\"47%\">".$data['mosque_jamaah']."</td>
            </tr>
            <tr width = \"100%\">
                <td width=\"5%\">
                </td>
                <td width=\"5%\">7.</td>
                <td width=\"40%\">Kegiatan Masjid</td>
                <td width=\"3%\">:</td>
                <td width=\"47%\">".$data['mosque_activity']."</td>
            </tr>
            <tr width = \"100%\">
                <td width=\"5%\">
                    <div style=\"font-weight:bold\">III.</div>
                </td>
                <td width=\"95%\">
                    <div style=\"font-weight:bold\">PEMBANGUNAN / RENOVASI MASJID</div>
                </td>
            </tr>
            <tr width = \"100%\">
                <td width=\"5%\">
                </td>
                <td width=\"5%\">1.</td>
                <td width=\"40%\">Pembangunan / Renovasi Masjid</td>
                <td width=\"3%\">:</td>
                <td width=\"47%\">".$data['mosque_build_renovation']."</td>
            </tr>
            <tr width = \"100%\">
                <td width=\"5%\">
                </td>
                <td width=\"5%\">2.</td>
                <td width=\"40%\">Progress Pembangunan / Renovasi Masjid</td>
                <td width=\"3%\">:</td>
                <td width=\"47%\">".$data['mosque_progress']."</td>
            </tr>
            <tr width = \"100%\">
                <td width=\"5%\">
                </td>
                <td width=\"5%\">3.</td>
                <td width=\"40%\">Dana yang Dimiliki Untuk Pembangunan Renovasi Masjid</td>
                <td width=\"3%\">:</td>
                <td width=\"47%\">".(is_numeric($data['mosque_fund']) ? number_format((int)$data['mosque_fund'], 2) : $data['mosque_fund'])."</td>
            </tr>
            <tr width = \"100%\">
                <td width=\"5%\">
                </td>
                <td width=\"5%\">4.</td>
                <td width=\"40%\">Gambar Masjid</td>
                <td width=\"3%\">:</td>
                <td width=\"47%\">Terlampir Dibelakang</td>
            </tr>
            <tr width = \"100%\">
                <td width=\"5%\">
                    <div style=\"font-weight:bold\">IV.</div>
                </td>
                <td width=\"95%\">
                    <div style=\"font-weight:bold\">CATATAN SURVEYOR</div>
                </td>
            </tr>
            <tr width = \"100%\">
                <td width=\"5%\">
                </td>
                <td width=\"95%\">".$data['surveyor_remark']."</td>
            </tr>
        </table>
        <br>
        <table cellspacing=\"0\" cellpadding=\"2\" border=\"0\">
            <tr>
                <td width=\"100%\"><div style=\"text-align:center;\">Surakarta, ".date("d-m-Y", strtotime($mustahikworksheetresult['worksheet_result_date']))."</div></td>
            </tr>
            <tr>
                <td width=\"100%\"><div style=\"text-align:center;\">Mengetahui,</div></td>
            </tr>
            <br>
            <tr>
                <td width=\"30%\"><div style=\"text-align:center;\">Wakil Ketua II</div></td>
                <td width=\"40%\"><div style=\"text-align:center;\">Kepala Pelaksana</div></td>
                <td width=\"30%\"><div style=\"text-align:center;\">Petugas Survei</div></td>
            </tr>
            <br>
            <br>
            <br>
            <tr>
                <td width=\"30%\"><div style=\"text-align:center;\">(Drs. Sarwaka)</div></td>
                <td width=\"40%\"><div style=\"text-align:center;\">(Dewi Purwantiningsih, S.E)</div></td>
                <td width=\"30%\"><div style=\"text-align:center;\">(".$surveyor['full_name'].")</div></td>
            </tr>
            <br>
            <tr>
                <td width=\"75%\"><div style=\"text-align:center; color:green; font-weight:bold;\">Kantor : </div></td>
                <td width=\"25%\"><div style=\"text-align:center;\"></div></td>
            </tr>
            <tr>
                <td width=\"75%\"><div style=\"text-align:center; color:gray;\">Jl. Raya Timur Km 4 Komplek BAZNAS Pilangsari</div></td>
                <td width=\"25%\"><div style=\"text-align:center;\"></div></td>
            </tr>
            <tr>
                <td width=\"75%\"><div style=\"text-align:center; color:gray;\">Ngrampal Surakarta  57252 Telp: 082138511100</div></td>
                <td width=\"25%\"><div style=\"text-align:center;\"></div></td>
            </tr>
            <tr>
                <td width=\"75%\"><div style=\"text-align:center; color:gray;\">Telp/Fax: (0271) 8825250</div></td>
                <td width=\"25%\"><div style=\"text-align:center;\"></div></td>
            </tr>
            <tr>
                <td width=\"75%\"><div style=\"text-align:center; color:gray;\">E-mail: baznaskab.sragen@baznas.go.id Website : bazsragen.org</div></td>
                <td width=\"25%\"><div style=\"text-align:center;\"></div></td>
            </tr>
        </table>
        ";
        
        $pdf::writeHTML($export, true, false, false, false, '');

        $pdf::Image( $path, 4, 4, 25, 25, 'PNG', '', 'LT', false, 300, 'C', false, false, 1, false, false, false);
        $pdf::Image( $audited, 137, 255, 16, 15, 'PNG', '', 'LT', false, 300, '', false, false, 1, false, false, false);
        $pdf::Image( $wqa, 154, 255, 25, 15, 'PNG', '', 'LT', false, 300, '', false, false, 1, false, false, false);
        $path = public_path('storage/worksheet-image/'.$mustahikworksheetresult['worksheet_requisition_id'].'/');

        $export2 = "
        <br pagebreak=\"true\"/>
        <div width=\"100%\" style=\"text-align: center; font-weight:bold; font-size:14;\">Lampiran Gambar</div>
        <br>
        <div style=\"font-size:12;\">Gambar Masjid</div>
        <br>
        <table cellspacing=\"0\" cellpadding=\"2\" border=\"1\">
            <tr width = \"100%\">
                <td width=\"100%\"><div style=\"text-align: center;\"></div>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                </td>
            </tr>
        </table>
        <br>
        <div style=\"font-size:12;\">Gambar Masjid</div>
        <br>
        <table cellspacing=\"0\" cellpadding=\"2\" border=\"1\">
            <tr width = \"100%\">
                <td width=\"100%\"><div style=\"text-align: center;\"></div>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                </td>
            </tr>
        </table>
        ";

        $pdf::writeHTML($export2, true, false, false, false, '');

        $path = public_path('storage/worksheet-image/'.$mustahikworksheetresult['worksheet_requisition_id'].'/');
        if($data['mosque_image_1'] != ''){
            $pdf::Image($path.$data['mosque_image_1'], 20, 36, 100, 100, '', '', 'LT', false, 300, 'C', false, false, 1, false, false, false);
        }
        if($data['mosque_image_2'] != ''){
            $pdf::Image($path.$data['mosque_image_2'], 20, 153, 100, 100, '', '', 'LT', false, 300, 'C', false, false, 1, false, false, false);
        }
        


        $export3 = "
        <br pagebreak=\"true\"/>
        <div width=\"100%\" style=\"text-align: center; font-weight:bold; font-size:14;\">Lampiran Gambar</div>
        <br>
        <div style=\"font-size:12;\">Gambar Lainnya 1</div>
        <br>
        <table cellspacing=\"0\" cellpadding=\"2\" border=\"1\">
            <tr width = \"100%\">
                <td width=\"100%\"><div style=\"text-align: center;\"></div>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                </td>
            </tr>
        </table>
        <br>
        <div style=\"font-size:12;\">Gambar Lainnya 2</div>
        <br>
        <table cellspacing=\"0\" cellpadding=\"2\" border=\"1\">
            <tr width = \"100%\">
                <td width=\"100%\"><div style=\"text-align: center;\"></div>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                </td>
            </tr>
        </table>
        ";

        $pdf::writeHTML($export3, true, false, false, false, '');

        $path = public_path('storage/worksheet-image/'.$mustahikworksheetresult['worksheet_requisition_id'].'/');
        if($data['other_image_1'] != ''){
            $pdf::Image($path.$data['other_image_1'], 20, 36, 100, 100, '', '', 'LT', false, 300, 'C', false, false, 1, false, false, false);
        }
        if($data['other_image_2'] != ''){
            $pdf::Image($path.$data['other_image_2'], 20, 153, 100, 100, '', '', 'LT', false, 300, 'C', false, false, 1, false, false, false);
        }
        


        $export4 = "
        <br pagebreak=\"true\"/>
        <div width=\"100%\" style=\"text-align: center; font-weight:bold; font-size:14;\">Foto bersama Responden</div>
        <br>
        <div style=\"font-size:12;\"></div>
        <br>
        <table cellspacing=\"0\" cellpadding=\"2\" border=\"1\">
            <tr width = \"100%\">
                <td width=\"100%\"><div style=\"text-align: center;\"></div>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                </td>
            </tr>
        </table>
        ";

        $pdf::writeHTML($export4, true, false, false, false, '');

        $path = public_path('storage/worksheet-image/'.$mustahikworksheetresult['worksheet_requisition_id'].'/');
        if($data['surveyor_respondent_photos'] != ''){
            $pdf::Image($path.$data['surveyor_respondent_photos'], 20, 36, 100, 100, '', '', 'LT', false, 300, 'C', false, false, 1, false, false, false);
        }

        // ob_clean();

        $filename = 'Penilaian Mustahik_'.$worksheet_result_id.'.pdf';
        $pdf::Output($filename, 'I');
    }
    
    public function printMustahikWorksheetResultModalUsaha($worksheet_result_id){
        $mustahikworksheetresult = MustahikWorksheetResult::select('mustahik_worksheet_result.worksheet_result_data', 'mustahik_worksheet_result.worksheet_result_date', 'mustahik_worksheet_result.user_id', 'mustahik_worksheet_requisition.service_id', 'mustahik_worksheet_requisition.worksheet_requisition_id')
        ->join('mustahik_worksheet_requisition', 'mustahik_worksheet_requisition.worksheet_requisition_id', 'mustahik_worksheet_result.worksheet_requisition_id')
        ->where('mustahik_worksheet_result.worksheet_result_id', $worksheet_result_id)
        ->first();

        $mustahikworksheet = MustahikWorksheet::select('worksheet_id', 'worksheet_no', 'worksheet_name', 'worksheet_type', 'worksheet_code')
        ->where('service_id', $mustahikworksheetresult['service_id'])
        ->get();

        $worksheetresultdata = json_decode($mustahikworksheetresult['worksheet_result_data']);

        $data = array();

        foreach($mustahikworksheet as $key => $val){
            if($val['worksheet_type'] == 1 || $val['worksheet_type'] == 3 || $val['worksheet_type'] == 4){
                $data[$val['worksheet_code']] = '';
            }else if($val['worksheet_type'] == 2){
                $data[$val['worksheet_code']] = array();
                $mustahikworksheetitem = MustahikWorksheetItem::select('worksheet_item_id', 'worksheet_id', 'section_name', 'worksheet_item_name', 'worksheet_item_code')
                ->where('worksheet_id', $val['worksheet_id'])
                ->get();

                foreach($mustahikworksheetitem as $keyy => $vall){
                    $data[$val['worksheet_code']][$vall['worksheet_item_code']] = '';
                }
            }
        }

        foreach($data as $key => $val){
            foreach($worksheetresultdata as $keyy => $vall){
                if($key == $vall->worksheet_code){
                    if(isset($vall->value)){
                        $data[$key] = $vall->value;
                    }else if(isset($vall->worksheetitem)){
                        foreach($vall->worksheetitem as $keyyy => $valll){
                            $data[$key][$valll->worksheet_item_code] = $valll->value;
                        }
                    }
                }
            }
        }
        
        $username = User::select('name', 'full_name')
        ->where('user_id','=',Auth::id())
        ->first();

        $surveyor = User::select('name', 'full_name')
        ->where('user_id','=',$mustahikworksheetresult['user_id'])
        ->first();

        $this->set_log(Auth::id(), $username['name'],'1089','Application.MustahikWorksheetResult.print',$username['name'],'Print');


        $pdf = new TCPDF('P', PDF_UNIT, 'A4', true, 'UTF-8', false);

        $pdf::SetPrintHeader(false);
        $pdf::SetPrintFooter(false);

        $pdf::SetMargins(20, 6, 20, 6);

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

        $path       = public_path('resources/img/logosmart/logobaznas01.png');
        $audited    = public_path('resources/img/audited.png');
        $wqa        = public_path('resources/img/wqa.png');

        $export = "
        <br></br>
        <div style=\"text-align:center;\">
            <br/>
            <br/>
            <br/>
            <br/>
            <br/>
            <br/>
            <b>FORMULIR SURVEI</b>
            <br/>
            <b>PROGRAM MUSTAHIK PENGUSAHA</b>
            <br/>
        </div>
        
        <table cellspacing=\"0\" cellpadding=\"2\" border=\"0\">
            <tr>
                <td width =\"70%\">
                    <table cellspacing=\"0\" cellpadding=\"1\" border=\"0\" style=\"float:left; width:420px\">
                        <tr width =\"100%\" align:\"center\">
                            <td>
                            </td>
                        </tr>
                    </table>
                </td>
                <td width = \"30%\">
                    <table cellspacing=\"0\" cellpadding=\"2\" border=\"2\" style=\"float:right; width:480px\">
                        <tr>";
                            foreach($data['category'] as $key => $val){
                                if($key == 'asnaf_fisabililah'){
                                    if($val == true){
                                        $ashnaf = "<div style=\"text-align: center; font-weight: bold; font-size: 12\">Asnaf Fisabililah</div>";
                                    }
                                }
                                if($key == 'asnaf_poor'){
                                    if($val == true){
                                        $ashnaf = "<div style=\"text-align: center; font-weight: bold; font-size: 12\">Asnaf Miskin</div>";
                                    }
                                }
                            }
                            $export .="
                            <td float=\"right\" width = \"40%\">".$ashnaf."</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <table cellspacing=\"0\" cellpadding=\"2\" border=\"0\">
            <tr width = \"100%\">
                <td width=\"5%\">
                    <div style=\"font-weight:bold\"></div>
                </td>
                <td width=\"95%\">
                    <div style=\"font-weight:bold\">PROFIL PRIBADI</div>
                </td>
            </tr>
            <tr width = \"100%\">
                <td width=\"5%\">
                </td>
                <td width=\"5%\">1.</td>
                <td width=\"40%\">Nama</td>
                <td width=\"3%\">:</td>
                <td width=\"47%\">".$data['respondent_name']."</td>
            </tr>
            <tr width = \"100%\">
                <td width=\"5%\">
                </td>
                <td width=\"5%\">2.</td>
                <td width=\"40%\">Alamat</td>
                <td width=\"3%\">:</td>
                <td width=\"47%\">".$data['respondent_address']."</td>
            </tr>
            <tr width = \"100%\">
                <td width=\"5%\">
                </td>
                <td width=\"5%\">3.</td>
                <td width=\"40%\">No. HP</td>
                <td width=\"3%\">:</td>
                <td width=\"47%\">".$data['respondent_phone']."</td>
            </tr>
            <tr width = \"100%\">
                <td width=\"5%\">
                </td>
                <td width=\"5%\">4.</td>
                <td width=\"40%\">Pekerjaan</td>
                <td width=\"3%\">:</td>
                <td width=\"47%\">".$data['respondent_job']."</td>
            </tr>
            <tr width = \"100%\">
                <td width=\"5%\">
                </td>
                <td width=\"5%\">5.</td>
                <td width=\"40%\">Status Pernikahan</td>
                <td width=\"3%\">:</td>
                <td width=\"47%\">".$data['respondent_marriage_status']."</td>
            </tr>
            <tr width = \"100%\">
                <td width=\"5%\">
                </td>
                <td width=\"5%\">6.</td>
                <td width=\"40%\">Pendidikan Terakhir</td>
                <td width=\"3%\">:</td>
                <td width=\"47%\">".$data['respondent_education']."</td>
            </tr>
            <tr width = \"100%\">
                <td width=\"5%\">
                </td>
                <td width=\"5%\">7.</td>
                <td width=\"40%\">Jumlah Tanggungan Keluarga</td>
                <td width=\"3%\">:</td>
                <td width=\"47%\">".$data['respondent_family']." Orang</td>
            </tr>";
            
            foreach($data['category'] as $key => $val){
                if($key == 'asnaf_fisabililah'){
                    if($val == true){
                        $export .= "
                        <tr width = \"100%\">
                            <td width=\"5%\">
                            </td>
                            <td width=\"5%\">8.</td>
                            <td width=\"40%\">Aktif Dalam Kegiatan Islami</td>
                            <td width=\"3%\">:</td>
                            <td width=\"47%\">";
                                $no = 0;
                                foreach($data['respondent_islam_activity'] as $key => $val){
                                    $item_name = MustahikWorksheetItem::select('worksheet_item_name')
                                    ->where('worksheet_item_code', $key)
                                    ->first();
                                    if($no == 2){
                                        if($val == true){
                                            $export .= "<br><input type=\"checkbox\" name=\"box\" value=\"1\" readonly=\"true\" checked=\"checked\"/>".$item_name['worksheet_item_name'];
                                        }else{
                                            $export .= "<br><input type=\"checkbox\" name=\"box\" value=\"1\" readonly=\"true\"/>".$item_name['worksheet_item_name'];
                                        }
                                    }else{
                                        if($val == true){
                                            $export .= "<input type=\"checkbox\" name=\"box\" value=\"1\" readonly=\"true\" checked=\"checked\"/>".$item_name['worksheet_item_name'];
                                        }else{
                                            $export .= "<input type=\"checkbox\" name=\"box\" value=\"1\" readonly=\"true\"/>".$item_name['worksheet_item_name'];
                                        }
                                    }
                                    $no++;
                                }
                            $export .= "</td>
                        </tr>
                        ";
                    }
                }
                if($key == 'asnaf_poor'){
                    if($val == true){
                        $export .= "
                        <tr width = \"100%\">
                            <td width=\"5%\">
                            </td>
                            <td width=\"5%\">8.</td>
                            <td width=\"40%\">SKTM</td>
                            <td width=\"3%\">:</td>
                            <td width=\"47%\">";
                                $no = 0;
                                foreach($data['sktm'] as $key => $val){
                                    $item_name = MustahikWorksheetItem::select('worksheet_item_name')
                                    ->where('worksheet_item_code', $key)
                                    ->first();
                                    if($no == 2){
                                        if($val == true){
                                            $export .= "<br><input type=\"checkbox\" name=\"box\" value=\"1\" readonly=\"true\" checked=\"checked\"/>".$item_name['worksheet_item_name'];
                                        }else{
                                            $export .= "<br><input type=\"checkbox\" name=\"box\" value=\"1\" readonly=\"true\"/>".$item_name['worksheet_item_name'];
                                        }
                                    }else{
                                        if($val == true){
                                            $export .= "<input type=\"checkbox\" name=\"box\" value=\"1\" readonly=\"true\" checked=\"checked\"/>".$item_name['worksheet_item_name'];
                                        }else{
                                            $export .= "<input type=\"checkbox\" name=\"box\" value=\"1\" readonly=\"true\"/>".$item_name['worksheet_item_name'];
                                        }
                                    }
                                    $no++;
                                }
                            $export .= "</td>
                        </tr>
                        ";
                    }
                }
            }

            $export .="<tr width = \"100%\">
                <td width=\"5%\">
                    <div style=\"font-weight:bold\"></div>
                </td>
                <td width=\"95%\">
                    <div style=\"font-weight:bold\">PROFIL USAHA</div>
                </td>
            </tr>
            <tr width = \"100%\">
                <td width=\"5%\">
                </td>
                <td width=\"5%\">1.</td>
                <td width=\"40%\">Jenis Usaha</td>
                <td width=\"3%\">:</td>
                <td width=\"47%\">".$data['business_type']."</td>
            </tr>
            <tr width = \"100%\">
                <td width=\"5%\">
                </td>
                <td width=\"5%\">2.</td>
                <td width=\"40%\">Nama Usaha</td>
                <td width=\"3%\">:</td>
                <td width=\"47%\">".$data['business_name']."</td>
            </tr>
            <tr width = \"100%\">
                <td width=\"5%\">
                </td>
                <td width=\"5%\">3.</td>
                <td width=\"40%\">Alamat Tempat Usaha</td>
                <td width=\"3%\">:</td>
                <td width=\"47%\">".$data['business_address']."</td>
            </tr>
            <tr width = \"100%\">
                <td width=\"5%\">
                </td>
                <td width=\"5%\">4.</td>
                <td width=\"40%\">Lokasi Usaha</td>
                <td width=\"3%\">:</td>
                <td width=\"47%\">";
                    $no = 0;
                    foreach($data['business_location'] as $key => $val){
                        $item_name = MustahikWorksheetItem::select('worksheet_item_name')
                        ->where('worksheet_item_code', $key)
                        ->first();
                        if($no == 2){
                            if($val == true){
                                $export .= "<br><input type=\"checkbox\" name=\"box\" value=\"1\" readonly=\"true\" checked=\"checked\"/>".$item_name['worksheet_item_name'];
                            }else{
                                $export .= "<br><input type=\"checkbox\" name=\"box\" value=\"1\" readonly=\"true\"/>".$item_name['worksheet_item_name'];
                            }
                        }else{
                            if($val == true){
                                $export .= "<input type=\"checkbox\" name=\"box\" value=\"1\" readonly=\"true\" checked=\"checked\"/>".$item_name['worksheet_item_name'];
                            }else{
                                $export .= "<input type=\"checkbox\" name=\"box\" value=\"1\" readonly=\"true\"/>".$item_name['worksheet_item_name'];
                            }
                        }
                        $no++;
                    }
                $export .= "</td>
            </tr>
            <tr width = \"100%\">
                <td width=\"5%\">
                </td>
                <td width=\"5%\">4.</td>
                <td width=\"40%\">Usaha Kelompok / Perorangan</td>
                <td width=\"3%\">:</td>
                <td width=\"47%\">";
                    $no = 0;
                    foreach($data['business_community'] as $key => $val){
                        $item_name = MustahikWorksheetItem::select('worksheet_item_name')
                        ->where('worksheet_item_code', $key)
                        ->first();
                        if($no == 2){
                            if($val == true){
                                $export .= "<br><input type=\"checkbox\" name=\"box\" value=\"1\" readonly=\"true\" checked=\"checked\"/>".$item_name['worksheet_item_name'];
                            }else{
                                $export .= "<br><input type=\"checkbox\" name=\"box\" value=\"1\" readonly=\"true\"/>".$item_name['worksheet_item_name'];
                            }
                        }else{
                            if($val == true){
                                $export .= "<input type=\"checkbox\" name=\"box\" value=\"1\" readonly=\"true\" checked=\"checked\"/>".$item_name['worksheet_item_name'];
                            }else{
                                $export .= "<input type=\"checkbox\" name=\"box\" value=\"1\" readonly=\"true\"/>".$item_name['worksheet_item_name'];
                            }
                        }
                        $no++;
                    }
                $export .= "</td>
            </tr>
            <tr width = \"100%\">
                <td width=\"5%\">
                </td>
                <td width=\"5%\">6.</td>
                <td width=\"40%\">Lama Usaha</td>
                <td width=\"3%\">:</td>
                <td width=\"47%\">".$data['business_age']." bulan</td>
            </tr>
            <tr width = \"100%\">
                <td width=\"5%\">
                </td>
                <td width=\"5%\">7.</td>
                <td width=\"40%\">Jumlah Karyawan / Anggota</td>
                <td width=\"3%\">:</td>
                <td width=\"47%\">".$data['business_employee']." Orang</td>
            </tr>
            <tr width = \"100%\">
                <td width=\"5%\">
                </td>
                <td width=\"5%\">8.</td>
                <td width=\"40%\">Sumber Modal</td>
                <td width=\"3%\">:</td>
                <td width=\"47%\">".$data['business_modal']."</td>
            </tr>
            <tr width = \"100%\">
                <td width=\"5%\">
                </td>
                <td width=\"5%\">9.</td>
                <td width=\"40%\">Biaya Produksi</td>
                <td width=\"3%\">:</td>
                <td width=\"47%\">Rp. ".number_format($data['business_fee'], 2)."</td>
            </tr>
            <tr width = \"100%\">
                <td width=\"5%\">
                </td>
                <td width=\"5%\">10.</td>
                <td width=\"40%\">Proses Produksi</td>
                <td width=\"3%\">:</td>
                <td width=\"47%\">".$data['business_process']."</td>
            </tr>
            <tr width = \"100%\">
                <td width=\"5%\">
                </td>
                <td width=\"5%\">11.</td>
                <td width=\"40%\">Harga Jual</td>
                <td width=\"3%\">:</td>
                <td width=\"47%\">Rp. ".number_format($data['business_selling_price'], 2)."</td>
            </tr>
            <tr width = \"100%\">
                <td width=\"5%\">
                </td>
                <td width=\"5%\">12.</td>
                <td width=\"40%\">Margin Keuntungan</td>
                <td width=\"3%\">:</td>
                <td width=\"47%\">".$data['business_interest']."%</td>
            </tr>
            <tr width = \"100%\">
                <td width=\"5%\">
                </td>
                <td width=\"5%\">13.</td>
                <td width=\"40%\">Kendala Dalam Menjalankan Usaha</td>
                <td width=\"3%\">:</td>
                <td width=\"47%\">".$data['business_constraint']."</td>
            </tr>
            <tr width = \"100%\">
                <td width=\"5%\">
                </td>
                <td width=\"5%\">14.</td>
                <td width=\"40%\">Rencana Kedepan Terhadap Usaha</td>
                <td width=\"3%\">:</td>
                <td width=\"47%\">".$data['business_future_plan']."</td>
            </tr>
            <tr width = \"100%\">
                <td width=\"5%\">
                </td>
                <td width=\"5%\">15.</td>
                <td width=\"40%\">Keterangan Lain</td>
                <td width=\"3%\">:</td>
                <td width=\"47%\">".$data['surveyor_remark']."</td>
            </tr>
            <tr width = \"100%\">
                <td width=\"5%\">
                </td>
                <td width=\"5%\">16.</td>
                <td width=\"40%\">Kebutuhan Pengajuan Bantuan</td>
                <td width=\"3%\">:</td>
                <td width=\"47%\">
                <br/>
                a) ".$data['business_need_1']."
                <br/>
                b) ".$data['business_need_2']."
                <br/>
                c) ".$data['business_need_3']."
                <br/>
                Total Kebutuhan Rp. ".number_format($data['business_need_total'], 2)."
                </td>
            </tr>
        </table>
        <br>
        <br>
        <table cellspacing=\"0\" cellpadding=\"2\" border=\"0\">
            <tr>
                <td width=\"70%\">
                </td>
                <td width=\"30%\">
                    <div style=\"text-align:center;\">Surakarta, ".date("d-m-Y", strtotime($mustahikworksheetresult['worksheet_result_date']))."</div>
                </td>
            </tr>
            <tr>
                <td width=\"30%\"><div style=\"text-align:center;\">Wakil Ketua II</div></td>
                <td width=\"40%\"><div style=\"text-align:center;\">Kepala Pelaksana</div></td>
                <td width=\"30%\"><div style=\"text-align:center;\">Petugas Survei</div></td>
            </tr>
            <br>
            <br>
            <br>
            <tr>
                <td width=\"30%\"><div style=\"text-align:center;\">(Drs. Sarwaka)</div></td>
                <td width=\"40%\"><div style=\"text-align:center;\">(Dewi Purwantiningsih, S.E)</div></td>
                <td width=\"30%\"><div style=\"text-align:center;\">(".$surveyor['full_name'].")</div></td>
            </tr>
            <br>
        </table>
        ";
        
        $pdf::writeHTML($export, true, false, false, false, '');

        $pdf::Image( $path, 4, 4, 25, 25, 'PNG', '', 'LT', false, 300, 'C', false, false, 1, false, false, false);
        $path = public_path('storage/worksheet-image/'.$mustahikworksheetresult['worksheet_requisition_id'].'/');

        $export2 = "
        <br pagebreak=\"true\"/>
        <div width=\"100%\" style=\"text-align: center; font-weight:bold; font-size:14;\">Lampiran Gambar</div>
        <br>
        <div style=\"font-size:12;\">Gambar 1</div>
        <br>
        <table cellspacing=\"0\" cellpadding=\"2\" border=\"1\">
            <tr width = \"100%\">
                <td width=\"100%\"><div style=\"text-align: center;\"></div>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                </td>
            </tr>
        </table>
        <br>
        <div style=\"font-size:12;\">Gambar 2</div>
        <br>
        <table cellspacing=\"0\" cellpadding=\"2\" border=\"1\">
            <tr width = \"100%\">
                <td width=\"100%\"><div style=\"text-align: center;\"></div>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                </td>
            </tr>
        </table>
        ";

        $pdf::writeHTML($export2, true, false, false, false, '');

        $path = public_path('storage/worksheet-image/'.$mustahikworksheetresult['worksheet_requisition_id'].'/');
        if($data['image_1'] != ''){
            $pdf::Image($path.$data['image_1'], 20, 36, 100, 100, '', '', 'LT', false, 300, 'C', false, false, 1, false, false, false);
        }
        if($data['image_2'] != ''){
            $pdf::Image($path.$data['image_2'], 20, 153, 100, 100, '', '', 'LT', false, 300, 'C', false, false, 1, false, false, false);
        }
        
        $export4 = "
        <br pagebreak=\"true\"/>
        <div width=\"100%\" style=\"text-align: center; font-weight:bold; font-size:14;\">Foto bersama Responden</div>
        <br>
        <div style=\"font-size:12;\"></div>
        <br>
        <table cellspacing=\"0\" cellpadding=\"2\" border=\"1\">
            <tr width = \"100%\">
                <td width=\"100%\"><div style=\"text-align: center;\"></div>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                <br/>
                </td>
            </tr>
        </table>
        ";

        $pdf::writeHTML($export4, true, false, false, false, '');

        $path = public_path('storage/worksheet-image/'.$mustahikworksheetresult['worksheet_requisition_id'].'/');
        if($data['surveyor_respondent_photos'] != ''){
            $pdf::Image($path.$data['surveyor_respondent_photos'], 20, 36, 100, 100, '', '', 'LT', false, 300, 'C', false, false, 1, false, false, false);
        }

        // ob_clean();

        $filename = 'Penilaian Mustahik_'.$worksheet_result_id.'.pdf';
        $pdf::Output($filename, 'I');
    }
    
    public function scoringMustahikWorksheetResult ($worksheet_result_id){
        $mustahikworksheetresult = MustahikWorksheetResult::select('mustahik_worksheet_result.worksheet_result_data', 'mustahik_worksheet_result.worksheet_result_date', 'mustahik_worksheet_result.user_id', 'mustahik_worksheet_requisition.service_id', 'mustahik_worksheet_requisition.worksheet_requisition_id')
        ->join('mustahik_worksheet_requisition', 'mustahik_worksheet_requisition.worksheet_requisition_id', 'mustahik_worksheet_result.worksheet_requisition_id')
        ->where('mustahik_worksheet_result.worksheet_result_id', $worksheet_result_id)
        ->first();

        $mustahikworksheet = MustahikWorksheet::select('worksheet_id', 'worksheet_no', 'worksheet_name', 'worksheet_type', 'worksheet_code')
        ->where('service_id', $mustahikworksheetresult['service_id'])
        ->get();

        $worksheetresultdata = json_decode($mustahikworksheetresult['worksheet_result_data']);

        $data = array();

        foreach($mustahikworksheet as $key => $val){
            if($val['worksheet_type'] == 1 || $val['worksheet_type'] == 3 || $val['worksheet_type'] == 4){
                $data[$val['worksheet_code']] = '';
            }else if($val['worksheet_type'] == 2){
                $data[$val['worksheet_code']] = array();
                $mustahikworksheetitem = MustahikWorksheetItem::select('worksheet_item_id', 'worksheet_id', 'section_name', 'worksheet_item_name', 'worksheet_item_code')
                ->where('worksheet_id', $val['worksheet_id'])
                ->get();

                foreach($mustahikworksheetitem as $keyy => $vall){
                    $data[$val['worksheet_code']][$vall['worksheet_item_code']] = '';
                }
            }
        }

        foreach($data as $key => $val){
            foreach($worksheetresultdata as $keyy => $vall){
                if($key == $vall->worksheet_code){
                    if(isset($vall->value)){
                        $data[$key] = $vall->value;
                    }else if(isset($vall->worksheetitem)){
                        foreach($vall->worksheetitem as $keyyy => $valll){
                            $data[$key][$valll->worksheet_item_code] = $valll->value;
                        }
                    }
                }
            }
        }

        $mustahikworksheetdetail = MustahikWorksheetResult::select('mustahik_worksheet_result.worksheet_result_id', 'mustahik_worksheet_result.worksheet_result_date', 'system_user.full_name', 'trans_service_requisition.service_requisition_no', 'trans_service_requisition.service_requisition_name', 'trans_service_requisition.created_at', 'core_service.service_name')
        ->join('mustahik_worksheet_requisition', 'mustahik_worksheet_requisition.worksheet_requisition_id', 'mustahik_worksheet_result.worksheet_requisition_id')
        ->join('trans_service_requisition', 'mustahik_worksheet_requisition.service_requisition_id', 'trans_service_requisition.service_requisition_id')
        ->join('system_user', 'mustahik_worksheet_result.user_id', 'system_user.user_id')
        ->join('core_service', 'core_service.service_id', 'trans_service_requisition.service_id')
        ->where('mustahik_worksheet_result.worksheet_result_id', $worksheet_result_id)
        ->first();

        
        if($mustahikworksheetresult['service_id'] == 7){
            return view('content/MustahikWorksheetResult/FormScoringMustahikWorksheetResult',compact('data', 'mustahikworksheetdetail'));
        }else if($mustahikworksheetresult['service_id'] == 1){
            return view('content/MustahikWorksheetResult/FormScoringMustahikWorksheetResultMasjid',compact('data', 'mustahikworksheetdetail'));
        }else if($mustahikworksheetresult['service_id'] == 6){
            return view('content/MustahikWorksheetResult/FormScoringMustahikWorksheetResultMU',compact('data', 'mustahikworksheetdetail')); 
        }else{
            $msg = "Tidak Ada Format Mustahik Pelayanan ini";
            return redirect('/mustahik-worksheet-result')->with('msg',$msg);
        }
    }

    public function printScoringMustahikWorksheetResult($worksheet_result_id){
        $worksheet = MustahikWorksheetResult::select('mustahik_worksheet_requisition.service_id')
        ->join('mustahik_worksheet_requisition', 'mustahik_worksheet_requisition.worksheet_requisition_id', 'mustahik_worksheet_result.worksheet_requisition_id')
        ->where('mustahik_worksheet_result.worksheet_result_id', $worksheet_result_id)
        ->first();

        if($worksheet['service_id'] == 7){
            $this->printScoringMustahikWorksheetResultRTLH($worksheet_result_id);
        }else if($worksheet['service_id'] == 1){
            $this->printScoringMustahikWorksheetResultMasjid($worksheet_result_id);
        }else if($worksheet['service_id'] == 6){
            $this->printScoringMustahikWorksheetResultMU($worksheet_result_id);        
        }else{
            $msg = "Tidak Ada Format Mustahik Pelayanan ini";
            return redirect('/mustahik-worksheet-result')->with('msg',$msg);
        }
    }
    
    public function printScoringMustahikWorksheetResultRTLH($worksheet_result_id){
        $mustahikworksheetresult = MustahikWorksheetResult::select('mustahik_worksheet_result.worksheet_result_data', 'mustahik_worksheet_result.worksheet_result_date', 'mustahik_worksheet_result.user_id', 'mustahik_worksheet_requisition.service_id', 'mustahik_worksheet_requisition.worksheet_requisition_id')
        ->join('mustahik_worksheet_requisition', 'mustahik_worksheet_requisition.worksheet_requisition_id', 'mustahik_worksheet_result.worksheet_requisition_id')
        ->where('mustahik_worksheet_result.worksheet_result_id', $worksheet_result_id)
        ->first();

        $mustahikworksheet = MustahikWorksheet::select('worksheet_id', 'worksheet_no', 'worksheet_name', 'worksheet_type', 'worksheet_code')
        ->where('service_id', $mustahikworksheetresult['service_id'])
        ->get();

        $worksheetresultdata = json_decode($mustahikworksheetresult['worksheet_result_data']);

        $data = array();

        foreach($mustahikworksheet as $key => $val){
            if($val['worksheet_type'] == 1 || $val['worksheet_type'] == 3 || $val['worksheet_type'] == 4){
                $data[$val['worksheet_code']] = '';
            }else if($val['worksheet_type'] == 2){
                $data[$val['worksheet_code']] = array();
                $mustahikworksheetitem = MustahikWorksheetItem::select('worksheet_item_id', 'worksheet_id', 'section_name', 'worksheet_item_name', 'worksheet_item_code')
                ->where('worksheet_id', $val['worksheet_id'])
                ->get();

                foreach($mustahikworksheetitem as $keyy => $vall){
                    $data[$val['worksheet_code']][$vall['worksheet_item_code']] = '';
                }
            }
        }

        foreach($data as $key => $val){
            foreach($worksheetresultdata as $keyy => $vall){
                if($key == $vall->worksheet_code){
                    if(isset($vall->value)){
                        $data[$key] = $vall->value;
                    }else if(isset($vall->worksheetitem)){
                        foreach($vall->worksheetitem as $keyyy => $valll){
                            $data[$key][$valll->worksheet_item_code] = $valll->value;
                        }
                    }
                }
            }
        }

        $mustahikworksheetdetail = MustahikWorksheetResult::select('mustahik_worksheet_result.worksheet_result_id', 'mustahik_worksheet_result.worksheet_result_date', 'system_user.full_name', 'trans_service_requisition.service_requisition_no', 'trans_service_requisition.service_requisition_name', 'trans_service_requisition.created_at', 'core_service.service_name')
        ->join('mustahik_worksheet_requisition', 'mustahik_worksheet_requisition.worksheet_requisition_id', 'mustahik_worksheet_result.worksheet_requisition_id')
        ->join('trans_service_requisition', 'mustahik_worksheet_requisition.service_requisition_id', 'trans_service_requisition.service_requisition_id')
        ->join('system_user', 'mustahik_worksheet_result.user_id', 'system_user.user_id')
        ->join('core_service', 'core_service.service_id', 'trans_service_requisition.service_id')
        ->where('mustahik_worksheet_result.worksheet_result_id', $worksheet_result_id)
        ->first();

        $username = User::select('name', 'full_name')
        ->where('user_id','=',Auth::id())
        ->first();

        $this->set_log(Auth::id(), $username['name'],'1089','Application.MustahikWorksheetResult.print',$username['name'],'Print');

        $pdf = new TCPDF('P', PDF_UNIT, 'A4', true, 'UTF-8', false);

        $pdf::SetPrintHeader(false);
        $pdf::SetPrintFooter(false);

        $pdf::SetMargins(20, 6, 20, 6);

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

        $path   = public_path('resources/img/logosmart/logobaznas01.png');
        $score  = 0;

        $export = "
        <br></br>
        <div style=\"text-align:center;\">
            <br/>
            <br/>
            <br/>
            <br/>
            <br/>
            <br/>
        </div>
        <table cellspacing=\"0\" cellpadding=\"2\" border=\"0\">
            <tr>
                <td align=\"center\" style=\"font-weight:bold\">SCORING MUSTAHIK</td>
            </tr>
        </table>
        <br/>
        <br/>
        <br/>
        <table cellspacing=\"5\" cellpadding=\"2\" border=\"0\">
            <tr>
                <td width=\"5%\" style=\"font-weight:bold\">I.</td>
                <td width=\"95%\" style=\"font-weight:bold\">DETAIL PENGAJUAN</td>
            </tr>
            <tr>
                <td width=\"5%\"></td>
                <td width=\"3%\">1.</td>
                <td width=\"25%\">Nomor Pengajuan</td>
                <td width=\"3%\">:</td>
                <td width=\"55%\">".$mustahikworksheetdetail['service_requisition_no']."</td>
            </tr>
            <tr>
                <td width=\"5%\"></td>
                <td width=\"3%\">2.</td>
                <td width=\"25%\">Tanggal Pengajuan</td>
                <td width=\"3%\">:</td>
                <td width=\"55%\">".$mustahikworksheetdetail['created_at']."</td>
            </tr>
            <tr>
                <td width=\"5%\"></td>
                <td width=\"3%\">3.</td>
                <td width=\"25%\">Nama Pemohon</td>
                <td width=\"3%\">:</td>
                <td width=\"55%\">".$mustahikworksheetdetail['service_requisition_name']."</td>
            </tr>
            <tr>
                <td width=\"5%\"></td>
                <td width=\"3%\">4.</td>
                <td width=\"25%\">Nama Layanan</td>
                <td width=\"3%\">:</td>
                <td width=\"55%\">".$mustahikworksheetdetail['service_name']."</td>
            </tr>
            <tr>
                <td width=\"5%\"></td>
                <td width=\"3%\">5.</td>
                <td width=\"25%\">Nama Surveyor</td>
                <td width=\"3%\">:</td>
                <td width=\"55%\">".$mustahikworksheetdetail['full_name']."</td>
            </tr>
            <tr>
                <td width=\"5%\"></td>
                <td width=\"3%\">6.</td>
                <td width=\"25%\">Tanggal Penilaian</td>
                <td width=\"3%\">:</td>
                <td width=\"55%\">".$mustahikworksheetdetail['worksheet_result_date']."</td>
            </tr>
        </table>
        <table cellspacing=\"5\" cellpadding=\"2\" border=\"0\">
            <tr>
                <td width=\"5%\" style=\"font-weight:bold\">II.</td>
                <td width=\"95%\" style=\"font-weight:bold\">SCORING</td>
            </tr>
        </table>
        <table cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
            <tr>
                <td width=\"5%\"></td>
                <td width=\"95%\">
                    <table cellspacing=\"0\" cellpadding=\"0\" border=\"1\">
                        <tr>
                            <td width=\"5%\" align=\"center\" style=\"font-weight:bold\">No</td>
                            <td width=\"30%\" align=\"center\" style=\"font-weight:bold\">Index Rumah</td>
                            <td width=\"35%\" align=\"center\" style=\"font-weight:bold\">Kriteria</td>
                            <td width=\"30%\" align=\"center\" style=\"font-weight:bold\">Bobot Nilai</td>
                        </tr>
                        <tr>
                            <td width=\"5%\" align=\"center\" rowspan=\"4\">1. </td>
                            <td width=\"30%\" rowspan=\"4\">Ukuran Rumah</td>";
                            if($data['worksheet_home_size_type'] >= 0 && $data['worksheet_home_size_type'] <= 40) {
                                $export .= "
                                <td width=\"35%\" align=\"left\" style=\"background-color:green;color:#FFFF00;\">0 - 40 m2</td>
                                <td width=\"30%\" align=\"center\" style=\"background-color:green;color:#FFFF00;\">0</td>";
                            }else{
                                $export .= "
                                <td width=\"35%\" align=\"left\">0 - 40 m2</td>
                                <td width=\"30%\" align=\"center\">0</td>";
                            }
                        $export .= "
                        </tr>
                        <tr>";
                        if($data['worksheet_home_size_type'] >= 41 && $data['worksheet_home_size_type'] <= 60) {
                            $score +=1;
                            $export .="
                            <td width=\"35%\" align=\"left\" style=\"background-color:green;color:#FFFF00;\">41 - 60 m2</td>
                            <td width=\"30%\" align=\"center\" style=\"background-color:green;color:#FFFF00;\">1</td>
                            ";
                        }else{
                            $export .="
                            <td width=\"35%\" align=\"left\">41 - 60 m2</td>
                            <td width=\"30%\" align=\"center\">1</td>
                            ";
                        }

                        $export .= "
                        </tr>
                        <tr>";
                        if($data['worksheet_home_size_type'] >= 61 && $data['worksheet_home_size_type'] <= 100) {
                            $score +=2;
                            $export .= "
                            <td width=\"35%\" align=\"left\" style=\"background-color:green;color:#FFFF00;\">61 - 100 m2</td>
                            <td width=\"30%\" align=\"center\" style=\"background-color:green;color:#FFFF00;\">2</td>";
                        }else{
                            $export .= "
                            <td width=\"35%\" align=\"left\">61 - 100 m2</td>
                            <td width=\"30%\" align=\"center\">2</td>";
                        }
                        $export .= "
                        </tr>
                        <tr>";
                        if($data['worksheet_home_size_type'] > 100) {
                            $score +=4;
                            $export .= "
                            <td width=\"35%\" align=\"left\" style=\"background-color:green;color:#FFFF00;\">Diatas 100 m2</td>
                            <td width=\"30%\" align=\"center\" style=\"background-color:green;color:#FFFF00;\">4</td>";
                        }else{
                            $export .= "
                            <td width=\"35%\" align=\"left\">Diatas 100 m2</td>
                            <td width=\"30%\" align=\"center\">4</td>";
                        }
                        $export .= "
                        </tr>
                        <tr>
                            <td width=\"5%\" align=\"center\" rowspan=\"4\">2. </td>
                            <td width=\"30%\" rowspan=\"4\">Dinding Rumah</td>
                            ";
                            if($data['worksheet_wall']['wall_bamboo']) {
                                $export .= "
                                <td width=\"35%\" style=\"background-color:green;color:#FFFF00;\">Bilik Bambu</td>
                                <td width=\"30%\" align=\"center\" style=\"background-color:green;color:#FFFF00;\">0</td>";
                            }else{
                                $export .= "
                                <td width=\"35%\">Bilik Bambu</td>
                                <td width=\"30%\" align=\"center\">0</td>";                                
                            }
                        $export .= "
                        </tr>
                        <tr>";
                        if($data['worksheet_wall']['wall_wood']) {
                            $score +=3;
                            $export .= "
                            <td width=\"35%\" style=\"background-color:green;color:#FFFF00;\">Kayu Rotan</td>
                            <td width=\"30%\" align=\"center\" style=\"background-color:green;color:#FFFF00;\">3</td>";
                        }else{
                            $export .= "
                            <td width=\"35%\">Kayu Rotan</td>
                            <td width=\"30%\" align=\"center\">3</td>";
                        }
                        $export .= "
                        </tr>
                        <tr>";
                        if($data['worksheet_wall']['wall_mix']) {
                            $score +=4;
                            $export .= "
                            <td width=\"35%\" style=\"background-color:green;color:#FFFF00;\">Campuran Tembok Kayu</td>
                            <td width=\"30%\" align=\"center\" style=\"background-color:green;color:#FFFF00;\">4</td>";
                        }else{
                            $export .= "
                            <td width=\"35%\">Campuran Tembok Kayu</td>
                            <td width=\"30%\" align=\"center\">4</td>";
                        }
                        $export .= "
                        </tr>
                        <tr>";
                        if($data['worksheet_wall']['wall_plaster']) {
                            $score +=5;
                            $export .= "
                            <td width=\"35%\" style=\"background-color:green;color:#FFFF00;\">Tembok Plester</td>
                            <td width=\"30%\" align=\"center\" style=\"background-color:green;color:#FFFF00;\">5</td>";
                        }else{
                            $export .= "
                            <td width=\"35%\">Tembok Plester</td>
                            <td width=\"30%\" align=\"center\">5</td>";
                        }
                        $export .= "
                        </tr>
                        <tr>
                            <td width=\"5%\" align=\"center\" rowspan=\"4\">3. </td>
                            <td width=\"30%\" rowspan=\"4\">Lantai</td>";
                            if($data['worksheet_floor']['floor_sand']) {
                                $export .= "
                                <td width=\"35%\" style=\"background-color:green;color:#FFFF00;\">Tanah</td>
                                <td width=\"30%\" align=\"center\" style=\"background-color:green;color:#FFFF00;\">0</td>";
                            }else{
                                $export .= "
                                <td width=\"35%\">Tanah</td>
                                <td width=\"30%\" align=\"center\">0</td>";
                            }
                        $export .= "
                        </tr>
                        <tr>";
                        if($data['worksheet_floor']['floor_wood']) {
                            $score +=2;
                            $export .= "
                            <td width=\"35%\" style=\"background-color:green;color:#FFFF00;\">Kayu</td>
                            <td width=\"30%\" align=\"center\" style=\"background-color:green;color:#FFFF00;\">2</td>";
                        }else{
                            $export .= "
                            <td width=\"35%\">Kayu</td>
                            <td width=\"30%\" align=\"center\">2</td>";                            
                        }
                        $export .= "
                        </tr>
                        <tr>";
                        if($data['worksheet_floor']['floor_cement']) {
                            $score +=4;
                            $export .= "
                            <td width=\"35%\" style=\"background-color:green;color:#FFFF00;\">Semen</td>
                            <td width=\"30%\" align=\"center\" style=\"background-color:green;color:#FFFF00;\">4</td>";
                        }else{
                            $export .= "
                            <td width=\"35%\">Semen</td>
                            <td width=\"30%\" align=\"center\">4</td>";
                        }
                        $export .= "
                        </tr>
                        <tr>";
                        if($data['worksheet_floor']['floor_ceramic']) {
                            $score +=5;
                            $export .= "
                            <td width=\"35%\" style=\"background-color:green;color:#FFFF00;\">Keramik</td>
                            <td width=\"30%\" align=\"center\" style=\"background-color:green;color:#FFFF00;\">5</td>";
                        }else{
                            $export .= "
                            <td width=\"35%\">Keramik</td>
                            <td width=\"30%\" align=\"center\">5</td>";
                        }
                        $export .= "
                        </tr>
                        <tr>
                            <td width=\"5%\" align=\"center\" rowspan=\"3\">4. </td>
                            <td width=\"30%\" rowspan=\"3\">Atap</td>";
                        if($data['worksheet_roof']['roof_asbes']) {
                            $score +=1;
                            $export .= "
                            <td width=\"35%\" style=\"background-color:green;color:#FFFF00;\">Asbes</td>
                            <td width=\"30%\" align=\"center\" style=\"background-color:green;color:#FFFF00;\">1</td>";
                        }else{
                            $export .= "
                            <td width=\"35%\">Asbes</td>
                            <td width=\"30%\" align=\"center\">1</td>";
                        }
                        $export .= "
                        </tr>
                        <tr>";
                        if($data['worksheet_roof']['roof_metal']) {
                            $score +=3;
                            $export .= "
                            <td width=\"35%\" style=\"background-color:green;color:#FFFF00;\">Seng Metal</td>
                            <td width=\"30%\" align=\"center\" style=\"background-color:green;color:#FFFF00;\">3</td>";
                        }else{
                            $export .= "
                            <td width=\"35%\">Seng Metal</td>
                            <td width=\"30%\" align=\"center\">3</td>";
                        }
                        $export .= "
                        </tr>
                        <tr>";
                        if($data['worksheet_roof']['roof_tile']) {
                            $score +=5;
                            $export .= "
                            <td width=\"35%\" style=\"background-color:green;color:#FFFF00;\">Genteng</td>
                            <td width=\"30%\" align=\"center\" style=\"background-color:green;color:#FFFF00;\">5</td>";
                        }else{
                            $export .= "
                            <td width=\"35%\">Genteng</td>
                            <td width=\"30%\" align=\"center\">5</td>";
                        }
                        $export .= "
                        </tr>
                        <tr>
                            <td width=\"5%\" align=\"center\" rowspan=\"3\">5. </td>
                            <td width=\"30%\" rowspan=\"3\">Sanitasi</td>";
                            if($data['worksheet_sanitation']['sanitation_bath_room']) {
                                $score +=3;
                                $export .= "
                                <td width=\"35%\" style=\"background-color:green;color:#FFFF00;\">Kamar Mandi</td>
                                <td width=\"30%\" align=\"center\" style=\"background-color:green;color:#FFFF00;\">3</td>";
                            }else{
                                $export .= "
                                <td width=\"35%\">Kamar Mandi</td>
                                <td width=\"30%\" align=\"center\">3</td>";
                            }
                        $export .= "
                        </tr>
                        <tr>";
                        if($data['worksheet_sanitation']['sanitation_mck']) {
                            $score +=3;
                            $export .= "
                            <td width=\"35%\" style=\"background-color:green;color:#FFFF00;\">MCK</td>
                            <td width=\"30%\" align=\"center\" style=\"background-color:green;color:#FFFF00;\">3</td>";
                        }else{
                            $export .= "
                            <td width=\"35%\">MCK</td>
                            <td width=\"30%\" align=\"center\">3</td>";
                        }
                        $export .= "
                        </tr>
                        <tr>";
                        if($data['worksheet_sanitation']['sanitation_well']) {
                            $score +=3;
                            $export .= "
                            <td width=\"35%\" style=\"background-color:green;color:#FFFF00;\">Sumur / Sumber Air</td>
                            <td width=\"30%\" align=\"center\" style=\"background-color:green;color:#FFFF00;\">3</td>";
                        }else{
                            $export .= "
                            <td width=\"35%\">Sumur / Sumber Air</td>
                            <td width=\"30%\" align=\"center\">3</td>";
                        }
                        $export .= "
                        </tr>
                        <tr>
                            <td width=\"5%\" align=\"center\" rowspan=\"2\">6. </td>
                            <td width=\"30%\" rowspan=\"2\">Listrik</td>";
                            if($data['worksheet_electricity']['electricity_private']) {
                                $score +=2;
                                $export .= "
                                <td width=\"35%\" style=\"background-color:green;color:#FFFF00;\">KWH Pribadi</td>
                                <td width=\"30%\" align=\"center\" style=\"background-color:green;color:#FFFF00;\">2</td>";
                            }else{
                                $export .= "
                                <td width=\"35%\">KWH Pribadi</td>
                                <td width=\"30%\" align=\"center\">2</td>";
                            }
                        $export .= "
                        </tr>
                        <tr>";
                        if($data['worksheet_electricity']['electricity_connect']) {
                            $score +=5;
                            $export .= "
                            <td width=\"35%\" style=\"background-color:green;color:#FFFF00;\">Menyambung</td>
                            <td width=\"30%\" align=\"center\" style=\"background-color:green;color:#FFFF00;\">5</td>";
                        }else{
                            $export .= "
                            <td width=\"35%\">Menyambung</td>
                            <td width=\"30%\" align=\"center\">5</td>";
                        }
                        $export .= "
                        </tr>
                        <tr>
                            <td width=\"5%\" align=\"center\" rowspan=\"3\">7. </td>
                            <td width=\"30%\" rowspan=\"3\">Kepemilikan Rumah</td>";
                            if($data['worksheet_ownership']['ownership_rent']) {
                                $score +=5;
                                $export .= "
                                <td width=\"35%\" style=\"background-color:green;color:#FFFF00;\">Sewa</td>
                                <td width=\"30%\" align=\"center\" style=\"background-color:green;color:#FFFF00;\">5</td>";
                            }else{
                                $export .= "
                                <td width=\"35%\">Sewa</td>
                                <td width=\"30%\" align=\"center\">5</td>";
                            }
                        $export .= "
                        </tr>
                        <tr>";
                        if($data['worksheet_ownership']['ownership_family']) {
                            $score +=3;
                            $export .= "
                            <td width=\"35%\" style=\"background-color:green;color:#FFFF00;\">Keluarga</td>
                            <td width=\"30%\" align=\"center\" style=\"background-color:green;color:#FFFF00;\">3</td>";
                        }else{
                            $export .= "
                            <td width=\"35%\">Keluarga</td>
                            <td width=\"30%\" align=\"center\">3</td>";
                        }
                        $export .= "
                        </tr>
                        <tr>";
                        if($data['worksheet_ownership']['ownership_self']) {
                            $export .= "
                            <td width=\"35%\" style=\"background-color:green;color:#FFFF00;\">Milik Sendiri</td>
                            <td width=\"30%\" align=\"center\" style=\"background-color:green;color:#FFFF00;\">0</td>";
                        }else{
                            $export .= "
                            <td width=\"35%\">Milik Sendiri</td>
                            <td width=\"30%\" align=\"center\">0</td>";
                        }
                        $export .= "
                        </tr>
                        <tr>
                            <td width=\"5%\" align=\"center\" rowspan=\"4\">7. </td>
                            <td width=\"30%\" rowspan=\"4\">Total Penghasilan / Tahun</td>";
                    
                        $penghasilan = 0; 
                        if($data['worksheet_husband_business_yearly']){
                            $penghasilan += $data['worksheet_husband_business_yearly'];
                        }
                        if($data['worksheet_wife_business_yearly']){
                            $penghasilan += $data['worksheet_wife_business_yearly'];
                        }
                        if($data['worksheet_parents_yearly']){
                            $penghasilan += $data['worksheet_parents_yearly'];
                        }
                        if($data['worksheet_childs_yearly']){
                            $penghasilan += $data['worksheet_childs_yearly'];
                        }
                        if($data['worksheet_other_yearly']){
                            $penghasilan += $data['worksheet_other_yearly'];
                        }

                            if($penghasilan >= 0 && $penghasilan <= 5000000) {
                                $export .= "
                                <td width=\"35%\" align=\"left\" style=\"background-color:green;color:#FFFF00;\">0 - 5.000.000</td>
                                <td width=\"30%\" align=\"center\" style=\"background-color:green;color:#FFFF00;\">0</td>";
                            }else{
                                $export .= "
                                <td width=\"35%\" align=\"left\">0 - 5.000.000</td>
                                <td width=\"30%\" align=\"center\">0</td>";
                            }
                        $export .= "
                        </tr>
                        <tr>";
                        if($penghasilan >= 5000001 && $penghasilan <= 10000000) {
                            $score +=2;
                            $export .= "
                            <td width=\"35%\" align=\"left\" style=\"background-color:green;color:#FFFF00;\">5.000.001 - 10.000.000</td>
                            <td width=\"30%\" align=\"center\" style=\"background-color:green;color:#FFFF00;\">2</td>";
                        }else{
                            $export .= "
                            <td width=\"35%\" align=\"left\">5.000.001 - 10.000.000</td>
                            <td width=\"30%\" align=\"center\">2</td>";
                        }
                        $export .= "
                        </tr>
                        <tr>";
                        if($penghasilan >= 10000001 && $penghasilan <= 15000000) {
                            $score +=4;
                            $export .= "
                            <td width=\"35%\" align=\"left\" style=\"background-color:green;color:#FFFF00;\">10.000.001 - 15.000.000</td>
                            <td width=\"30%\" align=\"center\" style=\"background-color:green;color:#FFFF00;\">4</td>";
                        }else{
                            $export .= "
                            <td width=\"35%\" align=\"left\">10.000.001 - 15.000.000</td>
                            <td width=\"30%\" align=\"center\">4</td>";
                        }
                        $export .= "
                        </tr>
                        <tr>";
                        if($penghasilan >= 15000001) {
                            $score +=5;
                            $export .= "
                            <td width=\"35%\" align=\"left\" style=\"background-color:green;color:#FFFF00;\">Diatas 15 Juta</td>
                            <td width=\"30%\" align=\"center\" style=\"background-color:green;color:#FFFF00;\">5</td>";
                        }else{
                            $export .= "
                            <td width=\"35%\" align=\"left\">Diatas 15 Juta</td>
                            <td width=\"30%\" align=\"center\">5</td>";
                        }
                        $export .= "
                        </tr>
                        <tr>
                            <td width=\"70%\" align=\"center\" style=\"font-weight: bold;\">Total</td>
                            <td width=\"30%\" align=\"center\" style=\"font-weight: bold;\">".$score."</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <br/>
        <br/>
        <br/>
        <table cellspacing=\"0\" cellpadding=\"2\" border=\"0\">
            <tr>
                <td width=\"5%\"></td>
                <td width=\"50%\">
                    <table cellspacing=\"0\" cellpadding=\"2\" border=\"1\">
                        <tr>
                            <td width=\"60%\" align=\"center\" style=\"font-weight: bold;\">Nilai Scoring</td>
                            <td width=\"40%\" align=\"center\" style=\"font-weight: bold;\">Kategori</td>
                        </tr>
                        <tr>
                            <td width=\"60%\" align=\"center\">0 - 20</td>
                            <td width=\"40%\" align=\"center\">Approve</td>
                        </tr>
                        <tr>
                            <td width=\"60%\" align=\"center\">21 - 39</td>
                            <td width=\"40%\" align=\"center\">Komite</td>
                        </tr>
                        <tr>
                            <td width=\"60%\" align=\"center\">40 keatas</td>
                            <td width=\"40%\" align=\"center\">Reject</td>
                        </tr>
                    </table>
                </td>";

                $score_result = "";
                if($score <= 20){
                    $score_result = "Aprrove";
                }else if($score >= 21 && $score <= 39){
                    $score_result = "Komite";
                }else if($score >= 40){
                    $score_result = "Reject";
                } 

                $export .= "<td width=\"45%\">
                    <table cellspacing=\"0\" cellpadding=\"2\" border=\"1\">
                        <tr>
                            <td width=\"100%\" align=\"center\" style=\"font-weight: bold;\">Hasil Scoring</td>
                        </tr>
                        <tr>
                            <td width=\"100%\" align=\"center\">".$score_result."</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        ";
        
        $pdf::Image( $path, 4, 4, 25, 25, 'PNG', '', 'LT', false, 300, 'C', false, false, 1, false, false, false);
        $pdf::writeHTML($export, true, false, false, false, '');

        // ob_clean();

        $filename = 'Scoring_'.$worksheet_result_id.'.pdf';
        $pdf::Output($filename, 'I');
    }
    
    public function printScoringMustahikWorksheetResultMU($worksheet_result_id){
        $mustahikworksheetresult = MustahikWorksheetResult::select('mustahik_worksheet_result.worksheet_result_data', 'mustahik_worksheet_result.worksheet_result_date', 'mustahik_worksheet_result.user_id', 'mustahik_worksheet_requisition.service_id', 'mustahik_worksheet_requisition.worksheet_requisition_id')
        ->join('mustahik_worksheet_requisition', 'mustahik_worksheet_requisition.worksheet_requisition_id', 'mustahik_worksheet_result.worksheet_requisition_id')
        ->where('mustahik_worksheet_result.worksheet_result_id', $worksheet_result_id)
        ->first();

        $mustahikworksheet = MustahikWorksheet::select('worksheet_id', 'worksheet_no', 'worksheet_name', 'worksheet_type', 'worksheet_code')
        ->where('service_id', $mustahikworksheetresult['service_id'])
        ->get();

        $worksheetresultdata = json_decode($mustahikworksheetresult['worksheet_result_data']);

        $data = array();

        foreach($mustahikworksheet as $key => $val){
            if($val['worksheet_type'] == 1 || $val['worksheet_type'] == 3 || $val['worksheet_type'] == 4){
                $data[$val['worksheet_code']] = '';
            }else if($val['worksheet_type'] == 2){
                $data[$val['worksheet_code']] = array();
                $mustahikworksheetitem = MustahikWorksheetItem::select('worksheet_item_id', 'worksheet_id', 'section_name', 'worksheet_item_name', 'worksheet_item_code')
                ->where('worksheet_id', $val['worksheet_id'])
                ->get();

                foreach($mustahikworksheetitem as $keyy => $vall){
                    $data[$val['worksheet_code']][$vall['worksheet_item_code']] = '';
                }
            }
        }

        foreach($data as $key => $val){
            foreach($worksheetresultdata as $keyy => $vall){
                if($key == $vall->worksheet_code){
                    if(isset($vall->value)){
                        $data[$key] = $vall->value;
                    }else if(isset($vall->worksheetitem)){
                        foreach($vall->worksheetitem as $keyyy => $valll){
                            $data[$key][$valll->worksheet_item_code] = $valll->value;
                        }
                    }
                }
            }
        }

        $mustahikworksheetdetail = MustahikWorksheetResult::select('mustahik_worksheet_result.worksheet_result_id', 'mustahik_worksheet_result.worksheet_result_date', 'system_user.full_name', 'trans_service_requisition.service_requisition_no', 'trans_service_requisition.service_requisition_name', 'trans_service_requisition.created_at', 'core_service.service_name')
        ->join('mustahik_worksheet_requisition', 'mustahik_worksheet_requisition.worksheet_requisition_id', 'mustahik_worksheet_result.worksheet_requisition_id')
        ->join('trans_service_requisition', 'mustahik_worksheet_requisition.service_requisition_id', 'trans_service_requisition.service_requisition_id')
        ->join('system_user', 'mustahik_worksheet_result.user_id', 'system_user.user_id')
        ->join('core_service', 'core_service.service_id', 'trans_service_requisition.service_id')
        ->where('mustahik_worksheet_result.worksheet_result_id', $worksheet_result_id)
        ->first();

        $username = User::select('name', 'full_name')
        ->where('user_id','=',Auth::id())
        ->first();

        $this->set_log(Auth::id(), $username['name'],'1089','Application.MustahikWorksheetResult.print',$username['name'],'Print');

        $pdf = new TCPDF('P', PDF_UNIT, 'A4', true, 'UTF-8', false);

        $pdf::SetPrintHeader(false);
        $pdf::SetPrintFooter(false);

        $pdf::SetMargins(20, 6, 20, 6);

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

        $path   = public_path('resources/img/logosmart/logobaznas01.png');
        $score  = 0;

        $export = "
        <br></br>
        <div style=\"text-align:center;\">
            <br/>
            <br/>
            <br/>
            <br/>
            <br/>
            <br/>
        </div>
        <table cellspacing=\"0\" cellpadding=\"2\" border=\"0\">
            <tr>
                <td align=\"center\" style=\"font-weight:bold\">SCORING MUSTAHIK</td>
            </tr>
        </table>
        <br/>
        <br/>
        <br/>
        <table cellspacing=\"5\" cellpadding=\"2\" border=\"0\">
            <tr>
                <td width=\"5%\" style=\"font-weight:bold\">I.</td>
                <td width=\"95%\" style=\"font-weight:bold\">DETAIL PENGAJUAN</td>
            </tr>
            <tr>
                <td width=\"5%\"></td>
                <td width=\"3%\">1.</td>
                <td width=\"25%\">Nomor Pengajuan</td>
                <td width=\"3%\">:</td>
                <td width=\"55%\">".$mustahikworksheetdetail['service_requisition_no']."</td>
            </tr>
            <tr>
                <td width=\"5%\"></td>
                <td width=\"3%\">2.</td>
                <td width=\"25%\">Tanggal Pengajuan</td>
                <td width=\"3%\">:</td>
                <td width=\"55%\">".$mustahikworksheetdetail['created_at']."</td>
            </tr>
            <tr>
                <td width=\"5%\"></td>
                <td width=\"3%\">3.</td>
                <td width=\"25%\">Nama Pemohon</td>
                <td width=\"3%\">:</td>
                <td width=\"55%\">".$mustahikworksheetdetail['service_requisition_name']."</td>
            </tr>
            <tr>
                <td width=\"5%\"></td>
                <td width=\"3%\">4.</td>
                <td width=\"25%\">Nama Layanan</td>
                <td width=\"3%\">:</td>
                <td width=\"55%\">".$mustahikworksheetdetail['service_name']."</td>
            </tr>
            <tr>
                <td width=\"5%\"></td>
                <td width=\"3%\">5.</td>
                <td width=\"25%\">Nama Surveyor</td>
                <td width=\"3%\">:</td>
                <td width=\"55%\">".$mustahikworksheetdetail['full_name']."</td>
            </tr>
            <tr>
                <td width=\"5%\"></td>
                <td width=\"3%\">6.</td>
                <td width=\"25%\">Tanggal Penilaian</td>
                <td width=\"3%\">:</td>
                <td width=\"55%\">".$mustahikworksheetdetail['worksheet_result_date']."</td>
            </tr>
        </table>
        <table cellspacing=\"5\" cellpadding=\"2\" border=\"0\">
            <tr>
                <td width=\"5%\" style=\"font-weight:bold\">II.</td>
                <td width=\"95%\" style=\"font-weight:bold\">SCORING</td>
            </tr>
        </table>
        <table cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
            <tr>
                <td width=\"5%\"></td>
                <td width=\"95%\">
                    <table cellspacing=\"0\" cellpadding=\"1\" border=\"1\">
                        <tr>
                            <td width=\"5%\" align=\"center\" style=\"font-weight:bold\">No</td>
                            <td width=\"30%\" align=\"center\" style=\"font-weight:bold\">Index Penilaian</td>
                            <td width=\"35%\" align=\"center\" style=\"font-weight:bold\">Kriteria</td>
                            <td width=\"30%\" align=\"center\" style=\"font-weight:bold\">Bobot Nilai</td>
                        </tr>
                        <tr>
                            <td width=\"5%\" align=\"center\" rowspan=\"2\">1. </td>
                            <td width=\"30%\" rowspan=\"2\">Lokasi Usaha</td>";
                            if($data['business_location']['location_not_strategic'] == true) {
                                $score += 2;
                                $export .= "
                                <td width=\"35%\" align=\"left\" style=\"background-color:green;color:#FFFF00;\">Tidak Strategis</td>
                                <td width=\"30%\" align=\"center\" style=\"background-color:green;color:#FFFF00;\">2</td>";
                            }else{
                                $export .= "
                                <td width=\"35%\" align=\"left\">Tidak Strategis</td>
                                <td width=\"30%\" align=\"center\">2</td>";
                            }
                        $export .= "
                        </tr>
                        <tr>";
                        if($data['business_location']['location_strategic'] == true) {
                            $score += 4;
                            $export .="
                            <td width=\"35%\" align=\"left\" style=\"background-color:green;color:#FFFF00;\">Strategis</td>
                            <td width=\"30%\" align=\"center\" style=\"background-color:green;color:#FFFF00;\">4</td>
                            ";
                        }else{
                            $export .="
                            <td width=\"35%\" align=\"left\">Strategis</td>
                            <td width=\"30%\" align=\"center\">4</td>
                            ";
                        }

                        $export .= "
                        </tr>
                        <tr>
                            <td width=\"5%\" align=\"center\" rowspan=\"2\">2. </td>
                            <td width=\"30%\" rowspan=\"2\">Tipe Usaha</td>
                            ";
                            if($data['business_community']['community_group']) {
                                $score += 2;
                                $export .= "
                                <td width=\"35%\" style=\"background-color:green;color:#FFFF00;\">Kelompok</td>
                                <td width=\"30%\" align=\"center\" style=\"background-color:green;color:#FFFF00;\">2</td>";
                            }else{
                                $export .= "
                                <td width=\"35%\">Kelompok</td>
                                <td width=\"30%\" align=\"center\">2</td>";                                
                            }
                        $export .= "
                        </tr>
                        <tr>";
                        if($data['business_community']['community_individual']) {
                            $score += 4;
                            $export .= "
                            <td width=\"35%\" style=\"background-color:green;color:#FFFF00;\">Personal</td>
                            <td width=\"30%\" align=\"center\" style=\"background-color:green;color:#FFFF00;\">4</td>";
                        }else{
                            $export .= "
                            <td width=\"35%\">Personal</td>
                            <td width=\"30%\" align=\"center\">4</td>";
                        }

                        $export .= "
                        </tr>
                        <tr>
                            <td width=\"5%\" align=\"center\" rowspan=\"4\">3. </td>
                            <td width=\"30%\" rowspan=\"4\">Lama Usaha</td>
                            ";
                            if(!$data['business_age'] || $data['business_age'] < 6) {
                                $score += 1;
                                $export .= "
                                <td width=\"35%\" style=\"background-color:green;color:#FFFF00;\">Dibawah 6 Bulan</td>
                                <td width=\"30%\" align=\"center\" style=\"background-color:green;color:#FFFF00;\">1</td>";
                            }else{
                                $export .= "
                                <td width=\"35%\">Dibawah 6 Bulan</td>
                                <td width=\"30%\" align=\"center\">1</td>";                                
                            }
                        $export .= "
                        </tr>
                        <tr>";
                        if($data['business_age'] >= 6 && $data['business_age'] <= 12) {
                            $score += 2;
                            $export .= "
                            <td width=\"35%\" style=\"background-color:green;color:#FFFF00;\">6 Bulan - 1 Tahun</td>
                            <td width=\"30%\" align=\"center\" style=\"background-color:green;color:#FFFF00;\">2</td>";
                        }else{
                            $export .= "
                            <td width=\"35%\">6 Bulan - 1 Tahun</td>
                            <td width=\"30%\" align=\"center\">2</td>";
                        }
                        $export .= "
                        </tr>
                        <tr>";
                        if($data['business_age'] >= 13 && $data['business_age'] <= 24) {
                            $score += 3;
                            $export .= "
                            <td width=\"35%\" style=\"background-color:green;color:#FFFF00;\">1 Tahun - 2 Tahun</td>
                            <td width=\"30%\" align=\"center\" style=\"background-color:green;color:#FFFF00;\">3</td>";
                        }else{
                            $export .= "
                            <td width=\"35%\">1 Tahun - 2 Tahun</td>
                            <td width=\"30%\" align=\"center\">3</td>";
                        }
                        $export .= "
                        </tr>
                        <tr>";
                        if($data['business_age'] > 24) {
                            $score += 4;
                            $export .= "
                            <td width=\"35%\" style=\"background-color:green;color:#FFFF00;\">Diatas 2 Tahun</td>
                            <td width=\"30%\" align=\"center\" style=\"background-color:green;color:#FFFF00;\">4</td>";
                        }else{
                            $export .= "
                            <td width=\"35%\">Diatas 2 Tahun</td>
                            <td width=\"30%\" align=\"center\">4</td>";
                        }

                        $export .= "
                        </tr>
                        <tr>
                            <td width=\"5%\" align=\"center\" rowspan=\"4\">4. </td>
                            <td width=\"30%\" rowspan=\"4\">Jumlah Karyawan / Anggota</td>
                            ";
                            if($data['business_employee'] <= 0) {
                                $score += 1;
                                $export .= "
                                <td width=\"35%\" style=\"background-color:green;color:#FFFF00;\">Sendiri</td>
                                <td width=\"30%\" align=\"center\" style=\"background-color:green;color:#FFFF00;\">1</td>";
                            }else{
                                $export .= "
                                <td width=\"35%\">Sendiri</td>
                                <td width=\"30%\" align=\"center\">1</td>";                                
                            }
                        $export .= "
                        </tr>
                        <tr>";
                        if($data['business_employee'] >= 1 && $data['business_employee'] <= 3) {
                            $score += 2;
                            $export .= "
                            <td width=\"35%\" style=\"background-color:green;color:#FFFF00;\">1 - 3 Orang</td>
                            <td width=\"30%\" align=\"center\" style=\"background-color:green;color:#FFFF00;\">2</td>";
                        }else{
                            $export .= "
                            <td width=\"35%\">1 - 3 Orang</td>
                            <td width=\"30%\" align=\"center\">2</td>";
                        }
                        $export .= "
                        </tr>
                        <tr>";
                        if($data['business_employee'] >= 4 && $data['business_employee'] <= 10) {
                            $score += 3;
                            $export .= "
                            <td width=\"35%\" style=\"background-color:green;color:#FFFF00;\">4 - 10 Orang</td>
                            <td width=\"30%\" align=\"center\" style=\"background-color:green;color:#FFFF00;\">3</td>";
                        }else{
                            $export .= "
                            <td width=\"35%\">4 - 10 Orang</td>
                            <td width=\"30%\" align=\"center\">3</td>";
                        }
                        $export .= "
                        </tr>
                        <tr>";
                        if($data['business_employee'] > 10) {
                            $score += 4;
                            $export .= "
                            <td width=\"35%\" style=\"background-color:green;color:#FFFF00;\">Diatas 10 Orang</td>
                            <td width=\"30%\" align=\"center\" style=\"background-color:green;color:#FFFF00;\">4</td>";
                        }else{
                            $export .= "
                            <td width=\"35%\">Diatas 10 Orang</td>
                            <td width=\"30%\" align=\"center\">4</td>";
                        }

                        $export .= "
                        </tr>
                        <tr>
                            <td width=\"5%\" align=\"center\" rowspan=\"5\">5. </td>
                            <td width=\"30%\" rowspan=\"5\">Margin Keuntungan</td>
                            ";
                            if($data['business_interest'] < 8) {
                                $score += 1;
                                $export .= "
                                <td width=\"35%\" style=\"background-color:green;color:#FFFF00;\">Dibawah 8%</td>
                                <td width=\"30%\" align=\"center\" style=\"background-color:green;color:#FFFF00;\">1</td>";
                            }else{
                                $export .= "
                                <td width=\"35%\">Dibawah 8%</td>
                                <td width=\"30%\" align=\"center\">1</td>";                                
                            }
                        $export .= "
                        </tr>
                        <tr>";
                        if($data['business_interest'] >= 8 && $data['business_interest'] <= 10) {
                            $score += 2;
                            $export .= "
                            <td width=\"35%\" style=\"background-color:green;color:#FFFF00;\">8% - 10%</td>
                            <td width=\"30%\" align=\"center\" style=\"background-color:green;color:#FFFF00;\">2</td>";
                        }else{
                            $export .= "
                            <td width=\"35%\">8% - 10%</td>
                            <td width=\"30%\" align=\"center\">2</td>";
                        }
                        $export .= "
                        </tr>
                        <tr>";
                        if($data['business_interest'] >= 11 && $data['business_interest'] <= 15) {
                            $score += 3;
                            $export .= "
                            <td width=\"35%\" style=\"background-color:green;color:#FFFF00;\">11% - 15%</td>
                            <td width=\"30%\" align=\"center\" style=\"background-color:green;color:#FFFF00;\">3</td>";
                        }else{
                            $export .= "
                            <td width=\"35%\">11% - 15%</td>
                            <td width=\"30%\" align=\"center\">3</td>";
                        }
                        $export .= "
                        </tr>
                        <tr>";
                        if($data['business_interest'] >= 16 && $data['business_interest'] <= 20) {
                            $score += 4;
                            $export .= "
                            <td width=\"35%\" style=\"background-color:green;color:#FFFF00;\">16% - 20%</td>
                            <td width=\"30%\" align=\"center\" style=\"background-color:green;color:#FFFF00;\">4</td>";
                        }else{
                            $export .= "
                            <td width=\"35%\">16% - 20%</td>
                            <td width=\"30%\" align=\"center\">4</td>";
                        }
                        $export .= "
                        </tr>
                        <tr>";
                        if($data['business_interest'] > 20) {
                            $score += 5;
                            $export .= "
                            <td width=\"35%\" style=\"background-color:green;color:#FFFF00;\">Diatas 20%</td>
                            <td width=\"30%\" align=\"center\" style=\"background-color:green;color:#FFFF00;\">5</td>";
                        }else{
                            $export .= "
                            <td width=\"35%\">Diatas 20%</td>
                            <td width=\"30%\" align=\"center\">5</td>";
                        }

                        $export .= "
                        </tr>
                        <tr>
                            <td width=\"5%\" align=\"center\" rowspan=\"4\">6. </td>
                            <td width=\"30%\" rowspan=\"4\">Pernah Mendapat Pinjaman / Bantuan</td>
                            ";
                            if($data['business_assistance_loans']['assistance_loans'] == true) {
                                $score += 1;
                                $export .= "
                                <td width=\"35%\" style=\"background-color:green;color:#FFFF00;\">Bantuan dan Pinjaman</td>
                                <td width=\"30%\" align=\"center\" style=\"background-color:green;color:#FFFF00;\">1</td>";
                            }else{
                                $export .= "
                                <td width=\"35%\">Bantuan dan Pinjaman</td>
                                <td width=\"30%\" align=\"center\">1</td>";                                
                            }
                        $export .= "
                        </tr>
                        <tr>";
                        if($data['business_assistance_loans']['loans'] == true) {
                            $score += 2;
                            $export .= "
                            <td width=\"35%\" style=\"background-color:green;color:#FFFF00;\">Pinjaman</td>
                            <td width=\"30%\" align=\"center\" style=\"background-color:green;color:#FFFF00;\">2</td>";
                        }else{
                            $export .= "
                            <td width=\"35%\">Pinjaman</td>
                            <td width=\"30%\" align=\"center\">2</td>";
                        }
                        $export .= "
                        </tr>
                        <tr>";
                        if($data['business_assistance_loans']['assistance'] == true) {
                            $score += 3;
                            $export .= "
                            <td width=\"35%\" style=\"background-color:green;color:#FFFF00;\">Bantuan</td>
                            <td width=\"30%\" align=\"center\" style=\"background-color:green;color:#FFFF00;\">3</td>";
                        }else{
                            $export .= "
                            <td width=\"35%\">Bantuan</td>
                            <td width=\"30%\" align=\"center\">3</td>";
                        }
                        $export .= "
                        </tr>
                        <tr>";
                        if($data['business_assistance_loans']['no_assistance_loans'] == true) {
                            $score += 4;
                            $export .= "
                            <td width=\"35%\" style=\"background-color:green;color:#FFFF00;\">Belum Pernah</td>
                            <td width=\"30%\" align=\"center\" style=\"background-color:green;color:#FFFF00;\">4</td>";
                        }else{
                            $export .= "
                            <td width=\"35%\">Belum Pernah</td>
                            <td width=\"30%\" align=\"center\">4</td>";
                        }
                        
                        if($data['category']['asnaf_poor'] == true){
                            $export .= "
                            </tr>
                            <tr>
                                <td width=\"5%\" align=\"center\" rowspan=\"2\">7. </td>
                                <td width=\"30%\" rowspan=\"2\">SKTM</td>
                                ";
                                if($data['sktm']['no_sktm'] == true) {
                                    $score += 2;
                                    $export .= "
                                    <td width=\"35%\" style=\"background-color:green;color:#FFFF00;\">Tidak Ada</td>
                                    <td width=\"30%\" align=\"center\" style=\"background-color:green;color:#FFFF00;\">2</td>";
                                }else{
                                    $export .= "
                                    <td width=\"35%\">Tidak Ada</td>
                                    <td width=\"30%\" align=\"center\">2</td>";                                
                                }
                            $export .= "
                            </tr>
                            <tr>";
                            if($data['sktm']['sktm'] == true) {
                                $score += 4;
                                $export .= "
                                <td width=\"35%\" style=\"background-color:green;color:#FFFF00;\">Ada</td>
                                <td width=\"30%\" align=\"center\" style=\"background-color:green;color:#FFFF00;\">4</td>";
                            }else{
                                $export .= "
                                <td width=\"35%\">Ada</td>
                                <td width=\"30%\" align=\"center\">4</td>";
                            }
                        }

                        $export .= "
                        </tr>
                        <tr>
                            <td width=\"70%\" align=\"center\" style=\"font-weight: bold;\">Total</td>
                            <td width=\"30%\" align=\"center\" style=\"font-weight: bold;\">".$score."</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <br/>
        <br/>
        <br/>
        <table cellspacing=\"0\" cellpadding=\"2\" border=\"0\">
            <tr>
                <td width=\"5%\"></td>
                <td width=\"50%\">
                    <table cellspacing=\"0\" cellpadding=\"2\" border=\"1\">
                        <tr>
                            <td width=\"60%\" align=\"center\" style=\"font-weight: bold;\">Nilai Scoring</td>
                            <td width=\"40%\" align=\"center\" style=\"font-weight: bold;\">Kategori</td>
                        </tr>";
                        
                        if($data['category']['asnaf_poor'] == true){
                            $export .= "<tr>
                                <td width=\"60%\" align=\"center\">22 Keatas</td>
                                <td width=\"40%\" align=\"center\">Approve</td>
                            </tr>
                            <tr>
                                <td width=\"60%\" align=\"center\">9 - 21</td>
                                <td width=\"40%\" align=\"center\">Komite</td>
                            </tr>
                            <tr>
                                <td width=\"60%\" align=\"center\">0 - 8</td>
                                <td width=\"40%\" align=\"center\">Reject</td>
                            </tr>";
                        }else{
                            $export .= "<tr>
                                <td width=\"60%\" align=\"center\">20 Keatas</td>
                                <td width=\"40%\" align=\"center\">Approve</td>
                            </tr>
                            <tr>
                                <td width=\"60%\" align=\"center\">9 - 19</td>
                                <td width=\"40%\" align=\"center\">Komite</td>
                            </tr>
                            <tr>
                                <td width=\"60%\" align=\"center\">0 - 8</td>
                                <td width=\"40%\" align=\"center\">Reject</td>
                            </tr>";
                        }
                        
                $export .=" </table>
                </td>";

                $score_result = "";
                
                if($data['category']['asnaf_poor'] == true){
                    if($score >= 22){
                        $score_result = "Aprrove";
                    }else if($score >= 9 && $score <= 21){
                        $score_result = "Komite";
                    }else if($score <= 8){
                        $score_result = "Reject";
                    } 
                }else{
                    if($score >= 20){
                        $score_result = "Aprrove";
                    }else if($score >= 9 && $score <= 19){
                        $score_result = "Komite";
                    }else if($score <= 8){
                        $score_result = "Reject";
                    }
                }

                $export .= "<td width=\"45%\">
                    <table cellspacing=\"0\" cellpadding=\"2\" border=\"1\">
                        <tr>
                            <td width=\"100%\" align=\"center\" style=\"font-weight: bold;\">Hasil Scoring</td>
                        </tr>
                        <tr>
                            <td width=\"100%\" align=\"center\">".$score_result."</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        ";
        
        $pdf::Image( $path, 4, 4, 25, 25, 'PNG', '', 'LT', false, 300, 'C', false, false, 1, false, false, false);
        $pdf::writeHTML($export, true, false, false, false, '');

        // ob_clean();

        $filename = 'Scoring_'.$worksheet_result_id.'.pdf';
        $pdf::Output($filename, 'I');
    }
    
    public function printScoringMustahikWorksheetResultMasjid($worksheet_result_id){
        $mustahikworksheetresult = MustahikWorksheetResult::select('mustahik_worksheet_result.worksheet_result_data', 'mustahik_worksheet_result.worksheet_result_date', 'mustahik_worksheet_result.user_id', 'mustahik_worksheet_requisition.service_id', 'mustahik_worksheet_requisition.worksheet_requisition_id')
        ->join('mustahik_worksheet_requisition', 'mustahik_worksheet_requisition.worksheet_requisition_id', 'mustahik_worksheet_result.worksheet_requisition_id')
        ->where('mustahik_worksheet_result.worksheet_result_id', $worksheet_result_id)
        ->first();

        $mustahikworksheet = MustahikWorksheet::select('worksheet_id', 'worksheet_no', 'worksheet_name', 'worksheet_type', 'worksheet_code')
        ->where('service_id', $mustahikworksheetresult['service_id'])
        ->get();

        $worksheetresultdata = json_decode($mustahikworksheetresult['worksheet_result_data']);

        $data = array();

        foreach($mustahikworksheet as $key => $val){
            if($val['worksheet_type'] == 1 || $val['worksheet_type'] == 3 || $val['worksheet_type'] == 4){
                $data[$val['worksheet_code']] = '';
            }else if($val['worksheet_type'] == 2){
                $data[$val['worksheet_code']] = array();
                $mustahikworksheetitem = MustahikWorksheetItem::select('worksheet_item_id', 'worksheet_id', 'section_name', 'worksheet_item_name', 'worksheet_item_code')
                ->where('worksheet_id', $val['worksheet_id'])
                ->get();

                foreach($mustahikworksheetitem as $keyy => $vall){
                    $data[$val['worksheet_code']][$vall['worksheet_item_code']] = '';
                }
            }
        }

        foreach($data as $key => $val){
            foreach($worksheetresultdata as $keyy => $vall){
                if($key == $vall->worksheet_code){
                    if(isset($vall->value)){
                        $data[$key] = $vall->value;
                    }else if(isset($vall->worksheetitem)){
                        foreach($vall->worksheetitem as $keyyy => $valll){
                            $data[$key][$valll->worksheet_item_code] = $valll->value;
                        }
                    }
                }
            }
        }

        $mustahikworksheetdetail = MustahikWorksheetResult::select('mustahik_worksheet_result.worksheet_result_id', 'mustahik_worksheet_result.worksheet_result_date', 'system_user.full_name', 'trans_service_requisition.service_requisition_no', 'trans_service_requisition.service_requisition_name', 'trans_service_requisition.created_at', 'core_service.service_name')
        ->join('mustahik_worksheet_requisition', 'mustahik_worksheet_requisition.worksheet_requisition_id', 'mustahik_worksheet_result.worksheet_requisition_id')
        ->join('trans_service_requisition', 'mustahik_worksheet_requisition.service_requisition_id', 'trans_service_requisition.service_requisition_id')
        ->join('system_user', 'mustahik_worksheet_result.user_id', 'system_user.user_id')
        ->join('core_service', 'core_service.service_id', 'trans_service_requisition.service_id')
        ->where('mustahik_worksheet_result.worksheet_result_id', $worksheet_result_id)
        ->first();

        $username = User::select('name', 'full_name')
        ->where('user_id','=',Auth::id())
        ->first();

        $this->set_log(Auth::id(), $username['name'],'1089','Application.MustahikWorksheetResult.print',$username['name'],'Print');

        $pdf = new TCPDF('P', PDF_UNIT, 'A4', true, 'UTF-8', false);

        $pdf::SetPrintHeader(false);
        $pdf::SetPrintFooter(false);

        $pdf::SetMargins(20, 6, 20, 6);

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

        $path   = public_path('resources/img/logosmart/logobaznas01.png');
        $score  = 0;

        $export = "
        <br></br>
        <div style=\"text-align:center;\">
            <br/>
            <br/>
            <br/>
            <br/>
            <br/>
            <br/>
        </div>
        <table cellspacing=\"0\" cellpadding=\"2\" border=\"0\">
            <tr>
                <td align=\"center\" style=\"font-weight:bold\">SCORING MUSTAHIK</td>
            </tr>
        </table>
        <br/>
        <br/>
        <br/>
        <table cellspacing=\"5\" cellpadding=\"2\" border=\"0\">
            <tr>
                <td width=\"5%\" style=\"font-weight:bold\">I.</td>
                <td width=\"95%\" style=\"font-weight:bold\">DETAIL PENGAJUAN</td>
            </tr>
            <tr>
                <td width=\"5%\"></td>
                <td width=\"3%\">1.</td>
                <td width=\"25%\">Nomor Pengajuan</td>
                <td width=\"3%\">:</td>
                <td width=\"55%\">".$mustahikworksheetdetail['service_requisition_no']."</td>
            </tr>
            <tr>
                <td width=\"5%\"></td>
                <td width=\"3%\">2.</td>
                <td width=\"25%\">Tanggal Pengajuan</td>
                <td width=\"3%\">:</td>
                <td width=\"55%\">".$mustahikworksheetdetail['created_at']."</td>
            </tr>
            <tr>
                <td width=\"5%\"></td>
                <td width=\"3%\">3.</td>
                <td width=\"25%\">Nama Pemohon</td>
                <td width=\"3%\">:</td>
                <td width=\"55%\">".$mustahikworksheetdetail['service_requisition_name']."</td>
            </tr>
            <tr>
                <td width=\"5%\"></td>
                <td width=\"3%\">4.</td>
                <td width=\"25%\">Nama Layanan</td>
                <td width=\"3%\">:</td>
                <td width=\"55%\">".$mustahikworksheetdetail['service_name']."</td>
            </tr>
            <tr>
                <td width=\"5%\"></td>
                <td width=\"3%\">5.</td>
                <td width=\"25%\">Nama Surveyor</td>
                <td width=\"3%\">:</td>
                <td width=\"55%\">".$mustahikworksheetdetail['full_name']."</td>
            </tr>
            <tr>
                <td width=\"5%\"></td>
                <td width=\"3%\">6.</td>
                <td width=\"25%\">Tanggal Penilaian</td>
                <td width=\"3%\">:</td>
                <td width=\"55%\">".$mustahikworksheetdetail['worksheet_result_date']."</td>
            </tr>
        </table>
        <table cellspacing=\"5\" cellpadding=\"2\" border=\"0\">
            <tr>
                <td width=\"5%\" style=\"font-weight:bold\">II.</td>
                <td width=\"95%\" style=\"font-weight:bold\">SCORING</td>
            </tr>
        </table>
        <table cellspacing=\"0\" cellpadding=\"0\" border=\"0\">
            <tr>
                <td width=\"5%\"></td>
                <td width=\"95%\">
                    <table cellspacing=\"0\" cellpadding=\"1\" border=\"1\">
                        <tr>
                            <td width=\"5%\" align=\"center\" style=\"font-weight:bold\">No</td>
                            <td width=\"30%\" align=\"center\" style=\"font-weight:bold\">Index Penilaian</td>
                            <td width=\"35%\" align=\"center\" style=\"font-weight:bold\">Kriteria</td>
                            <td width=\"30%\" align=\"center\" style=\"font-weight:bold\">Bobot Nilai</td>
                        </tr>
                        <tr>
                            <td width=\"5%\" align=\"center\" rowspan=\"4\">1. </td>
                            <td width=\"30%\" rowspan=\"4\">Jumlah KK Miskin</td>";
                            if($data['mosque_poor_KK'] < 5) {
                                $score += 4;
                                $export .= "
                                <td width=\"35%\" align=\"left\" style=\"background-color:green;color:#FFFF00;\">Dibawah 5</td>
                                <td width=\"30%\" align=\"center\" style=\"background-color:green;color:#FFFF00;\">4</td>";
                            }else{
                                $export .= "
                                <td width=\"35%\" align=\"left\">Dibawah 5</td>
                                <td width=\"30%\" align=\"center\">4</td>";
                            }
                        $export .= "
                        </tr>
                        <tr>";
                        if($data['mosque_poor_KK'] >= 5 && $data['mosque_poor_KK'] <= 10) {
                            $score += 3;
                            $export .="
                            <td width=\"35%\" align=\"left\" style=\"background-color:green;color:#FFFF00;\">5 - 10</td>
                            <td width=\"30%\" align=\"center\" style=\"background-color:green;color:#FFFF00;\">3</td>
                            ";
                        }else{
                            $export .="
                            <td width=\"35%\" align=\"left\">5 - 10</td>
                            <td width=\"30%\" align=\"center\">3</td>
                            ";
                        }

                        $export .= "
                        </tr>
                        <tr>";
                        if($data['mosque_poor_KK'] >= 11 && $data['mosque_poor_KK'] <= 15) {
                            $score +=2;
                            $export .= "
                            <td width=\"35%\" align=\"left\" style=\"background-color:green;color:#FFFF00;\">11 - 15</td>
                            <td width=\"30%\" align=\"center\" style=\"background-color:green;color:#FFFF00;\">2</td>";
                        }else{
                            $export .= "
                            <td width=\"35%\" align=\"left\">11 - 15</td>
                            <td width=\"30%\" align=\"center\">2</td>";
                        }
                        $export .= "
                        </tr>
                        <tr>";
                        if($data['mosque_poor_KK'] > 15) {
                            $score += 1;
                            $export .= "
                            <td width=\"35%\" align=\"left\" style=\"background-color:green;color:#FFFF00;\">Diatas 15</td>
                            <td width=\"30%\" align=\"center\" style=\"background-color:green;color:#FFFF00;\">1</td>";
                        }else{
                            $export .= "
                            <td width=\"35%\" align=\"left\">Diatas 15</td>
                            <td width=\"30%\" align=\"center\">1</td>";
                        }
                        $export .= "
                        </tr>";


                        $export .="
                        <tr>
                            <td width=\"5%\" align=\"center\" rowspan=\"2\">2. </td>
                            <td width=\"30%\" rowspan=\"2\">Sertifikat Wakaf</td>";
                            if($data['mosque_wakaf_certificate']['no_certificate']) {
                                $score += 4;
                                $export .= "
                                <td width=\"35%\" align=\"left\" style=\"background-color:green;color:#FFFF00;\">Tidak Ada</td>
                                <td width=\"30%\" align=\"center\" style=\"background-color:green;color:#FFFF00;\">4</td>";
                            }else{
                                $export .= "
                                <td width=\"35%\" align=\"left\">Tidak Ada</td>
                                <td width=\"30%\" align=\"center\">4</td>";
                            }
                        $export .= "
                        </tr>
                        <tr>";
                        if($data['mosque_wakaf_certificate']['certificate']) {
                            $score += 2;
                            $export .="
                            <td width=\"35%\" align=\"left\" style=\"background-color:green;color:#FFFF00;\">Ada</td>
                            <td width=\"30%\" align=\"center\" style=\"background-color:green;color:#FFFF00;\">2</td>
                            ";
                        }else{
                            $export .="
                            <td width=\"35%\" align=\"left\">Ada</td>
                            <td width=\"30%\" align=\"center\">2</td>
                            ";
                        }
                        $export .= "
                        </tr>";


                        $export .="
                        <tr>
                            <td width=\"5%\" align=\"center\" rowspan=\"2\">3. </td>
                            <td width=\"30%\" rowspan=\"2\">Ijin Masjid</td>";
                            if($data['mosque_permission']['no_permission']) {
                                $score += 4;
                                $export .= "
                                <td width=\"35%\" align=\"left\" style=\"background-color:green;color:#FFFF00;\">Tidak Ada</td>
                                <td width=\"30%\" align=\"center\" style=\"background-color:green;color:#FFFF00;\">4</td>";
                            }else{
                                $export .= "
                                <td width=\"35%\" align=\"left\">Tidak Ada</td>
                                <td width=\"30%\" align=\"center\">4</td>";
                            }
                        $export .= "
                        </tr>
                        <tr>";
                        if($data['mosque_permission']['permission']) {
                            $score += 2;
                            $export .="
                            <td width=\"35%\" align=\"left\" style=\"background-color:green;color:#FFFF00;\">Ada</td>
                            <td width=\"30%\" align=\"center\" style=\"background-color:green;color:#FFFF00;\">2</td>
                            ";
                        }else{
                            $export .="
                            <td width=\"35%\" align=\"left\">Ada</td>
                            <td width=\"30%\" align=\"center\">2</td>
                            ";
                        }
                        $export .= "
                        </tr>";


                        $export .="
                        <tr>
                            <td width=\"5%\" align=\"center\" rowspan=\"4\">4. </td>
                            <td width=\"30%\" rowspan=\"4\">Jumlah Jamaah</td>";
                            if($data['mosque_jamaah'] < 50) {
                                $score += 4;
                                $export .= "
                                <td width=\"35%\" align=\"left\" style=\"background-color:green;color:#FFFF00;\">Dibawah 50</td>
                                <td width=\"30%\" align=\"center\" style=\"background-color:green;color:#FFFF00;\">4</td>";
                            }else{
                                $export .= "
                                <td width=\"35%\" align=\"left\">Dibawah 50</td>
                                <td width=\"30%\" align=\"center\">4</td>";
                            }
                        $export .= "
                        </tr>
                        <tr>";
                        if($data['mosque_jamaah'] >= 50 && $data['mosque_jamaah'] <= 100) {
                            $score += 3;
                            $export .="
                            <td width=\"35%\" align=\"left\" style=\"background-color:green;color:#FFFF00;\">50 - 100</td>
                            <td width=\"30%\" align=\"center\" style=\"background-color:green;color:#FFFF00;\">3</td>
                            ";
                        }else{
                            $export .="
                            <td width=\"35%\" align=\"left\">50 - 100</td>
                            <td width=\"30%\" align=\"center\">3</td>
                            ";
                        }
                        $export .= "
                        </tr>
                        <tr>";
                        if($data['mosque_jamaah'] >= 101 && $data['mosque_jamaah'] <= 150) {
                            $score += 2;
                            $export .="
                            <td width=\"35%\" align=\"left\" style=\"background-color:green;color:#FFFF00;\">101 - 150</td>
                            <td width=\"30%\" align=\"center\" style=\"background-color:green;color:#FFFF00;\">2</td>
                            ";
                        }else{
                            $export .="
                            <td width=\"35%\" align=\"left\">101 - 150</td>
                            <td width=\"30%\" align=\"center\">2</td>
                            ";
                        }
                        $export .= "
                        </tr>
                        <tr>";
                        if($data['mosque_jamaah'] > 150) {
                            $score += 1;
                            $export .="
                            <td width=\"35%\" align=\"left\" style=\"background-color:green;color:#FFFF00;\">Diatas 150</td>
                            <td width=\"30%\" align=\"center\" style=\"background-color:green;color:#FFFF00;\">1</td>
                            ";
                        }else{
                            $export .="
                            <td width=\"35%\" align=\"left\">Diatas 150</td>
                            <td width=\"30%\" align=\"center\">1</td>
                            ";
                        }
                        $export .= "
                        </tr>";


                        $export .="
                        <tr>
                            <td width=\"5%\" align=\"center\" rowspan=\"4\">5. </td>
                            <td width=\"30%\" rowspan=\"4\">Luas Bangunan</td>";
                            if($data['mosque_area'] < 50) {
                                $score += 4;
                                $export .= "
                                <td width=\"35%\" align=\"left\" style=\"background-color:green;color:#FFFF00;\">Dibawah 50 m2</td>
                                <td width=\"30%\" align=\"center\" style=\"background-color:green;color:#FFFF00;\">4</td>";
                            }else{
                                $export .= "
                                <td width=\"35%\" align=\"left\">Dibawah 50</td>
                                <td width=\"30%\" align=\"center\">4</td>";
                            }
                        $export .= "
                        </tr>
                        <tr>";
                        if($data['mosque_area'] >= 50 && $data['mosque_area'] <= 100) {
                            $score += 3;
                            $export .="
                            <td width=\"35%\" align=\"left\" style=\"background-color:green;color:#FFFF00;\">50 - 100 m2</td>
                            <td width=\"30%\" align=\"center\" style=\"background-color:green;color:#FFFF00;\">3</td>
                            ";
                        }else{
                            $export .="
                            <td width=\"35%\" align=\"left\">50 - 100 m2</td>
                            <td width=\"30%\" align=\"center\">3</td>
                            ";
                        }
                        $export .= "
                        </tr>
                        <tr>";
                        if($data['mosque_area'] >= 101 && $data['mosque_area'] <= 150) {
                            $score += 2;
                            $export .="
                            <td width=\"35%\" align=\"left\" style=\"background-color:green;color:#FFFF00;\">101 - 150 m2</td>
                            <td width=\"30%\" align=\"center\" style=\"background-color:green;color:#FFFF00;\">2</td>
                            ";
                        }else{
                            $export .="
                            <td width=\"35%\" align=\"left\">101 - 150 m2</td>
                            <td width=\"30%\" align=\"center\">2</td>
                            ";
                        }
                        $export .= "
                        </tr>
                        <tr>";
                        if($data['mosque_area'] > 150) {
                            $score += 1;
                            $export .="
                            <td width=\"35%\" align=\"left\" style=\"background-color:green;color:#FFFF00;\">Diatas 150 m2</td>
                            <td width=\"30%\" align=\"center\" style=\"background-color:green;color:#FFFF00;\">1</td>
                            ";
                        }else{
                            $export .="
                            <td width=\"35%\" align=\"left\">Diatas 150 m2</td>
                            <td width=\"30%\" align=\"center\">1</td>
                            ";
                        }
                        $export .= "
                        </tr>";


                        $export .="
                        <tr>
                            <td width=\"5%\" align=\"center\" rowspan=\"2\">6. </td>
                            <td width=\"30%\" rowspan=\"2\">Sumber Air</td>";
                            if($data['water_source']['water_source_machine']) {
                                $score += 4;
                                $export .= "
                                <td width=\"35%\" align=\"left\" style=\"background-color:green;color:#FFFF00;\">Mesin</td>
                                <td width=\"30%\" align=\"center\" style=\"background-color:green;color:#FFFF00;\">4</td>";
                            }else{
                                $export .= "
                                <td width=\"35%\" align=\"left\">Mesin</td>
                                <td width=\"30%\" align=\"center\">4</td>";
                            }
                        $export .= "
                        </tr>
                        <tr>";
                        if($data['water_source']['water_source_manual']) {
                            $score += 2;
                            $export .="
                            <td width=\"35%\" align=\"left\" style=\"background-color:green;color:#FFFF00;\">Alam / Manual</td>
                            <td width=\"30%\" align=\"center\" style=\"background-color:green;color:#FFFF00;\">2</td>
                            ";
                        }else{
                            $export .="
                            <td width=\"35%\" align=\"left\">Alam / Manual</td>
                            <td width=\"30%\" align=\"center\">2</td>
                            ";
                        }
                        $export .= "
                        </tr>";


                        $export .="
                        <tr>
                            <td width=\"5%\" align=\"center\" rowspan=\"3\">7. </td>
                            <td width=\"30%\" rowspan=\"3\">Lantai</td>";
                            if($data['floor']['floor_1']) {
                                $score += 1;
                                $export .= "
                                <td width=\"35%\" align=\"left\" style=\"background-color:green;color:#FFFF00;\">Ubin / Tegel</td>
                                <td width=\"30%\" align=\"center\" style=\"background-color:green;color:#FFFF00;\">1</td>";
                            }else{
                                $export .= "
                                <td width=\"35%\" align=\"left\">Ubin / Tegel</td>
                                <td width=\"30%\" align=\"center\">1</td>";
                            }
                        $export .= "
                        </tr>
                        <tr>";
                        if($data['floor']['floor_2']) {
                            $score += 3;
                            $export .="
                            <td width=\"35%\" align=\"left\" style=\"background-color:green;color:#FFFF00;\">Keramik</td>
                            <td width=\"30%\" align=\"center\" style=\"background-color:green;color:#FFFF00;\">3</td>
                            ";
                        }else{
                            $export .="
                            <td width=\"35%\" align=\"left\">Keramik</td>
                            <td width=\"30%\" align=\"center\">3</td>
                            ";
                        }
                        $export .= "
                        </tr>
                        <tr>";
                        if($data['floor']['floor_3']) {
                            $score += 5;
                            $export .="
                            <td width=\"35%\" align=\"left\" style=\"background-color:green;color:#FFFF00;\">Marmer / Granit</td>
                            <td width=\"30%\" align=\"center\" style=\"background-color:green;color:#FFFF00;\">5</td>
                            ";
                        }else{
                            $export .="
                            <td width=\"35%\" align=\"left\">Marmer / Granit</td>
                            <td width=\"30%\" align=\"center\">5</td>
                            ";
                        }
                        $export .= "
                        </tr>";


                        $export .="
                        <tr>
                            <td width=\"5%\" align=\"center\" rowspan=\"2\">8. </td>
                            <td width=\"30%\" rowspan=\"2\">Dinding</td>";
                            if($data['wall']['wall_1']) {
                                $score += 4;
                                $export .= "
                                <td width=\"35%\" align=\"left\" style=\"background-color:green;color:#FFFF00;\">Bagus</td>
                                <td width=\"30%\" align=\"center\" style=\"background-color:green;color:#FFFF00;\">4</td>";
                            }else{
                                $export .= "
                                <td width=\"35%\" align=\"left\">Bagus</td>
                                <td width=\"30%\" align=\"center\">4</td>";
                            }
                        $export .= "
                        </tr>
                        <tr>";
                        if($data['wall']['wall_2']) {
                            $score += 2;
                            $export .="
                            <td width=\"35%\" align=\"left\" style=\"background-color:green;color:#FFFF00;\">Tidak Bagus</td>
                            <td width=\"30%\" align=\"center\" style=\"background-color:green;color:#FFFF00;\">2</td>
                            ";
                        }else{
                            $export .="
                            <td width=\"35%\" align=\"left\">Tidak Bagus</td>
                            <td width=\"30%\" align=\"center\">2</td>
                            ";
                        }
                        $export .= "
                        </tr>";


                        $export .="
                        <tr>
                            <td width=\"5%\" align=\"center\" rowspan=\"3\">9. </td>
                            <td width=\"30%\" rowspan=\"3\">Atap</td>";
                            if($data['roof']['roof_1']) {
                                $score += 4;
                                $export .= "
                                <td width=\"35%\" align=\"left\" style=\"background-color:green;color:#FFFF00;\">Genteng, Pvc, Spandek</td>
                                <td width=\"30%\" align=\"center\" style=\"background-color:green;color:#FFFF00;\">4</td>";
                            }else{
                                $export .= "
                                <td width=\"35%\" align=\"left\">Genteng, Pvc, Spandek</td>
                                <td width=\"30%\" align=\"center\">4</td>";
                            }
                        $export .= "
                        </tr>
                        <tr>";
                        if($data['roof']['roof_2']) {
                            $score += 2;
                            $export .="
                            <td width=\"35%\" align=\"left\" style=\"background-color:green;color:#FFFF00;\">Seng</td>
                            <td width=\"30%\" align=\"center\" style=\"background-color:green;color:#FFFF00;\">2</td>
                            ";
                        }else{
                            $export .="
                            <td width=\"35%\" align=\"left\">Seng</td>
                            <td width=\"30%\" align=\"center\">2</td>
                            ";
                        }
                        $export .= "
                        </tr>
                        <tr>";
                        if($data['roof']['roof_3']) {
                            $score += 1;
                            $export .="
                            <td width=\"35%\" align=\"left\" style=\"background-color:green;color:#FFFF00;\">Lebih jelek dr diatas</td>
                            <td width=\"30%\" align=\"center\" style=\"background-color:green;color:#FFFF00;\">1</td>
                            ";
                        }else{
                            $export .="
                            <td width=\"35%\" align=\"left\">Lebih jelek dr diatas</td>
                            <td width=\"30%\" align=\"center\">1</td>
                            ";
                        }
                        $export .= "
                        </tr>";


                        $export .="
                        <tr>
                            <td width=\"5%\" align=\"center\" rowspan=\"2\">10. </td>
                            <td width=\"30%\" rowspan=\"2\">Pagar</td>";
                            if($data['fence']['fence']) {
                                $score += 4;
                                $export .= "
                                <td width=\"35%\" align=\"left\" style=\"background-color:green;color:#FFFF00;\">Ada</td>
                                <td width=\"30%\" align=\"center\" style=\"background-color:green;color:#FFFF00;\">4</td>";
                            }else{
                                $export .= "
                                <td width=\"35%\" align=\"left\">Ada</td>
                                <td width=\"30%\" align=\"center\">4</td>";
                            }
                        $export .= "
                        </tr>
                        <tr>";
                        if($data['fence']['no_fence']) {
                            $score += 2;
                            $export .="
                            <td width=\"35%\" align=\"left\" style=\"background-color:green;color:#FFFF00;\">Tidak Ada</td>
                            <td width=\"30%\" align=\"center\" style=\"background-color:green;color:#FFFF00;\">2</td>
                            ";
                        }else{
                            $export .="
                            <td width=\"35%\" align=\"left\">Tidak Ada</td>
                            <td width=\"30%\" align=\"center\">2</td>
                            ";
                        }
                        $export .= "
                        </tr>";


                        $export .="
                        <tr>
                            <td width=\"5%\" align=\"center\" rowspan=\"7\">11. </td>
                            <td width=\"30%\" rowspan=\"7\">Sarpras</td>";
                            if($data['sarpras']['mat']) {
                                $score += 1;
                                $export .= "
                                <td width=\"35%\" align=\"left\" style=\"background-color:green;color:#FFFF00;\">Tikar/Karpet</td>
                                <td width=\"30%\" align=\"center\" style=\"background-color:green;color:#FFFF00;\">1</td>";
                            }else{
                                $export .= "
                                <td width=\"35%\" align=\"left\">Tikar/Karpet</td>
                                <td width=\"30%\" align=\"center\">1</td>";
                            }
                        $export .= "
                        </tr>
                        <tr>";
                        if($data['sarpras']['ac']) {
                            $score += 2;
                            $export .="
                            <td width=\"35%\" align=\"left\" style=\"background-color:green;color:#FFFF00;\">AC</td>
                            <td width=\"30%\" align=\"center\" style=\"background-color:green;color:#FFFF00;\">2</td>
                            ";
                        }else{
                            $export .="
                            <td width=\"35%\" align=\"left\">AC</td>
                            <td width=\"30%\" align=\"center\">2</td>
                            ";
                        }
                        $export .= "
                        </tr>
                        <tr>";
                        if($data['sarpras']['fan']) {
                            $score += 1;
                            $export .="
                            <td width=\"35%\" align=\"left\" style=\"background-color:green;color:#FFFF00;\">Kipas</td>
                            <td width=\"30%\" align=\"center\" style=\"background-color:green;color:#FFFF00;\">1</td>
                            ";
                        }else{
                            $export .="
                            <td width=\"35%\" align=\"left\">Kipas</td>
                            <td width=\"30%\" align=\"center\">1</td>
                            ";
                        }
                        $export .= "
                        </tr>
                        <tr>";
                        if($data['sarpras']['sound']) {
                            $score += 1;
                            $export .="
                            <td width=\"35%\" align=\"left\" style=\"background-color:green;color:#FFFF00;\">Sound</td>
                            <td width=\"30%\" align=\"center\" style=\"background-color:green;color:#FFFF00;\">1</td>
                            ";
                        }else{
                            $export .="
                            <td width=\"35%\" align=\"left\">Sound</td>
                            <td width=\"30%\" align=\"center\">1</td>
                            ";
                        }
                        $export .= "
                        </tr>
                        <tr>";
                        if($data['sarpras']['wudhu']) {
                            $score += 1;
                            $export .="
                            <td width=\"35%\" align=\"left\" style=\"background-color:green;color:#FFFF00;\">Tempat Wudhu</td>
                            <td width=\"30%\" align=\"center\" style=\"background-color:green;color:#FFFF00;\">1</td>
                            ";
                        }else{
                            $export .="
                            <td width=\"35%\" align=\"left\">Tempat Wudhu</td>
                            <td width=\"30%\" align=\"center\">1</td>
                            ";
                        }
                        $export .= "
                        </tr>
                        <tr>";
                        if($data['sarpras']['parking']) {
                            $score += 1;
                            $export .="
                            <td width=\"35%\" align=\"left\" style=\"background-color:green;color:#FFFF00;\">Tempat Parkir</td>
                            <td width=\"30%\" align=\"center\" style=\"background-color:green;color:#FFFF00;\">1</td>
                            ";
                        }else{
                            $export .="
                            <td width=\"35%\" align=\"left\">Tempat Parkir</td>
                            <td width=\"30%\" align=\"center\">1</td>
                            ";
                        }
                        $export .= "
                        </tr>
                        <tr>";
                        if($data['sarpras']['canopy']) {
                            $score += 1;
                            $export .="
                            <td width=\"35%\" align=\"left\" style=\"background-color:green;color:#FFFF00;\">Kanopi</td>
                            <td width=\"30%\" align=\"center\" style=\"background-color:green;color:#FFFF00;\">1</td>
                            ";
                        }else{
                            $export .="
                            <td width=\"35%\" align=\"left\">Kanopi</td>
                            <td width=\"30%\" align=\"center\">1</td>
                            ";
                        }
                        $export .= "
                        </tr>";


                        $export .= "<tr>
                            <td width=\"70%\" align=\"center\" style=\"font-weight: bold;\">Total</td>
                            <td width=\"30%\" align=\"center\" style=\"font-weight: bold;\">".$score."</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        <br/>
        <br/>
        <br/>
        <table cellspacing=\"0\" cellpadding=\"2\" border=\"0\">
            <tr>
                <td width=\"5%\"></td>
                <td width=\"50%\">
                    <table cellspacing=\"0\" cellpadding=\"2\" border=\"1\">
                        <tr>
                            <td width=\"60%\" align=\"center\" style=\"font-weight: bold;\">Nilai Scoring</td>
                            <td width=\"40%\" align=\"center\" style=\"font-weight: bold;\">Kategori</td>
                        </tr>
                        <tr>
                            <td width=\"60%\" align=\"center\">< 25</td>
                            <td width=\"40%\" align=\"center\">Approve</td>
                        </tr>
                        <tr>
                            <td width=\"60%\" align=\"center\">25 - 35</td>
                            <td width=\"40%\" align=\"center\">Komite</td>
                        </tr>
                        <tr>
                            <td width=\"60%\" align=\"center\">> 35</td>
                            <td width=\"40%\" align=\"center\">Reject</td>
                        </tr>
                    </table>
                </td>";

                $score_result = "";
                if($score < 25){
                    $score_result = "Aprrove";
                }else if($score >= 25 && $score <= 35){
                    $score_result = "Komite";
                }else if($score > 35){
                    $score_result = "Reject";
                } 

                $export .= "<td width=\"45%\">
                    <table cellspacing=\"0\" cellpadding=\"2\" border=\"1\">
                        <tr>
                            <td width=\"100%\" align=\"center\" style=\"font-weight: bold;\">Hasil Scoring</td>
                        </tr>
                        <tr>
                            <td width=\"100%\" align=\"center\">".$score_result."</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
        ";
        
        $pdf::Image( $path, 4, 4, 25, 25, 'PNG', '', 'LT', false, 300, 'C', false, false, 1, false, false, false);
        $pdf::writeHTML($export, true, false, false, false, '');

        // ob_clean();

        $filename = 'Scoring_'.$worksheet_result_id.'.pdf';
        $pdf::Output($filename, 'I');
    }

}
