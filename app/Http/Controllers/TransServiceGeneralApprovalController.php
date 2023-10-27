<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\CoreService;
use App\Models\CoreSection;
use App\Models\CoreServiceGeneralParameter;
use App\Models\CoreServiceTerm;
use App\Models\CoreServiceParameter;
use App\Models\TransServiceDocumentRequisition;
use App\Models\TransServiceLog;
use App\Models\TransServiceGeneral;
use App\Models\TransServiceGeneralParameter;
use App\Models\TransServiceRequisition;
use App\Models\TransServiceRequisitionTerm;
use App\Models\TransServiceRequisitionParameter;
use App\Models\User;
use App\Models\SystemLogUser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Elibyy\TCPDF\Facades\TCPDF;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Illuminate\Support\Str;

class TransServiceGeneralApprovalController extends Controller
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

        $transservicegeneral = TransServiceGeneral::where('data_state', 0)
        ->where('created_at','>=',$start_date)
        ->where('created_at','<=',$stop_date)
        ->where('service_general_status', '!=', 0)
        ->get();

        return view('content/TransServiceGeneralApproval/ListTransServiceGeneralApproval',compact('transservicegeneral', 'start_date', 'end_date'));
    }
    
    public function filter(Request $request){
        $start_date     = $request->start_date;
        $end_date       = $request->end_date;

        Session::put('start_date', $start_date);
        Session::put('end_date', $end_date);

        return redirect('/trans-service-general-approval');
    }

    public function search()
    {
        $transservicegeneral = TransServiceGeneral::where('data_state', 0)
        ->where('service_general_status', 0)
        ->get();

        return view('content/TransServiceGeneralApproval/SearchTransServiceGeneral',compact('transservicegeneral'));
    }

    public function addReset()
    {
        Session::forget('sess_servicegeneraltoken');
        Session::forget('data_coreservice');
        Session::forget('data_coreserviceterm');
        Session::forget('data_coreserviceparameter');

        return redirect('/trans-service-requisition/add');
    }

    public function addTransServiceGeneralApproval($service_general_id)
    {
        $service_general_token_approval		    = Session::get('sess_servicegeneraltokenapproval');

        if (empty($service_general_token_approval)){
            $service_general_token_approval = md5(date("YmdHis"));
            Session::put('sess_servicegeneraltokenapproval', $service_general_token_approval);
        }

        $servicegeneral = TransServiceGeneral::findOrFail($service_general_id);

        $servicegeneralparameter = TransServiceGeneralParameter::select('trans_service_general_parameter.*', 'core_service_general_parameter.*')
        ->join('core_service_general_parameter', 'core_service_general_parameter.service_general_parameter_id', 'trans_service_general_parameter.general_parameter_id')
        ->where('trans_service_general_parameter.service_general_id', $service_general_id)
        ->where('trans_service_general_parameter.data_state', 0)
        ->get();

        return view('content/TransServiceGeneralApproval/FormAddTransServiceGeneralApproval',compact('servicegeneral', 'servicegeneralparameter', 'service_general_id', 'service_general_token_approval'));
    }

    public function detailTransServiceGeneralApproval($service_general_id){
        if (empty($service_general_token_edit)){
            $service_general_token_edit = md5(date("YmdHis"));
            Session::put('sess_servicegeneraltokenedit', $service_general_token_edit);
        }

        $servicegeneral = TransServiceGeneral::findOrFail($service_general_id);

        $servicegeneralparameter = TransServiceGeneralParameter::select('trans_service_general_parameter.*', 'core_service_general_parameter.*')
        ->join('core_service_general_parameter', 'core_service_general_parameter.service_general_parameter_id', 'trans_service_general_parameter.general_parameter_id')
        ->where('trans_service_general_parameter.service_general_id', $service_general_id)
        ->where('trans_service_general_parameter.data_state', 0)
        ->get();

        return view('content/TransServiceGeneralApproval/FormDetailTransServiceGeneralApproval',compact('servicegeneral', 'servicegeneralparameter', 'service_general_id'));
    }

    public function revisionTransServiceGeneralApproval($service_general_id){
        if (empty($service_general_token_edit)){
            $service_general_token_edit = md5(date("YmdHis"));
            Session::put('sess_servicegeneraltokenedit', $service_general_token_edit);
        }

        $servicegeneral = TransServiceGeneral::findOrFail($service_general_id);

        $servicegeneralparameter = TransServiceGeneralParameter::select('trans_service_general_parameter.*', 'core_service_general_parameter.*')
        ->join('core_service_general_parameter', 'core_service_general_parameter.service_general_parameter_id', 'trans_service_general_parameter.general_parameter_id')
        ->where('trans_service_general_parameter.service_general_id', $service_general_id)
        ->where('trans_service_general_parameter.data_state', 0)
        ->get();

        return view('content/TransServiceGeneralApproval/FormRevisionTransServiceGeneralApproval',compact('servicegeneral', 'servicegeneralparameter', 'service_general_id'));
    }

    public function processRevisionTransServiceGeneralApproval(Request $request){   
        $service_general_id = $request->service_general_id;

        $servicegeneral = TransServiceGeneral::findOrFail($service_general_id);
        $servicegeneral->service_general_status = 0;
        $servicegeneral->revision_id = Auth::id();
        $servicegeneral->revision_at = date('Y-m-d H:i:s');
        if($servicegeneral->save()){

            $wa_msg = "Siabas\r\n\r\n\r\nNama Instansi: ".$servicegeneral['service_general_agency']."\r\n\r\nNomor Pengajuan : ".$servicegeneral['service_general_no']."\r\n\r\nBagian : Surat Umum\r\n\r\nPesan : ".$this->getMessage(7);
            $wa_status = $this->getMessageStatus(7);
            $wa_no  = $servicegeneral['service_general_phone'];
            $this->postWhatsappMessages($wa_msg, $wa_status, $wa_no);

            $msg = "Revisi Surat Umum Berhasil";
            return redirect('/trans-service-general-approval')->with('msg',$msg);
        }else{
            $msg = "Revisi Surat Umum Tidak Berhasil";
            return redirect('/trans-service-general-approval/revision/'.$service_general_id)->with('msg',$msg);
        }
    }

    public function processApproveTransServiceGeneralApproval(Request $request){   
        $fields = $request->validate([
            'service_general_id'        => 'required',
            'service_general_remark'    => 'required',
        ]);
        
        $allrequest = $request->all();
                    
        $fileNameToStore = '';

        if($request->hasFile('file_sk')){
            // Get filename with the extension
            $filenameWithExt = $request->file('file_sk')->getClientOriginalName();
            //Get just filename
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            // Get just ext
            $extension = $request->file('file_sk')->getClientOriginalExtension();
            // Filename to store
            $fileNameToStore = $filename.'_'.time().'.'.$extension;
            // Upload Image
            $path = $request->file('file_sk')->storeAs('public/service-general',$fileNameToStore);

        }else{
            $msg = "Upload Surat SK Masih Kosong";
            return redirect('/trans-service-general-approval/add/'.$fields['service_general_id'])->with('msg',$msg);
        }

        $servicegeneral = TransServiceGeneral::findOrFail($fields['service_general_id']);
        $servicegeneral->service_general_sk_file = $fileNameToStore;
        $servicegeneral->service_general_status = 1;
        $servicegeneral->service_general_remark = $fields['service_general_remark'];
        $servicegeneral->approved_id = Auth::id();
        $servicegeneral->approved_at = date('Y-m-d H:i:s');
        if($servicegeneral->save()){

            $wa_msg = "Siabas\r\n\r\n\r\nNama Instansi: ".$servicegeneral['service_general_agency']."\r\n\r\nNomor Pengajuan : ".$servicegeneral['service_general_no']."\r\n\r\nBagian : Surat Umum\r\n\r\nPesan : ".$this->getMessage(4);
            $wa_status = $this->getMessageStatus(4);
            $wa_no  = $servicegeneral['service_general_phone'];
            $this->postWhatsappMessages($wa_msg, $wa_status, $wa_no);

            $msg = "Persetujuan Surat Umum Berhasil";
            return redirect('/trans-service-general-approval')->with('msg',$msg);
        }else{
            $msg = "Persetujuan Surat Umum Tidak Berhasil";
            return redirect('/trans-service-general-approval/add/'.$fields['service_general_id'])->with('msg',$msg);
        }
    }

    public function processDisapproveTransServiceGeneralApproval (Request $request){   
        $fields = $request->validate([
            'service_general_id'        => 'required',
            'service_general_remark'    => 'required',
        ]);

        $fileNameToStore = '';

        if($request->hasFile('file_disapprove')){
            // Get filename with the extension
            $filenameWithExt = $request->file('file_disapprove')->getClientOriginalName();
            //Get just filename
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            // Get just ext
            $extension = $request->file('file_disapprove')->getClientOriginalExtension();
            // Filename to store
            $fileNameToStore = $filename.'_'.time().'.'.$extension;
            // Upload Image
            $path = $request->file('file_disapprove')->storeAs('public/service-general',$fileNameToStore);

        }

        $servicegeneral = TransServiceGeneral::findOrFail($fields['service_general_id']);
        $servicegeneral->service_general_remark     = $fields['service_general_remark'];
        $servicegeneral->service_general_sk_file    = $fileNameToStore;
        $servicegeneral->service_general_status     = 2;
        $servicegeneral->disapproved_id             = Auth::id();
        $servicegeneral->disapproved_at             = date('Y-m-d H:i:s');

        if($servicegeneral->save()){

            $wa_msg = "Siabas\r\n\r\n\r\nNama Instansi: ".$servicegeneral['service_general_agency']."\r\n\r\nNomor Pengajuan : ".$servicegeneral['service_general_no']."\r\n\r\nBagian : Surat Umum\r\n\r\nPesan : ".$this->getMessage(8);
            $wa_status = $this->getMessageStatus(8);
            $wa_no  = $servicegeneral['service_general_phone'];
            $this->postWhatsappMessages($wa_msg, $wa_status, $wa_no);
            
            $msg = "Penolakan Surat Umum Berhasil";
            return redirect('/trans-service-general-approval')->with('msg',$msg);
        }else{
            $msg = "Penolakan Surat Umum Tidak Berhasil";
            return redirect('/trans-service-general-approval/add/'.$fields['service_general_id'])->with('msg',$msg);
        }
    }

    public function editReset($service_id)
    {
        Session::forget('data_coreserviceterm');
        Session::forget('data_coreserviceterm_first');
        Session::forget('data_coreserviceparameter_first');

        return redirect('/service/edit/'.$service_id);
    }

    public function processEditArrayCoreServiceTerm(Request $request)
    {
        $service_id                     = $request->service_id;

        $data_coreserviceterm = array(
            'record_id'				    => date('YmdHis'),
            'service_term_no'		    => $request->service_term_no,
            'service_term_description'	=> $request->service_term_description,
            'item_status'	            => 1,
        );

        $lastdatacoreserviceterm = Session::get('data_coreserviceterm');
        if($lastdatacoreserviceterm !== null){
            array_push($lastdatacoreserviceterm, $data_coreserviceterm);
            Session::put('data_coreserviceterm', $lastdatacoreserviceterm);
        }else{
            $lastdatacoreserviceterm = [];
            array_push($lastdatacoreserviceterm, $data_coreserviceterm);
            Session::push('data_coreserviceterm', $data_coreserviceterm);
        }
        
        return redirect('/service/edit/'.$service_id);
    }

    public function deleteEditArrayCoreServiceTerm($record_id, $service_id)
    {
        $arrayBaru			= array();
        $dataArrayHeader	= Session::get('data_coreserviceterm');

        foreach($dataArrayHeader as $key => $val){
            if($key == $record_id){
                $arrayBaru[$key] 				= $val;
                $arrayBaru[$key]['item_status'] = 2;
            } else {
                $arrayBaru[$key] 				= $val;
            }
        }

        Session::forget('data_coreserviceterm');
        Session::put('data_coreserviceterm', $arrayBaru);

        return redirect('/service/edit/'.$service_id);
    }

    public function processEditArrayCoreServiceParameter(Request $request)
    {
        $service_id                     = $request->service_id;

        $data_coreserviceparameter = array(
            'record_id'				        => date('YmdHis'),
            'service_parameter_no'		    => $request->service_parameter_no,
            'service_parameter_description'	=> $request->service_parameter_description,
            'item_status'	                => 1,
        );

        $lastdatacoreserviceparameter = Session::get('data_coreserviceparameter');
        if($lastdatacoreserviceparameter !== null){
            array_push($lastdatacoreserviceparameter, $data_coreserviceparameter);
            Session::put('data_coreserviceparameter', $lastdatacoreserviceparameter);
        }else{
            $lastdatacoreserviceparameter = [];
            array_push($lastdatacoreserviceparameter, $data_coreserviceparameter);
            Session::push('data_coreserviceparameter', $data_coreserviceparameter);
        }
        
        return redirect('/service/edit/'.$service_id);
    }

    public function deleteEditArrayCoreServiceParameter($record_id, $service_id)
    {
        $arrayBaru			= array();
        $dataArrayHeader	= Session::get('data_coreserviceparameter');

        foreach($dataArrayHeader as $key => $val){
            if($key == $record_id){
                $arrayBaru[$key] 				= $val;
                $arrayBaru[$key]['item_status'] = 2;
            } else {
                $arrayBaru[$key] 				= $val;
            }
        }

        Session::forget('data_coreserviceparameter');
        Session::put('data_coreserviceparameter', $arrayBaru);

        return redirect('/service/edit/'.$service_id);
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

    public function downloadTransServiceGeneralFile ($service_general_id){
        $servicegeneral = TransServiceGeneral::findOrFail($service_general_id);
        
        return response()->download(
            storage_path('app/public/service-general/'.$servicegeneral['service_general_file']),
            $servicegeneral['service_general_file'],
        );
    }

    public function downloadTransServiceGeneralApprovalSKFile ($service_general_id){
        $servicegeneral = TransServiceGeneral::findOrFail($service_general_id);
        
        return response()->download(
            storage_path('app/public/service-general/'.$servicegeneral['service_general_sk_file']),
            $servicegeneral['service_general_sk_file'],
        );
        print_r($servicegeneral['service_general_sk_file']);exit;
    }
    
    public function print($service_general_id){
        $transservicegeneral = TransServiceGeneral::findOrFail($service_general_id);

        $coreservicegeneralparameter = CoreServiceGeneralParameter::where('data_state', 0)
        ->first();

        $username = User::select('name')->where('user_id','=',Auth::id())->first();

        $this->set_log(Auth::id(), $username['name'],'1089','Application.TransServiecGeneral.print',$username['name'],'Export');


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

        $datetime = strtotime($transservicegeneral['created_at']);
        $date = date('Y-m-d', $datetime);
        $style = array(
            'align-item' => 'right',
        );
        // echo $barcode->getBarcodePNG('1', 'EAN13');exit;

        

        $export = "
        <div style=\"text-align:center;\">
            <a style=\"color: green; text-decoration: none; font-size: 25px\">BAZNAS</a>
            <br/>
            <a style=\"color: black; text-decoration: none;\">Badan Amil Zakat Nasional</a>
            <br/>
            <a style=\"color: green; text-decoration: none; font-size: 15px\">KOTA SURAKARTA</a>
        </div>
        <hr style=\"width:100%; text-align:left !important; \"></hr>
        </br>
        <div style=\"text-align:left;\">
            <a style=\"color: black; text-decoration: none; font-size: 15px\">Nama Instansi &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: ".$transservicegeneral['service_general_agency']."</a>
            <br/>
            <a style=\"color: black; text-decoration: none; font-size: 15px\"> Tanggal Pengajuan &nbsp;&nbsp;: ".$date."</a>
            <br/>
            <a style=\"color: black; text-decoration: none; font-size: 15px\"> Jenis Pengajuan &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: SURAT UMUM</a>
        </div>
        </br>
        <div style=\"text-align:right;\">
            <br/>
            <a style=\"color: black; text-decoration: none; font-size: 15px\">".$transservicegeneral['service_general_no']."</a>
        </div>
        ";

        $pdf::writeHTML($export, true, false, false, false, '');
        $pdf::write2DBarcode($transservicegeneral['service_general_no'], 'QRCODE,H', 168, 62, 40, 40, $style, 'N');

        if (ob_get_contents()) ob_end_clean();
        // -----------------------------------------------------------------------------
        
        //Close and output PDF document
        $filename = 'Bukti Pengajuan Surat Umum_'.$transservicegeneral['service_general_id'].'.pdf';
        $pdf::Output($filename, 'I');

        //============================================================+
        // END OF FILE
        //============================================================+
    }
}
