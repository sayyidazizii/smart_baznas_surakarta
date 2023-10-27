<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\CoreSection;
use App\Models\CoreService;
use App\Models\CoreServiceTerm;
use App\Models\CoreServiceParameter;
use App\Models\TransServiceDocumentRequisition;
use App\Models\TransServiceRequisition;
use App\Models\TransServiceRequisitionTerm;
use App\Models\TransServiceRequisitionParameter;
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

class TransServiceDispositionReviewController extends Controller
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
        Session::forget('sess_servicedispositionreviewtoken');
        Session::forget('sess_servicedispositionreviewtokenedit');
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

        $transservicedisposition1 = TransServiceDisposition::where('data_state', 0)
        ->where('created_at','>=',$start_date)
        ->where('created_at','<=',$stop_date)
        ->where('approved_status', 1)
        ->where('review_status', 1)
        ->get();

        $transservicedisposition2 = TransServiceDisposition::where('data_state', 0)
        ->where('created_at','>=',$start_date)
        ->where('created_at','<=',$stop_date)
        ->where('approved_status', 1)
        ->where('review_status', 2)
        ->get();

        $transservicedisposition = $transservicedisposition1->merge($transservicedisposition2);

        return view('content/TransServiceDispositionReview/ListTransServiceDispositionReview',compact('transservicedisposition', 'start_date', 'end_date'));
    }
    

    public function filter(Request $request){
        $start_date     = $request->start_date;
        $end_date       = $request->end_date;

        Session::put('start_date', $start_date);
        Session::put('end_date', $end_date);

        return redirect('/trans-service-disposition-review');
    }

    public function search()
    {
        Session::forget('sess_servicedispositiontokenreview');
        $transservicedisposition = TransServiceDisposition::select('trans_service_disposition.*', 'core_service.service_name')
        ->where('trans_service_disposition.data_state', 0)
        ->where('approved_status', 1)
        ->where('review_status', 0)
        ->join('core_service', 'core_service.service_id', 'trans_service_disposition.service_id')
        ->get();

        return view('content/TransServiceDispositionReview/SearchTransServiceDispositionApproval',compact('transservicedisposition'));
    }

    public function addReset($service_requisition_id)
    {
        Session::forget('sess_servicerequisitiontokenedit');

        return redirect('/trans-service-disposition/add/'.$service_requisition_id);
    }

    public function addTransServiceDispositionReview($service_disposition_id)
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

        return view('content/TransServiceDispositionReview/FormAddTransServiceDispositionReview',compact('servicedisposition', 'servicedispositionparameter', 'servicedispositionterm', 'service_disposition_review_token_edit', 'service_disposition_id'));
    }

    public function detailTransServiceDispositionReview($service_disposition_id){
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

        return view('content/TransServiceDispositionReview/FormDetailTransServiceDispositionReview',compact('servicedisposition', 'servicedispositionterm', 'servicedispositionparameter', 'service_disposition_id'));
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

    public function processAddTransServiceDispositionReview(Request $request)
    {
        $fields = $request->validate([
            'service_disposition_id'                    => 'required',
            'review_remark'                             => 'required',
            'service_disposition_review_token_edit'     => 'required',
        ]);
        
        $fileNameToStore = '';

        if($request->hasFile('file_sk')){

            //Storage::delete('/public/receipt_images/'.$user->receipt_image);

            // Get filename with the extension
            $filenameWithExt = $request->file('file_sk')->getClientOriginalName();
            //Get just filename
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            // Get just ext
            $extension = $request->file('file_sk')->getClientOriginalExtension();
            // Filename to store
            $fileNameToStore = $filename.'_'.time().'.'.$extension;
            // Upload Image
            $path = $request->file('file_sk')->storeAs('public/sk/'.$fields['service_disposition_id'],$fileNameToStore);

        }else{
            $msg = "Upload File SK!";
            return redirect('/trans-service-disposition-review/add/'.$fields['service_disposition_id'])->with('msg',$msg);
        }

        $servicedisposition = TransServiceDisposition::findOrFail($fields['service_disposition_id']);
        $servicedisposition->file_sk       = $fileNameToStore;
        $servicedisposition->review_status = 1;
        $servicedisposition->review_id     = Auth::id();
        $servicedisposition->review_at     = date('Y-m-d H:i:s');
        $servicedisposition->review_remark = $fields['review_remark'];

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

                $this->set_log(Auth::id(), $username['name'],'1089','Application.TransServiceDispositionReview.processAddTransServiceDispositionReview',$username['name'],'Add Trans Service Disposition Review');

                $servicerequisition->service_requisition_status = 4;
                if($servicerequisition->save()){  
                    $msg = "Review Disposisi Bantuan Berhasil";
                }else{
                    $msg = "Review Disposisi Bantuan Gagal";
                    return redirect('/trans-service-disposition-review/add/'.$fields['service_disposition_id'])->with('msg',$msg);
                }
            
                $disposition_data = TransServiceDisposition::findOrFail($fields['service_disposition_id']);

                $service_log = array(
                    'service_status'            => 4,
                    'service_requisition_no'    => $disposition_data['service_requisition_no'],
                    'section_id'                => $disposition_data['section_id'],
                    'created_id'                => Auth::id(),
                );
                TransServiceLog::create($service_log);
    
                $wa_msg = "Siabas\r\n\r\n\r\nNama : ".$disposition_data['service_requisition_name']."\r\n\r\nNomor Pengajuan : ".$disposition_data['service_requisition_no']."\r\n\r\nJenis Pengajuan : ".$this->getServiceName($disposition_data['service_id'])."\r\n\r\nBagian : ".$this->getSectionName($disposition_data['section_id'])."\r\n\r\nPesan : ".$this->getMessage(4);
                $wa_status = $this->getMessageStatus(4);
                $wa_no  = $disposition_data['service_requisition_phone'];
                $this->postWhatsappMessages($wa_msg, $wa_status, $wa_no);

                $msg = "Review Disposisi Bantuan Berhasil";
                return redirect('/trans-service-disposition-review')->with('msg',$msg);
            }else{
                $msg = "Review Disposisi Bantuan Gagal";
                return redirect('/trans-service-disposition-review/add/'.$fields['service_disposition_id'])->with('msg',$msg);
            }
        }else{
            $servicerequisition->service_requisition_status = 4;
            if($servicerequisition->save()){  
                $msg = "Review Disposisi Bantuan Berhasil";
            }else{
                $msg = "Review Disposisi Bantuan Gagal";
                return redirect('/trans-service-disposition-review/add/'.$fields['service_disposition_id'])->with('msg',$msg);
            }
            
            $disposition_data = TransServiceDisposition::findOrFail($fields['service_disposition_id']);

            $service_log = array(
                'service_status'            => 4,
                'service_requisition_no'    => $disposition_data['service_requisition_no'],
                'section_id'                => $disposition_data['section_id'],
                'created_id'                => Auth::id(),
            );
            TransServiceLog::create($service_log);

            $wa_msg = "Siabas\r\n\r\n\r\nNama : ".$disposition_data['service_requisition_name']."\r\n\r\nNomor Pengajuan : ".$disposition_data['service_requisition_no']."\r\n\r\nJenis Pengajuan : ".$this->getServiceName($disposition_data['service_id'])."\r\n\r\nBagian : ".$this->getSectionName($disposition_data['section_id'])."\r\n\r\nPesan : ".$this->getMessage(4);
            $wa_status = $this->getMessageStatus(4);
            $wa_no  = $disposition_data['service_requisition_phone'];
            $this->postWhatsappMessages($wa_msg, $wa_status, $wa_no);
            
            $msg = "Review Disposisi Bantuan Berhasil";
            return redirect('/trans-service-disposition-review')->with('msg',$msg);
        }
    }

    public function processDisapproveTransServiceDispositionReview(Request $request)
    {
        $fields = $request->validate([
            'service_disposition_id'                    => 'required',
            'disapprove_remark'                         => 'required',
            'service_disposition_review_token_edit'     => 'required',
        ]);
        
        
        $fileNameToStore = '';

        if($request->hasFile('file_sk')){

            //Storage::delete('/public/receipt_images/'.$user->receipt_image);

            // Get filename with the extension
            $filenameWithExt = $request->file('file_sk')->getClientOriginalName();
            //Get just filename
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            // Get just ext
            $extension = $request->file('file_sk')->getClientOriginalExtension();
            // Filename to store
            $fileNameToStore = $filename.'_'.time().'.'.$extension;
            // Upload Image
            $path = $request->file('file_sk')->storeAs('public/sk/'.$fields['service_disposition_id'],$fileNameToStore);

        }

        $servicedisposition = TransServiceDisposition::findOrFail($fields['service_disposition_id']);
        $servicedisposition->review_status     = 2;
        $servicedisposition->file_sk           = $fileNameToStore;
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
                    return redirect('/trans-service-disposition-review/add/'.$fields['service_disposition_id'])->with('msg',$msg);
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
                return redirect('/trans-service-disposition-review')->with('msg',$msg);
            }else{
                $msg = "Review Disposisi Bantuan Gagal";
                return redirect('/trans-service-disposition-review/add/'.$fields['service_disposition_id'])->with('msg',$msg);
            }
        }else{
            $servicerequisition->service_requisition_status = 5;
            if($servicerequisition->save()){  
                $msg = "Review Disposisi Bantuan Berhasil";
            }else{
                $msg = "Review Disposisi Bantuan Gagal";
                return redirect('/trans-service-disposition-review/add/'.$fields['service_disposition_id'])->with('msg',$msg);
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

    public function downloadTransServiceDispositionReviewFA($service_disposition_id){
        $dispositionsk = TransServiceDisposition::findOrFail($service_disposition_id);
        
        return response()->download(
            storage_path('app/public/fundsapplication/'.$dispositionsk['service_disposition_id'].'/'.$dispositionsk['file_funds_application']),
            $dispositionsk['file_funds_application'],
        );
    }

    public function downloadTransServiceDispositionReviewFO($service_disposition_id){
        $dispositionsk = TransServiceDisposition::findOrFail($service_disposition_id);
        
        return response()->download(
            storage_path('app/public/fundsorder/'.$dispositionsk['service_disposition_id'].'/'.$dispositionsk['file_funds_order']),
            $dispositionsk['file_funds_order'],
        );
    }
}
