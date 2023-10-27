<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\CoreSection;
use App\Models\CoreService;
use App\Models\TransServiceRequisition;
use App\Models\TransServiceDisposition;
use App\Models\TransServiceDispositionTerm;
use App\Models\TransServiceDispositionParameter;
use App\Models\TransServiceLog;
use App\Models\User;
use App\Models\SystemLogUser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Elibyy\TCPDF\Facades\TCPDF;

class TransServiceDispositionFundsController extends Controller
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

        $transservicedisposition = TransServiceDisposition::where('data_state', 0)
        ->where('created_at','>=',$start_date)
        ->where('created_at','<=',$stop_date)
        ->where('approved_status', 1)
        ->where('review_status', 1)
        ->where('service_disposition_funds_status', '!=', 0)
        ->get();

        return view('content/TransServiceDispositionFunds/ListTransServiceDispositionFunds',compact('transservicedisposition', 'start_date', 'end_date'));
    }
    
    public function filter(Request $request){
        $start_date     = $request->start_date;
        $end_date       = $request->end_date;

        Session::put('start_date', $start_date);
        Session::put('end_date', $end_date);

        return redirect('/trans-service-disposition-funds');
    }

    public function search()
    {
        $transservicedisposition = TransServiceDisposition::select('trans_service_disposition.*', 'core_service.service_name')
        ->where('trans_service_disposition.data_state', 0)
        ->where('approved_status', 1)
        ->where('review_status', 1)
        ->where('service_disposition_funds_status', 0)
        ->join('core_service', 'core_service.service_id', 'trans_service_disposition.service_id')
        ->get();

        return view('content/TransServiceDispositionFunds/SearchTransServiceDispositionReview',compact('transservicedisposition'));
    }

    public function addReset($service_requisition_id)
    {
        Session::forget('sess_servicerequisitiontokenedit');

        return redirect('/trans-service-disposition/add/'.$service_requisition_id);
    }

    public function addTransServiceDispositionFunds($service_disposition_id)
    {
        $service_disposition_review_token_edit		    = Session::get('sess_servicedispositionreviewtokenedit');

        if (empty($service_disposition_review_token_edit)){
            $service_disposition_review_token_edit = md5(date("YmdHis"));
            Session::put('sess_servicedispositionreviewtokenedit', $service_disposition_review_token_edit);
        }

        $service_disposition_review_token_edit		= Session::get('sess_servicedispositionreviewtokenedit');

        $servicedisposition = TransServiceDisposition::findOrFail($service_disposition_id);

        $servicedispositionterm = TransServiceDispositionTerm::select('trans_service_disposition_term.*', 'core_service_term.*')
        ->join('core_service_term', 'core_service_term.service_term_id', 'trans_service_disposition_term.service_term_id')
        ->where('service_disposition_id', $service_disposition_id)
        ->where('trans_service_disposition_term.data_state', 0)
        ->get();

        $servicedispositionparameter = TransServiceDispositionParameter::select('trans_service_disposition_parameter.*', 'core_service_parameter.*')
        ->join('core_service_parameter', 'core_service_parameter.service_parameter_id', 'trans_service_disposition_parameter.service_parameter_id')
        ->where('service_disposition_id', $service_disposition_id)
        ->where('trans_service_disposition_parameter.data_state', 0)
        ->get();

        return view('content/TransServiceDispositionFunds/FormAddTransServiceDispositionFunds',compact('servicedisposition', 'servicedispositionparameter', 'servicedispositionterm', 'service_disposition_review_token_edit', 'service_disposition_id'));
    }

    public function detailTransServiceDispositionFunds($service_disposition_id){
        $servicedisposition = TransServiceDisposition::findOrFail($service_disposition_id);

        $servicedispositionterm = TransServiceDispositionTerm::select('trans_service_disposition_term.*', 'core_service_term.*')
        ->join('core_service_term', 'core_service_term.service_term_id', 'trans_service_disposition_term.service_term_id')
        ->where('service_disposition_id', $service_disposition_id)
        ->where('trans_service_disposition_term.data_state', 0)
        ->get();

        $servicedispositionparameter = TransServiceDispositionParameter::select('trans_service_disposition_parameter.*', 'core_service_parameter.*')
        ->join('core_service_parameter', 'core_service_parameter.service_parameter_id', 'trans_service_disposition_parameter.service_parameter_id')
        ->where('service_disposition_id', $service_disposition_id)
        ->where('trans_service_disposition_parameter.data_state', 0)
        ->get();

        return view('content/TransServiceDispositionFunds/FormDetailTransServiceDispositionFunds',compact('servicedisposition', 'servicedispositionterm', 'servicedispositionparameter', 'service_disposition_id'));
    }

    public function unApproveTransServiceDispositionReview($service_disposition_id){
        $service_disposition_review_token_edit		    = Session::get('sess_servicedispositionreviewtokenedit');

        if (empty($service_disposition_review_token_edit)){
            $service_disposition_review_token_edit = md5(date("YmdHis"));
            Session::put('sess_servicedispositionreviewtokenedit', $service_disposition_review_token_edit);
        }

        $service_disposition_review_token_edit		= Session::get('sess_servicedispositionreviewtokenedit');

        $servicedisposition = TransServiceDisposition::findOrFail($service_disposition_id);

        $servicedispositionterm = TransServiceDispositionTerm::select('trans_service_disposition_term.*', 'core_service_term.*')
        ->join('core_service_term', 'core_service_term.service_term_id', 'trans_service_disposition_term.service_term_id')
        ->where('service_disposition_id', $service_disposition_id)
        ->where('trans_service_disposition_term.data_state', 0)
        ->get();

        $servicedispositionparameter = TransServiceDispositionParameter::select('trans_service_disposition_parameter.*', 'core_service_parameter.*')
        ->join('core_service_parameter', 'core_service_parameter.service_parameter_id', 'trans_service_disposition_parameter.service_parameter_id')
        ->where('service_disposition_id', $service_disposition_id)
        ->where('trans_service_disposition_parameter.data_state', 0)
        ->get();

        $coresection    = CoreSection::where('data_state', 0)
        ->pluck('section_name', 'section_id');

        return view('content/TransServiceDispositionReview/FormUnApproveTransServiceDispositionReview',compact('servicedisposition', 'servicedispositionparameter', 'servicedispositionterm', 'service_disposition_review_token_edit', 'service_disposition_id', 'coresection'));
    }
    
    public function addElementsCoreService(Request $request)
    {
        $data_coreservice[$request->name] = $request->value;

        Session::put('data_coreservice', $data_coreservice);
        
        return redirect('/service/add');
    }

    public function processAddTransServiceDispositionFunds(Request $request)
    {
        $fields = $request->validate([
            'service_disposition_id'                    => 'required',
            'service_disposition_amount'                => 'required',
        ]);
        
        $fileNameToStoreApplication = '';

        if($request->hasFile('file_funds_application')){
            $filenameWithExt            = $request->file('file_funds_application')->getClientOriginalName();
            $filename                   = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension                  = $request->file('file_funds_application')->getClientOriginalExtension();
            $fileNameToStoreApplication = $filename.'_'.time().'.'.$extension;
            $path                       = $request->file('file_funds_application')->storeAs('public/fundsapplication/'.$fields['service_disposition_id'],$fileNameToStoreApplication);
        }else{
            $msg = "Upload File Surat Permohonan Pencairan!";
            return redirect('/trans-service-disposition-funds/add/'.$fields['service_disposition_id'])->with('msg',$msg);
        }
        
        $fileNameToStoreOrder = '';

        if($request->hasFile('file_funds_order')){
            $filenameWithExt        = $request->file('file_funds_order')->getClientOriginalName();
            $filename               = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            $extension              = $request->file('file_funds_order')->getClientOriginalExtension();
            $fileNameToStoreOrder   = $filename.'_'.time().'.'.$extension;
            $path                   = $request->file('file_funds_order')->storeAs('public/fundsorder/'.$fields['service_disposition_id'],$fileNameToStoreOrder);
        }else{
            $msg = "Upload File Surat Perintah Pencairan!";
            return redirect('/trans-service-disposition-funds/add/'.$fields['service_disposition_id'])->with('msg',$msg);
        }

        $servicedisposition = TransServiceDisposition::findOrFail($fields['service_disposition_id']);
        $servicedisposition->service_id                         = $servicedisposition['service_id'];
        $servicedisposition->service_disposition_funds_status   = 1;
        $servicedisposition->funds_amount_id                    = Auth::id();
        $servicedisposition->funds_amount_at                    = date('Y-m-d H:i:s');
        $servicedisposition->service_disposition_amount         = $fields['service_disposition_amount'];
        $servicedisposition->file_funds_application             = $fileNameToStoreApplication;
        $servicedisposition->file_funds_order                   = $fileNameToStoreOrder;
            
        $service_requisition_id = $servicedisposition['service_requisition_id'];
        $servicerequisition     = TransServiceRequisition::where('data_state', 0)
        ->where('service_requisition_id', $service_requisition_id)
        ->first(); 

        if($servicedisposition->save()){
            $username = User::select('name')->where('user_id','=',Auth::id())->first();

            $this->set_log(Auth::id(), $username['name'],'1089','Application.TransServiceDispositionFunds.processAddTransServiceDispositionFunds',$username['name'],'Add Trans Service Disposition Funds');

            $servicerequisition->service_requisition_status = 7;
            if($servicerequisition->save()){  
                $msg = "Pencairan Disposisi Bantuan Berhasil";
            }else{
                $msg = "Pencairan Disposisi Bantuan Gagal";
                return redirect('/trans-service-disposition-funds/add/'.$fields['service_disposition_id'])->with('msg',$msg);
            }
        
            $disposition_data = TransServiceDisposition::findOrFail($fields['service_disposition_id']);

            $service_log = array(
                'service_status'            => 11,
                'service_requisition_no'    => $disposition_data['service_requisition_no'],
                'section_id'                => 11,
                'created_id'                => Auth::id(),
            );
            TransServiceLog::create($service_log);

            $wa_msg = "Siabas\r\n\r\n\r\nNama : ".$disposition_data['service_requisition_name']."\r\n\r\nNomor Pengajuan : ".$disposition_data['service_requisition_no']."\r\n\r\nJenis Pengajuan : ".$this->getServiceName($disposition_data['service_id'])."\r\n\r\nBagian : ".$this->getSectionName(11)."\r\n\r\nPesan : ".$this->getMessage(10);
            $wa_status = $this->getMessageStatus(10);
            $wa_no  = $disposition_data['service_requisition_phone'];
            $this->postWhatsappMessages($wa_msg, $wa_status, $wa_no);

            $msg = "Pencairan Disposisi Bantuan Berhasil";
            return redirect('/trans-service-disposition-funds')->with('msg',$msg);
        }else{
            $msg = "Pencairan Disposisi Bantuan Gagal";
            return redirect('/trans-service-disposition-funds/add/'.$fields['service_disposition_id'])->with('msg',$msg);
        }
    }

    public function processDisapproveTransServiceDispositionReview(Request $request)
    {
        $fields = $request->validate([
            'service_disposition_id'                    => 'required',
            'disapprove_remark'                         => 'required',
            'service_disposition_review_token_edit'     => 'required',
        ]);

        $servicedisposition = TransServiceDisposition::findOrFail($fields['service_disposition_id']);
        $servicedisposition->review_status     = 2;
        $servicedisposition->disapprove_id     = Auth::id();
        $servicedisposition->disapprove_at     = date('Y-m-d H:i:s');
        $servicedisposition->disapprove_remark = $fields['disapprove_remark'];

        $service_disposition_review_token_edit = TransServiceDisposition::select('service_disposition_token_edit')
            ->where('service_disposition_token_edit', $fields['service_disposition_review_token_edit'])
            ->count();
            
        $service_requisition_id = $servicedisposition['service_requisition_id'];
        $servicerequisition     = TransServiceRequisition::where('data_state', 0)
        ->where('service_requisition_id', $service_requisition_id)
        ->first(); 

        if($service_disposition_review_token_edit == 0){
            if($servicedisposition->save()){
                $username = User::select('name')->where('user_id','=',Auth::id())->first();

                $this->set_log(Auth::id(), $username['name'],'1089','Application.TransServiceDispositionReview.processAddTransServiceDispositionReview',$username['name'],'Add Trans Service Disposition Review');

                $servicerequisition->service_requisition_status = 5;
                if($servicerequisition->save()){  
                    $msg = "Disapprove Review Disposisi Bantuan Berhasil";
                }else{
                    $msg = "Disapprove Review Disposisi Bantuan Gagal";
                    return redirect('/trans-service-disposition-funds/add/'.$fields['service_disposition_id'])->with('msg',$msg);
                }
            
                $disposition_data = TransServiceDisposition::findOrFail($fields['service_disposition_id']);

                $service_log = array(
                    'service_status'            => 9,
                    'service_requisition_no'    => $disposition_data['service_requisition_no'],
                    'section_id'                => $disposition_data['section_id'],
                    'created_id'                => Auth::id(),
                );
                TransServiceLog::create($service_log);
    
                $wa_msg = "Siabas\r\n\r\n\r\nNama : ".$disposition_data['service_requisition_name']."\r\n\r\nNomor Pengajuan : ".$disposition_data['service_requisition_no']."\r\n\r\nJenis Pengajuan : ".$this->getServiceName($disposition_data['service_id'])."\r\n\r\nBagian : ".$this->getSectionName(1)."\r\n\r\nPesan : ".$this->getMessage(8);
                $wa_status = $this->getMessageStatus(8);
                $wa_no  = $disposition_data['service_requisition_phone'];
                $this->postWhatsappMessages($wa_msg, $wa_status, $wa_no);

                $msg = "Review Disposisi Bantuan Berhasil";
                return redirect('/trans-service-disposition-funds')->with('msg',$msg);
            }else{
                $msg = "Review Disposisi Bantuan Gagal";
                return redirect('/trans-service-disposition-funds/add/'.$fields['service_disposition_id'])->with('msg',$msg);
            }
        }else{
            $servicerequisition->service_requisition_status = 5;
            if($servicerequisition->save()){  
                $msg = "Review Disposisi Bantuan Berhasil";
            }else{
                $msg = "Review Disposisi Bantuan Gagal";
                return redirect('/trans-service-disposition-funds/add/'.$fields['service_disposition_id'])->with('msg',$msg);
            }
            
            $disposition_data = TransServiceDisposition::findOrFail($fields['service_disposition_id']);

            $service_log = array(
                'service_status'            => 9,
                'service_requisition_no'    => $disposition_data['service_requisition_no'],
                'section_id'                => $disposition_data['section_id'],
                'created_id'                => Auth::id(),
            );
            TransServiceLog::create($service_log);

            $wa_msg = "Siabas\r\n\r\n\r\nNama : ".$disposition_data['service_requisition_name']."\r\n\r\nNomor Pengajuan : ".$disposition_data['service_requisition_no']."\r\n\r\nJenis Pengajuan : ".$this->getServiceName($disposition_data['service_id'])."\r\n\r\nBagian : ".$this->getSectionName($disposition_data['section_id'])."\r\n\r\nPesan : ".$this->getMessage(8);
            $wa_status = $this->getMessageStatus(8);
            $wa_no  = $disposition_data['service_requisition_phone'];
            $this->postWhatsappMessages($wa_msg, $wa_status, $wa_no);
            
            $msg = "Review Disposisi Bantuan Berhasil";
            return redirect('/trans-service-disposition-review')->with('msg',$msg);
        }
    }

    public function processUnApproveTransServiceDispositionReview(Request $request)
    {
        $fields = $request->validate([
            'service_disposition_id'                  => 'required',
            'unreview_remark'                         => 'required',
            'service_disposition_review_token_edit'   => 'required',
        ]);

        $servicedisposition = TransServiceDisposition::findOrFail($fields['service_disposition_id']);
        $servicedisposition->review_status   = 0;
        $servicedisposition->unreview_id       = Auth::id();
        $servicedisposition->unreview_at       = date('Y-m-d H:i:s');
        $servicedisposition->unreview_remark   = $fields['unreview_remark'];

        $service_disposition_review_token_edit  = TransServiceDisposition::select('service_disposition_token_edit')
            ->where('service_disposition_token_edit', $fields['service_disposition_review_token_edit'])
            ->count();
            
        $service_requisition_id = $servicedisposition['service_requisition_id'];
        $servicerequisition     = TransServiceRequisition::where('data_state', 0)
        ->where('service_requisition_id', $service_requisition_id)
        ->first(); 

        if($service_disposition_review_token_edit == 0){
            if($servicedisposition->save()){
                $username = User::select('name')->where('user_id','=',Auth::id())->first();

                $this->set_log(Auth::id(), $username['name'],'1089','Application.TransServiceDispositionReview.processUnApproveTransServiceDispositionReview',$username['name'],'UnApprove Trans Service Disposition Review');

                $servicerequisition->service_requisition_status = 3;
                if($servicerequisition->save()){  
                    $msg = "Pembatalan Review Disposisi Bantuan Berhasil";
                }else{
                    $msg = "Pembatalan Review Disposisi Bantuan Gagal";
                    return redirect('/trans-service-disposition-review/unapprove/'.$fields['service_disposition_id'])->with('msg',$msg);
                }
            
                $disposition_data = TransServiceDisposition::findOrFail($fields['service_disposition_id']);

                $service_log = array(
                    'service_status'            => 7,
                    'service_requisition_no'    => $disposition_data['service_requisition_no'],
                    'section_id'                => $disposition_data['section_id'],
                    'created_id'                => Auth::id(),
                );
                TransServiceLog::create($service_log);
    
                $wa_msg = "Siabas\r\n\r\n\r\nNama : ".$disposition_data['service_requisition_name']."\r\n\r\nNomor Pengajuan : ".$disposition_data['service_requisition_no']."\r\n\r\nJenis Pengajuan : ".$this->getServiceName($disposition_data['service_id'])."\r\n\r\nBagian : ".$this->getSectionName($disposition_data['section_id'])."\r\n\r\nPesan : ".$this->getMessage(7);
                $wa_status = $this->getMessageStatus(7);
                $wa_no  = $disposition_data['service_requisition_phone'];
                $this->postWhatsappMessages($wa_msg, $wa_status, $wa_no);

                $msg = "Pembatalan Review Disposisi Bantuan Berhasil";
                return redirect('/trans-service-disposition-review')->with('msg',$msg);
            }else{
                $msg = "Pembatalan Review Disposisi Bantuan Gagal";
                return redirect('/trans-service-disposition-review/unapprove/'.$fields['service_disposition_id'])->with('msg',$msg);
            }
        }else{
            $servicerequisition->service_requisition_status = 3;
            if($servicerequisition->save()){  
                $msg = "Pembatalan Review Disposisi Bantuan Berhasil";
            }else{
                $msg = "Pembatalan Review Disposisi Bantuan Gagal";
                return redirect('/trans-service-disposition-review/unapprove/'.$fields['service_disposition_id'])->with('msg',$msg);
            }
            
            $disposition_data = TransServiceDisposition::findOrFail($fields['service_disposition_id']);

            $service_log = array(
                'service_status'            => 7,
                'service_requisition_no'    => $disposition_data['service_requisition_no'],
                'section_id'                => $disposition_data['section_id'],
                'created_id'                => Auth::id(),
            );
            TransServiceLog::create($service_log);

            $wa_msg = "Siabas\r\n\r\n\r\nNama : ".$disposition_data['service_requisition_name']."\r\n\r\nNomor Pengajuan : ".$disposition_data['service_requisition_no']."\r\n\r\nJenis Pengajuan : ".$this->getServiceName($disposition_data['service_id'])."\r\n\r\nBagian : ".$this->getSectionName($disposition_data['section_id'])."\r\n\r\nPesan : ".$this->getMessage(7);
            $wa_status = $this->getMessageStatus(7);
            $wa_no  = $disposition_data['service_requisition_phone'];
            $this->postWhatsappMessages($wa_msg, $wa_status, $wa_no);
            
            $msg = "Pembatalan Review Disposisi Bantuan Berhasil";
            return redirect('/trans-service-disposition-review')->with('msg',$msg);
        }
    }

    public function editReset($service_id)
    {
        Session::forget('data_coreserviceterm');
        Session::forget('data_coreserviceterm_first');
        Session::forget('data_coreserviceparameter_first');

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

    public function downloadTransServiceDispositionTerm($service_id, $service_disposition_term_id){
        $dispositionterm = TransServiceDispositionTerm::findOrFail($service_disposition_term_id);
        
        return response()->download(
            storage_path('app/public/term/'.$service_id.'/'.$dispositionterm['service_disposition_term_value']),
            'term_'.$dispositionterm['service_disposition_term_id'].'.png',
        );
    }

    public function downloadTransServiceDispositionReviewSK($service_disposition_id){
        $dispositionsk = TransServiceDisposition::findOrFail($service_disposition_id);
        
        return response()->download(
            storage_path('app/public/sk/'.$dispositionsk['service_disposition_id'].'/'.$dispositionsk['file_sk']),
            $dispositionsk['file_sk'],
        );
    }
    
    public function printApplication($service_disposition_id){
        $transservicedisposition = TransServiceDisposition::findOrFail($service_disposition_id);

        $coreservice = CoreService::where('service_id', $transservicedisposition['service_id'])
        ->where('data_state', 0)
        ->first();

        $username = User::select('name')->where('user_id','=',Auth::id())->first();

        $this->set_log(Auth::id(), $username['name'],'1089','Application.TransServiceDispositionFunds.print',$username['name'],'Export');

        $pdf = new TCPDF('P', PDF_UNIT, 'A4', true, 'UTF-8', false);

        $pdf::SetPrintHeader(false);
        $pdf::SetPrintFooter(false);

        $pdf::SetMargins(6, 6, 6, 6);

        $pdf::setImageScale(PDF_IMAGE_SCALE_RATIO);

        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
            $pdf::setLanguageArray($l);
        }

        // ---------------------------------------------------------

        $pdf::SetFont('helvetica', 'B', 20);
        $pdf::AddPage('L', 'mm', array(215.9, 330.2), true, 'UTF-8', false);
        $pdf::SetFont('helvetica', '', 10);
        $pdf::setImageScale(PDF_IMAGE_SCALE_RATIO);

        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
            $pdf::setLanguageArray($l);
        }

        // -----------------------------------------------------------------------------

        $datetime = strtotime($transservicedisposition['created_at']);
        $date = date('d-m-Y', $datetime);
        $style = array(
            'align-item' => 'right',
        );
        $style2 = array(
            'align-item' => 'left',
        );

        $monthname = array(
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember',
        );
        
        $path = public_path('resources/img/paraf.png');
        $export = "
        <table cellspacing=\"0\" cellpadding=\"2\" border=\"0\" style=\"font-weight: bold;\">
            <tr>
                <td width=\"100%\"><div style=\"text-align:left;\">Kepada Yth : Bagian Keuangan & Pelaporan BAZNAS Kota Surakarta</div></td>
            </tr>
            <tr>
                <td width=\"100%\"><div style=\"text-align:center;\">SURAT PERMOHONAN  PENCAIRAN  DANA  ZAKAT</div></td>
            </tr>
            <tr>
                <td width=\"100%\"><div style=\"text-align:center;\">BULAN ".strtoupper($monthname[date('m')]).' '.date('Y')."</div></td>
            </tr>
        </table>
        <br/>
        <br/>
        <table cellspacing=\"0\" cellpadding=\"2\" border=\"1\">
            <tr style=\"font-weight: bold;\">
                <td width=\"5%\"><div style=\"text-align:center;\">NO</div></td>
                <td width=\"20%\"><div style=\"text-align:center;\">JENIS</div></td>
                <td width=\"35%\"><div style=\"text-align:center;\">URAIAN</div></td>
                <td width=\"10%\"><div style=\"text-align:center;\">JUMLAH</div></td>
                <td width=\"20%\"><div style=\"text-align:center;\">KETERANGAN</div></td>
                <td width=\"10%\"><div style=\"text-align:center;\">ANGGARAN TERSEDIA</div></td>
            </tr>
            <tr>
                <td style=\"height:100px\"><div style=\"text-align:center;\">1.</div></td>
                <td><div style=\"text-align:center;\"></div></td>
                <td><div style=\"text-align:left;\">".$coreservice['service_name'].' '.$transservicedisposition['service_requisition_name'].' Bulan '.$monthname[date('m')].' '.date('Y')."</div></td>
                <td><div style=\"text-align:left;\">Rp</div></td>
                <td rowspan=\"2\"><div style=\"text-align:left;\">".$transservicedisposition['review_remark']."</div></td>
                <td><div style=\"text-align:left;\">Rp</div></td>
            </tr>
            <tr style=\"font-weight: bold;\">
                <td width=\"60%\"><div style=\"text-align:center;\">Jumlah</div></td>
                <td width=\"10%\"><div style=\"text-align:left;\">Rp</div></td>
                <td width=\"10%\"><div style=\"text-align:left;\"></div></td>
            </tr>
        </table>
        <br/>
        <br/>
        <br/>
        <br/>
        <table cellspacing=\"0\" cellpadding=\"2\" border=\"0\">
            <tr>
                <td width=\"72.5%\"><div style=\"text-align:center;\"></div></td>
                <td width=\"25%\"><div style=\"text-align:center;\">Surakarta, ".date('d').' '.$monthname[date('m')].' '.date('Y')."</div></td>
                <td width=\"2.5%\"><div style=\"text-align:center;\"></div></td>
            </tr>
            <tr>
                <td width=\"2.5%\"><div style=\"text-align:center;\"></div></td>
                <td width=\"25%\"><div style=\"text-align:center;\">Wakil Ketua II</div></td>
                <td width=\"10%\"><div style=\"text-align:center;\"></div></td>
                <td width=\"25%\"><div style=\"text-align:center;\">Kepala Pelaksana</div></td>
                <td width=\"10%\"><div style=\"text-align:center;\"></div></td>
                <td width=\"25%\"><div style=\"text-align:center;\">Bagian Pendistribusian dan Pendayagunaan</div></td>
                <td width=\"2.5%\"><div style=\"text-align:center;\"></div></td>
            </tr>
            <tr>
                <td style=\"height:70px\"></td>
            </tr>
            <tr>
                <td width=\"2.5%\"><div style=\"text-align:center;\"></div></td>
                <td width=\"25%\"><div style=\"text-align:center;\">Drs. Sarwaka</div></td>
                <td width=\"10%\"><div style=\"text-align:center;\"></div></td>
                <td width=\"25%\"><div style=\"text-align:center;\">Dewi Purwantiningsih, S.E.</div></td>
                <td width=\"10%\"><div style=\"text-align:center;\"></div></td>
                <td width=\"25%\"><div style=\"text-align:center;\">Sepby Widyo Utomo, S.Kom.</div></td>
                <td width=\"2.5%\"><div style=\"text-align:center;\"></div></td>
            </tr>
        </table>
        ";

        $pdf::writeHTML($export, true, false, false, false, '');
        $pdf::Image( $path, 133, 88, 30, 30, 'PNG', '', 'LT', false, 300, '', false, false, 1, false, false, false);

        if (ob_get_contents()) ob_end_clean();
        // -----------------------------------------------------------------------------
        
        $filename = 'Bukti Pengajuan Bantuan_'.$transservicedisposition['service_requisition_id'].'.pdf';
        $pdf::Output($filename, 'I');
    }
    
    public function printWarrant($service_disposition_id){
        $transservicedisposition = TransServiceDisposition::findOrFail($service_disposition_id);

        $coreservice = CoreService::where('service_id', $transservicedisposition['service_id'])
        ->where('data_state', 0)
        ->first();

        $username = User::select('name')->where('user_id','=',Auth::id())->first();

        $this->set_log(Auth::id(), $username['name'],'1089','Application.TransServiceDispositionFunds.print',$username['name'],'Export');

        $pdf = new TCPDF('P', PDF_UNIT, 'A4', true, 'UTF-8', false);

        $pdf::SetPrintHeader(false);
        $pdf::SetPrintFooter(false);

        $pdf::SetMargins(6, 6, 6, 6);

        $pdf::setImageScale(PDF_IMAGE_SCALE_RATIO);

        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
            $pdf::setLanguageArray($l);
        }

        // ---------------------------------------------------------

        $pdf::SetFont('helvetica', 'B', 20);
        $pdf::AddPage('P', 'mm', array(215.9, 330.2), true, 'UTF-8', false);
        $pdf::SetFont('helvetica', '', 10);
        $pdf::setImageScale(PDF_IMAGE_SCALE_RATIO);

        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
            $pdf::setLanguageArray($l);
        }

        // -----------------------------------------------------------------------------

        $datetime = strtotime($transservicedisposition['created_at']);
        $date = date('d-m-Y', $datetime);
        $style = array(
            'align-item' => 'right',
        );
        $style2 = array(
            'align-item' => 'left',
        );

        $monthname = array(
            '01' => 'Januari',
            '02' => 'Februari',
            '03' => 'Maret',
            '04' => 'April',
            '05' => 'Mei',
            '06' => 'Juni',
            '07' => 'Juli',
            '08' => 'Agustus',
            '09' => 'September',
            '10' => 'Oktober',
            '11' => 'November',
            '12' => 'Desember',
        );
        
        $path = public_path('resources/img/paraf.png');
        $export = "
        <table cellspacing=\"0\" cellpadding=\"2\" border=\"0\" style=\"font-weight: bold;\">
            <tr>
                <td width=\"100%\"><div style=\"text-align:center;\">SURAT PERINTAH PENCAIRAN DANA</div></td>
            </tr>
            <tr>
                <td width=\"100%\"><div style=\"text-align:center;\">BAZNAS KOTA SURAKARTA</div></td>
            </tr>
        </table>
        <br/>
        <br/>
        <table cellspacing=\"0\" cellpadding=\"2\" border=\"0\">
            <tr>
                <td width=\"20%\"><div style=\"text-align:left;\">Nomor SK</div></td>
                <td width=\"2%\"><div style=\"text-align:left;\">:</div></td>
                <td width=\"78%\"><div style=\"text-align:left;\"></div></td>
            </tr>
            <tr>
                <td width=\"20%\"><div style=\"text-align:left;\">Tanggal</div></td>
                <td width=\"2%\"><div style=\"text-align:left;\">:</div></td>
                <td width=\"78%\"><div style=\"text-align:left;\">".date('d').' '.$monthname[date('m')].' '.date('Y')."</div></td>
            </tr>
        </table>
        <br/>
        <br/>
        <table cellspacing=\"0\" cellpadding=\"2\" border=\"0\">
            <tr>
                <td width=\"20%\"><div style=\"text-align:left;\">Dari</div></td>
                <td width=\"2%\"><div style=\"text-align:left;\">:</div></td>
                <td width=\"78%\"><div style=\"text-align:left;\">Wakil Ketua III</div></td>
            </tr>
            <tr>
                <td width=\"20%\"><div style=\"text-align:left;\">Kepada</div></td>
                <td width=\"2%\"><div style=\"text-align:left;\">:</div></td>
                <td width=\"78%\"><div style=\"text-align:left;\">Kepala Pelaksana</div></td>
            </tr>
        </table>
        <br/>
        <br/>
        <table cellspacing=\"0\" cellpadding=\"2\" border=\"0\">
            <tr>
                <td width=\"33%\"><div style=\"text-align:left;\">Agar mencairkan uang sebesar</div></td>
                <td width=\"2%\"><div style=\"text-align:left;\">:</div></td>
                <td width=\"65%\"><div style=\"text-align:left; font-style:italic;\">Rp ".number_format($transservicedisposition['service_disposition_amount'])."</div></td>
            </tr>
            <tr>
                <td width=\"100%\"><div style=\"text-align:left; font-style:italic;\">".$this->numtotxt($transservicedisposition['service_disposition_amount'])."</div></td>
            </tr>
        </table>
        <br/>
        <br/>
        <table cellspacing=\"0\" cellpadding=\"2\" border=\"0\">
            <tr>
                <td width=\"20%\"><div style=\"text-align:left;\">Dari dana</div></td>
                <td width=\"2%\"><div style=\"text-align:left;\">:</div></td>
                <td width=\"11%\"><div style=\"text-align:left;\">Zakat</div></td>
                <td width=\"2%\"><div style=\"text-align:left;\">:</div></td>
                <td width=\"65%\"><div style=\"text-align:left;\">Rp </div></td>
            </tr>
            <tr>
                <td width=\"22%\"><div style=\"text-align:left;\"></div></td>
                <td width=\"11%\"><div style=\"text-align:left;\">Infaq</div></td>
                <td width=\"2%\"><div style=\"text-align:left;\">:</div></td>
                <td width=\"65%\"><div style=\"text-align:left;\">Rp </div></td>
            </tr>
            <tr>
                <td width=\"22%\"><div style=\"text-align:left;\"></div></td>
                <td width=\"11%\"><div style=\"text-align:left;\">Amil</div></td>
                <td width=\"2%\"><div style=\"text-align:left;\">:</div></td>
                <td width=\"65%\"><div style=\"text-align:left;\">Rp </div></td>
            </tr>
            <tr>
                <td width=\"22%\"><div style=\"text-align:left;\"></div></td>
                <td width=\"11%\"><div style=\"text-align:left;\">APBD</div></td>
                <td width=\"2%\"><div style=\"text-align:left;\">:</div></td>
                <td width=\"65%\"><div style=\"text-align:left;\">Rp </div></td>
            </tr>
            <tr>
                <td width=\"22%\"><div style=\"text-align:left;\"></div></td>
                <td width=\"11%\"><div style=\"text-align:left;\">Non-Halal</div></td>
                <td width=\"2%\"><div style=\"text-align:left;\">:</div></td>
                <td width=\"65%\"><div style=\"text-align:left;\">Rp </div></td>
            </tr>
            <tr style=\"font-style:italic;\">
                <td width=\"22%\"><div style=\"text-align:left;\"></div></td>
                <td width=\"11%\"><div style=\"text-align:left; border-top: 1 solid black;\">Jumlah</div></td>
                <td width=\"2%\"><div style=\"text-align:left; border-top: 1 solid black;\">:</div></td>
                <td width=\"15%\"><div style=\"text-align:left; border-top: 1 solid black;\">Rp ".number_format($transservicedisposition['service_disposition_amount'])."</div></td>
            </tr>
        </table>
        <br/>
        <br/>
        <table cellspacing=\"0\" cellpadding=\"2\" border=\"0\">
            <tr>
                <td width=\"20%\"><div style=\"text-align:left;\">Untuk Keperluan</div></td>
                <td width=\"2%\"><div style=\"text-align:left;\">:</div></td>
                <td width=\"78%\"><div style=\"text-align:left;\">Sebagaimana terlampir</div></td>
            </tr>
        </table>
        <br/>
        <br/>
        <br/>
        <br/>
        <table cellspacing=\"0\" cellpadding=\"2\" border=\"0\">
            <tr>
                <td width=\"35%\"><div style=\"text-align:center;\">Yang diberi perintah</div></td>
                <td width=\"30%\"><div style=\"text-align:center;\"></div></td>
                <td width=\"35%\"><div style=\"text-align:center;\">Surakarta, ".date('d').' '.$monthname[date('m')].' '.date('Y')."</div></td>
            </tr>
            <tr>
                <td width=\"35%\"><div style=\"text-align:center;\">Kepala Pelaksana</div></td>
                <td width=\"30%\"><div style=\"text-align:center;\"></div></td>
                <td width=\"35%\"><div style=\"text-align:center;\">Wakil Ketua III</div></td>
            </tr>
            <tr>
                <td style=\"height: 70px\"></td>
            </tr>
            <tr>
                <td width=\"35%\"><div style=\"text-align:center;\">Dewi Purwantiningsih, S.E.</div></td>
                <td width=\"30%\"><div style=\"text-align:center;\"></div></td>
                <td width=\"35%\"><div style=\"text-align:center;\">Suparto, S.Sos., M.M.</div></td>
            </tr>
        </table>
        ";

        $pdf::writeHTML($export, true, false, false, false, '');
        $pdf::Image( $path, 25, 133, 30, 30, 'PNG', '', 'LT', false, 300, '', false, false, 1, false, false, false);

        if (ob_get_contents()) ob_end_clean();
        // -----------------------------------------------------------------------------
        
        $filename = 'Bukti Pengajuan Bantuan_'.$transservicedisposition['service_requisition_id'].'.pdf';
        $pdf::Output($filename, 'I');
    }
    
    public function printRegisterNo($service_disposition_id){
        $transservicedisposition = TransServiceDisposition::select('service_register_no')
        ->where('service_disposition_id', $service_disposition_id)
        ->first();

        $style = array(
            'align-item' => 'center',
        );

        $pdf = new TCPDF('P', PDF_UNIT, 'A4', true, 'UTF-8', false);

        $pdf::SetPrintHeader(false);
        $pdf::SetPrintFooter(false);

        $pdf::SetMargins(6, 6, 6, 6);

        $pdf::setImageScale(PDF_IMAGE_SCALE_RATIO);

        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
            $pdf::setLanguageArray($l);
        }

        // ---------------------------------------------------------

        $pdf::SetFont('helvetica', 'B', 20);
        $pdf::AddPage('L', array(90, 100), false, false);

        if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
            require_once(dirname(__FILE__).'/lang/eng.php');
            $pdf::setLanguageArray($l);
        }

        // -----------------------------------------------------------------------------

        $pdf::Rect(0, 0, 100, 35, 'DF', '',  array(255,255,255));
        $pdf::Rect(10, 2, 80, 18, 'DF', '',  array(15, 110, 51));
        $export = "
        <div style=\"border: 2px solid white; border-radius: 50px; background-color: #0f6e33; color: white; text-align:center;\">
            NO. REG. ".$transservicedisposition['service_register_no']."
        </div>
        ";
        $pdf::writeHTML($export, true, false, false, false, '');
        $pdf::write2DBarcode($transservicedisposition['service_register_no'], 'QRCODE,H', 30, 25, 40, 40, $style, 'N');

        if (ob_get_contents()) ob_end_clean();
        // -----------------------------------------------------------------------------
        
        $filename = 'Bukti Pengajuan Bantuan_'.$transservicedisposition['service_requisition_id'].'.pdf';
        $pdf::Output($filename, 'I');
    }
}
