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
use App\Models\CoreServiceGeneralPriority;
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

class TransServiceGeneralController extends Controller
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
        ->get();

        return view('content/TransServiceGeneral/ListTransServiceGeneral',compact('transservicegeneral', 'start_date', 'end_date'));
    }
    
    public function filter(Request $request){
        $start_date     = $request->start_date;
        $end_date       = $request->end_date;

        Session::put('start_date', $start_date);
        Session::put('end_date', $end_date);

        return redirect('/trans-service-general');
    }

    public function search()
    {
        $coreservice = CoreService::where('data_state', 0)
        ->get();

        return view('content/TransServiceRequisition/SearchCoreService',compact('coreservice'));
    }

    public function addReset()
    {
        Session::forget('sess_servicegeneraltoken');
        Session::forget('data_coreservice');
        Session::forget('data_coreserviceterm');
        Session::forget('data_coreserviceparameter');

        return redirect('/trans-service-requisition/add');
    }

    public function addTransServiceGeneral()
    {
        $service_general_token		    = Session::get('sess_servicegeneraltoken');

        if (empty($service_general_token)){
            $service_general_token = md5(date("YmdHis"));
            Session::put('sess_servicegeneraltoken', $service_general_token);
        }

        $service_general_token		= Session::get('sess_servicegeneraltoken');

        $coreservicegeneralparameter = CoreServiceGeneralParameter::where('data_state', 0)
        ->orderBy('service_general_parameter_no','ASC')
        ->get();

        $coreservicegeneralpriority = CoreServiceGeneralPriority::where('data_state', 0)
        ->pluck('service_general_priority_name', 'service_general_priority_id');

        return view('content/TransServiceGeneral/FormAddTransServiceGeneral',compact('coreservicegeneralparameter', 'service_general_token', 'coreservicegeneralpriority'));
    }

    public function detailTransServiceGeneral($service_general_id){
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

        return view('content/TransServiceGeneral/FormDetailTransServiceGeneral',compact('servicegeneral', 'servicegeneralparameter', 'service_general_id'));
    }

    public function deleteTransServiceGeneral($service_general_id){
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

        return view('content/TransServiceGeneral/FormDeleteTransServiceGeneral',compact('servicegeneral', 'servicegeneralparameter', 'service_general_id'));
    }

    public function editTransServiceGeneral($service_general_id){
        $service_general_token_edit		    = Session::get('sess_servicegeneraltokenedit');

        if (empty($service_general_token_edit)){
            $service_general_token_edit = md5(date("YmdHis"));
            Session::put('sess_servicegeneraltokenedit', $service_general_token_edit);
        }

        $service_general_token_edit		= Session::get('sess_servicegeneraltokenedit');

        $servicegeneral = TransServiceGeneral::findOrFail($service_general_id);

        $servicegeneralparameter = TransServiceGeneralParameter::select('trans_service_general_parameter.*', 'core_service_general_parameter.*')
        ->join('core_service_general_parameter', 'core_service_general_parameter.service_general_parameter_id', 'trans_service_general_parameter.general_parameter_id')
        ->where('trans_service_general_parameter.service_general_id', $service_general_id)
        ->where('trans_service_general_parameter.data_state', 0)
        ->get();

        $coreservicegeneralpriority = CoreServiceGeneralPriority::where('data_state', 0)
        ->pluck('service_general_priority_name', 'service_general_priority_id');

        return view('content/TransServiceGeneral/FormEditTransServiceGeneral',compact('servicegeneral', 'servicegeneralparameter', 'service_general_id', 'service_general_token_edit', 'coreservicegeneralpriority'));
    }

    public function documentRequisitionTransServiceRequisition($service_requisition_id){
        $service_requisition_token_edit		    = Session::get('sess_servicegeneraltokenedit');

        if (empty($service_requisition_token_edit)){
            $service_requisition_token_edit = md5(date("YmdHis"));
            Session::put('sess_servicegeneraltokenedit', $service_requisition_token_edit);
        }

        $service_requisition_token_edit		= Session::get('sess_servicegeneraltokenedit');

        $servicerequisition = TransServiceRequisition::findOrFail($service_requisition_id);

        $servicedocumentrequisition = TransServiceDocumentRequisition::select('trans_service_document_requisition.*', 'core_service_term.*')
        ->join('core_service_term', 'core_service_term.service_term_id', 'trans_service_document_requisition.service_term_id')
        ->where('service_requisition_id', $service_requisition_id)
        ->where('trans_service_document_requisition.data_state', 0)
        ->get();

        return view('content/TransServiceRequisition/FormDocumentRequisitionTransServiceRequisition',compact('servicerequisition', 'servicedocumentrequisition', 'service_requisition_id', 'service_requisition_token_edit'));
    }
    

    public function addElementsCoreService(Request $request)
    {
        $data_coreservice[$request->name] = $request->value;

        Session::put('data_coreservice', $data_coreservice);
        
        return redirect('/service/add');
    }

    public function processAddTransServiceGeneral(Request $request)
    {
        $fields = $request->validate([
            'service_general_agency'    => 'required',
            'service_general_phone'     => 'required',
            'service_general_token'     => 'required',
            'service_general_priority'  => 'required',
        ]);

        $coreservicegeneralparameter = CoreServiceGeneralParameter::where('data_state', 0)
        ->get();
        
        $allrequest = $request->all();
                    
        $fileNameToStore = '';

        if($request->hasFile('service_general_file')){

            //Storage::delete('/public/receipt_images/'.$user->receipt_image);

            // Get filename with the extension
            $filenameWithExt = $request->file('service_general_file')->getClientOriginalName();
            //Get just filename
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            // Get just ext
            $extension = $request->file('service_general_file')->getClientOriginalExtension();
            // Filename to store
            $fileNameToStore = $filename.'_'.time().'.'.$extension;
            // Upload Image
            $path = $request->file('service_general_file')->storeAs('public/service-general',$fileNameToStore);

        }else{
            $msg = "Upload Surat Masih Kosong";
            return redirect('/trans-service-general/add')->with('msg',$msg);
        }
        
        $data = array(
            'service_general_agency'    => $fields['service_general_agency'],
            'service_general_phone'     => $fields['service_general_phone'],
            'service_general_priority'  => $fields['service_general_priority'],
            'service_general_file'      => $fileNameToStore,
            'service_general_token'     => $fields['service_general_token'],
            'data_state' 				=> 0,
            'created_id' 				=> Auth::id(),
            'created_on' 				=> date('Y-m-d H:i:s'),
        );

        $service_general_token  = TransServiceGeneral::select('service_general_token')
            ->where('service_general_token', $data['service_general_token'])
            ->count();
        
        if($service_general_token == 0){
            if(TransServiceGeneral::create($data)){
                $username = User::select('name')->where('user_id','=',Auth::id())->first();

                $this->set_log(Auth::id(), $username['name'],'1089','Application.TransServiceGeneral.processAddTransServiceGeneral',$username['name'],'Add Trans Service General');

                $transservicegeneral_last 		= TransServiceGeneral::select('service_general_id', 'service_general_no')
                    ->where('created_id', $data['created_id'])
                    ->orderBy('service_general_id', 'DESC')
                    ->first();
                
                foreach($coreservicegeneralparameter as $key => $val){
                    $dataitem = array(
                        'service_general_id'			    => $transservicegeneral_last['service_general_id'],
                        'general_parameter_id'			    => $val['service_general_parameter_id'],
                        'service_general_parameter_value'	=> $allrequest['parameter_'.$val['service_general_parameter_id']],
                        'service_general_parameter_token'	=> $data['service_general_token'].$val['service_general_parameter_id']
                    );

                    $service_general_parameter_token = TransServiceGeneralParameter::select('service_general_parameter_token')
                        ->where('service_general_parameter_token', '=', $dataitem['service_general_parameter_token'])
                        ->count();

                    if(TransServiceGeneralParameter::create($dataitem)){	
                        $username = User::select('name')->where('user_id','=',Auth::id())->first();

                        $this->set_log(Auth::id(), $username['name'],'1089','Application.TransServiceGeneral.processAddTransServiceGeneral',$username['name'],'Add Trans Service General');

                        $msg = "Tambah Pengajuan Surat Umum Berhasil";
                        continue;
                    } else {
                        $msg = "Tambah Pengajuan Surat Umum Gagal";
                        return redirect('/trans-service-general/add')->with('msg',$msg);
                        break;
                    }
                }

                $wa_msg = "Siabas\r\n\r\n\r\nNama Instansi: ".$data['service_general_agency']."\r\n\r\nNomor Pengajuan : ".$transservicegeneral_last['service_general_no']."\r\n\r\nBagian : ".$this->getSectionName(1)."\r\n\r\nPesan : ".$this->getMessage(1);
                $wa_status = $this->getMessageStatus(1);
                $wa_no  = $data['service_general_phone'];
                $this->postWhatsappMessages($wa_msg, $wa_status, $wa_no);
                
                Session::forget('sess_servicegeneraltoken');
                return redirect('/trans-service-general')->with('msg',$msg);
            }else{
                $msg = "Tambah Pengajuan Surat Umum Gagal";
                return redirect('/trans-service-general/add')->with('msg',$msg);
            }
        } else {
            $username = User::select('name')->where('user_id','=',Auth::id())->first();

            $this->set_log(Auth::id(), $username['name'],'1089','Application.TransServiceGeneral.processAddTransServiceGeneral',$username['name'],'Add Trans Service General');

            $transservicegeneral_last 		= TransServiceGeneral::select('service_general_id', 'service_general_no')
                ->where('created_id', $data['created_id'])
                ->orderBy('service_general_id', 'DESC')
                ->first();

            $msg = "Tambah Pengajuan Surat Umum Berhasil";
            
            foreach($coreservicegeneralparameter as $key => $val){
                $dataitem = array(
                    'service_general_id'			    => $transservicegeneral_last['service_general_id'],
                    'general_parameter_id'			    => $val['service_general_parameter_id'],
                    'service_general_parameter_value'	=> $allrequest['parameter_'.$val['service_general_parameter_id']],
                    'service_general_parameter_token'	=> $data['service_general_token'].$val['service_general_parameter_id']
                );

                $service_general_parameter_token = TransServiceGeneralParameter::select('service_general_parameter_token')
                    ->where('service_general_parameter_token', '=', $dataitem['service_general_parameter_token'])
                    ->count();

                if($service_general_parameter_token == 0){
                    if(TransServiceGeneralParameter::create($dataitem)){	
                        $username = User::select('name')->where('user_id','=',Auth::id())->first();

                        $this->set_log(Auth::id(), $username['name'],'1089','Application.TransServiceGeneral.processAddTransServiceGeneral',$username['name'],'Add Trans Service General');

                        $msg = "Tambah Pengajuan Surat Umum Berhasil";
                        continue;
                    } else {
                        $msg = "Tambah Pengajuan Surat Umum Gagal";
                        return redirect('/trans-service-general/add')->with('msg',$msg);
                        break;
                    }
                }
            }

            $wa_msg = "Siabas\r\n\r\n\r\nNama Instansi: ".$data['service_general_agency']."\r\n\r\nNomor Pengajuan : ".$transservicegeneral_last['service_general_no']."\r\n\r\nBagian : ".$this->getSectionName(1)."\r\n\r\nPesan : ".$this->getMessage(1);
            $wa_status = $this->getMessageStatus(1);
            $wa_no  = $data['service_general_phone'];
            $this->postWhatsappMessages($wa_msg, $wa_status, $wa_no);
            
            Session::forget('sess_servicegeneraltoken');
            return redirect('/trans-service-general')->with('msg',$msg);
        }
    }
    

    public function processEditTransServiceGeneral(Request $request)
    {
        $fields = $request->validate([
            'service_general_id'            => 'required',
            'service_general_agency'        => 'required',
            'service_general_phone'         => 'required',
            'service_general_priority'      => 'required',
            'service_general_token_edit'    => 'required',
        ]);

        $coreservicegeneralparameter = CoreServiceGeneralParameter::where('data_state', 0)
        ->get();
        
        $allrequest = $request->all();
                    
        $fileNameToStore = '';

        if($request->hasFile('service_general_file')){

            //Storage::delete('/public/receipt_images/'.$user->receipt_image);

            // Get filename with the extension
            $filenameWithExt = $request->file('service_general_file')->getClientOriginalName();
            //Get just filename
            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
            // Get just ext
            $extension = $request->file('service_general_file')->getClientOriginalExtension();
            // Filename to store
            $fileNameToStore = $filename.'_'.time().'.'.$extension;
            // Upload Image
            $path = $request->file('service_general_file')->storeAs('public/service-general',$fileNameToStore);

        }

        $data = TransServiceGeneral::findOrFail($fields['service_general_id']);
        $data->service_general_agency       = $fields['service_general_agency'];
        $data->service_general_phone        = $fields['service_general_phone'];
        $data->service_general_priority     = $fields['service_general_priority'];
        $data->service_general_token_edit   = $fields['service_general_token_edit'];
        $data->updated_id                   = Auth::id();
        $data->updated_at                   = date('Y-m-d H:i:s');
        
        if($request->hasFile('service_general_file')){
            $data->service_general_file   = $fileNameToStore;
        }

        $service_general_token_edit  = TransServiceGeneral::select('service_general_token_edit')
            ->where('service_general_token_edit', $data['service_general_token_edit'])
            ->count();
        
        if($service_general_token_edit == 0){
            if($data->save()){
                $username = User::select('name')->where('user_id','=',Auth::id())->first();

                $this->set_log(Auth::id(), $username['name'],'1089','Application.TransServiceGeneral.processEditTransServiceGeneral',$username['name'],'Edit Trans Service General');
                
                foreach($coreservicegeneralparameter as $key => $val){
                    $transservicegeneralparameter = TransServiceGeneralParameter::where('service_general_id', $data['service_general_id'])
                    ->where('general_parameter_id', $val['service_general_parameter_id'])
                    ->first();

                    $transservicegeneralparameter->service_general_parameter_value = $allrequest['parameter_'.$val['service_general_parameter_id']];
                    $transservicegeneralparameter->updated_id = Auth::id();
                    $transservicegeneralparameter->updated_at = date('Y-m-d H:i:s');

                    if($transservicegeneralparameter->save()){	
                        $username = User::select('name')->where('user_id','=',Auth::id())->first();

                        $this->set_log(Auth::id(), $username['name'],'1089','Application.TransServiceGeneral.processEditTransServiceGeneral',$username['name'],'Edit Trans Service General');

                        $msg = "Edit Pengajuan Surat Umum Berhasil";
                        continue;
                    } else {
                        $msg = "Edit Pengajuan Surat Umum Gagal";
                        return redirect('/trans-service-general/edit/'.$fields['service_general_id'])->with('msg',$msg);
                        break;
                    }
                }
                Session::forget('sess_servicegeneraltokenedit');
                return redirect('/trans-service-general')->with('msg',$msg);
            }else{
                $msg = "Edit Pengajuan Surat Umum Gagal";
                return redirect('/trans-service-general/edit/'.$fields['service_general_id'])->with('msg',$msg);
            }
        }else{
            $username = User::select('name')->where('user_id','=',Auth::id())->first();

            $this->set_log(Auth::id(), $username['name'],'1089','Application.TransServiceGeneral.processEditTransServiceGeneral',$username['name'],'Edit Trans Service General');
            
            foreach($coreservicegeneralparameter as $key => $val){
                $transservicegeneralparameter = TransServiceGeneralParameter::where('service_general_id', $data['service_general_id'])
                ->where('general_parameter_id', $val['service_general_parameter_id'])
                ->first();

                $transservicegeneralparameter->service_general_parameter_value = $allrequest['parameter_'.$val['service_general_parameter_id']];
                $transservicegeneralparameter->updated_id = Auth::id();
                $transservicegeneralparameter->updated_at = date('Y-m-d H:i:s');

                if($transservicegeneralparameter->save()){	
                    $username = User::select('name')->where('user_id','=',Auth::id())->first();

                    $this->set_log(Auth::id(), $username['name'],'1089','Application.TransServiceGeneral.processEditTransServiceGeneral',$username['name'],'Edit Trans Service General');

                    $msg = "Edit Pengajuan Surat Umum Berhasil";
                    continue;
                } else {
                    $msg = "Edit Pengajuan Surat Umum Gagal";
                    return redirect('/trans-service-general/edit/'.$fields['service_general_id'])->with('msg',$msg);
                    break;
                }
            }
            Session::forget('sess_servicegeneraltokenedit');
            return redirect('/trans-service-general')->with('msg',$msg);
        }
    }

    public function processDeleteTransServiceGeneral(Request $request){   
        $fields = $request->validate([
            'service_general_id'=> 'required',
        ]);

        $servicegeneral = TransServiceGeneral::findOrFail($fields['service_general_id']);
        $servicegeneral->data_state = 1;
        $servicegeneral->deleted_id = Auth::id();
        $servicegeneral->deleted_at = date('Y-m-d H:i:s');
        $servicegeneral->delete_remark = $request->delete_remark;
        if($servicegeneral->save()){
            $msg = "Hapus Pengajuan Surat Umum Berhasil";
            return redirect('/trans-service-general')->with('msg',$msg);
        }else{
            $msg = "Hapus Pengajuan Surat Umum Tidak Berhasil";
            return redirect('/trans-service-general/delete/'.$fields['service_general_id'])->with('msg',$msg);
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

    public function getPriorityName($priority_id){
        $priority = CoreServiceGeneralPriority::where('data_state', 0)
        ->where('service_general_priority_id', $priority_id)
        ->first();

        return $priority['service_general_priority_name'];
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
        // print_r($service_requisition_term_id);
        // print_r($service_id);
    }
    
    
    public function print($service_general_id){
        $transservicegeneral = TransServiceGeneral::findOrFail($service_general_id);

        $coreservice = CoreService::where('service_id', $transservicegeneral['service_id'])
        ->where('data_state', 0)
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
        $date = date('d-m-Y', $datetime);
        $style = array(
            'align-item' => 'right',
        );
        $style2 = array(
            'align-item' => 'left',
        );
        // echo $barcode->getBarcodePNG('1', 'EAN13');exit;

        
        $path = public_path('resources/img/logosmart/logobaznas01.png');
        $export = "
        <br>
        <br>
        <section class='row'>
            <section class='col-md-6'>
                <div style=\"text-align:left; !important\">
                    <a style=\"color: green; text-decoration: none; font-size: 30px\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;BAZNAS</a>
                    <br/>
                    <a style=\"color: black; text-decoration: none; font-size: 15px\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Badan Amil Zakat Nasional</a>
                    <br/>
                    <a style=\"color: green; text-decoration: none; font-size: 18px\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Kota Surakarta</a>
                </div>
            </section>
        <br>
        </section>
        <hr style=\"width:100%; text-align:left !important; \"></hr>
        </br>
        <div style=\"text-align:center !important;\">
            <a style=\"color: black; text-decoration: none; font-size: 15px\">TANDA BUKTI PENGAJUAN SURAT UMUM</a>
            <br/>
            <a style=\"color: black; text-decoration: none; font-size: 12px\">".$transservicegeneral['service_general_no']."</a>
        </div>
        </br>
        <hr style=\"width:100%; text-align:left !important; \"></hr>
        </br>
        <div style=\"text-align:left;\">          
            <a style=\"color: black; text-decoration: none; font-size: 15px\">&nbsp;&nbsp;&nbsp;&nbsp;Dengan ini menjadi tanda bukti pengajuan bantuan kepada Badan Amil Zakat Nasional Kota Surakarta yang diajukan oleh : </a>
        </div>
        <div style=\"text-align:left;\">            
            <a style=\"color: black; text-decoration: none; font-size: 15px\">Nama &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: ".$transservicegeneral['service_general_agency']."</a>
            <br/>
            <a style=\"color: black; text-decoration: none; font-size: 15px\"> Tanggal &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: ".$date."</a>
            <br/>
            <a style=\"color: black; text-decoration: none; font-size: 15px\"> Nomor Whatsapp : ".$transservicegeneral['service_general_phone']."</a>
            <br/>
            <a style=\"color: black; text-decoration: none; font-size: 15px\"> Jenis Bantuan &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: SURAT UMUM</a>
        </div>
        <div style=\"text-align:left;\">          
            <a style=\"color: black; text-decoration: none; font-size: 15px\">&nbsp;&nbsp;&nbsp;&nbsp;Proses - proses sampai disetujuinya pengajuan bantuan ini akan disampaikan melalui pesan Whatsapp dengan nomor yang tertera diatas. Scan kode QR yang tertera di kantor kami (Komplek Masjid Bazis, Pilangsari, Kebayanan Jetis, Pilangsari, Kec. Surakarta, Kota Surakarta, Jawa Tengah 57252) untuk melihat track tahapan pengajuan bantuan anda.</a>
            <p style=\"color: black; text-decoration: none; font-size: 15px\">&nbsp;&nbsp;&nbsp;&nbsp;Terimakasih telah mengajukan bantuan kepada kami. Semoga pengajuan bantuan yang diberikan kepada Badan Amil Zakat Nasional Kota Surakarta dapat di proses dengan baik dan memenuhi persyaratan yang ada.</p>
        </div>
        <div style=\"text-align:left;\">          
            <a style=\"color: black; text-decoration: none; font-size: 15px\">Surakarta, ".$date."</a>
            <br/>
            <section style='position: absolute;'>
                <a style=\"color: black; text-decoration: none; font-size: 15px\">&nbsp;&nbsp;Penerima,</a>
                <a style=\"color: black; text-decoration: none; font-size: 15px\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Pengaju,</a>
            <section>
            <br/>
            <br/>
            <br/>
            <br/>
            <br/>
            <br/>
            <br/>
            <br/>
            <section style='position: absolute;'>
                <a style=\"color: black; text-decoration: none; font-size: 15px\">&nbsp;&nbsp;..........................................</a>
                <a style=\"color: black; text-decoration: none; font-size: 15px\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;..........................................</a>
            <section>
        </div>
        ";

        $pdf::writeHTML($export, true, false, false, false, '');
        $pdf::write2DBarcode($transservicegeneral['service_general_no'], 'QRCODE,H', 167, 5, 40, 40, $style, 'N');
        $pdf::Image( $path, 7, 4, 38, 38, 'PNG', '', 'LT', false, 300, '', false, false, 1, false, false, false);

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
