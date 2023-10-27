<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\CoreKecamatan;
use App\Models\CoreKelurahan;
use App\Models\CoreService;
use App\Models\CoreSection;
use App\Models\CoreServiceTerm;
use App\Models\CoreServiceParameter;
use App\Models\TransServiceDocumentRequisition;
use App\Models\TransServiceLog;
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

class TransServiceRequisitionController extends Controller
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

        $transservicerequisition = TransServiceRequisition::where('data_state', 0)
        ->where('created_at','>=',$start_date)
        ->where('created_at','<=',$stop_date)
        ->get();

        return view('content/TransServiceRequisition/ListTransServiceRequisition',compact('transservicerequisition', 'start_date', 'end_date'));
    }

    public function filter(Request $request){
        $start_date     = $request->start_date;
        $end_date       = $request->end_date;

        Session::put('start_date', $start_date);
        Session::put('end_date', $end_date);

        return redirect('/trans-service-requisition');
    }

    public function search()
    {
        $coreservice = CoreService::where('data_state', 0)
        ->get();

        return view('content/TransServiceRequisition/SearchCoreService',compact('coreservice'));
    }

    public function addReset()
    {
        Session::forget('sess_servicerequisitiontoken');
        Session::forget('data_coreservice');
        Session::forget('data_coreserviceterm');
        Session::forget('data_coreserviceparameter');

        return redirect('/trans-service-requisition/add');
    }

    public function addTransServiceRequisition($service_id)
    {
        $service_requisition_token		    = Session::get('sess_servicerequisitiontoken');

        if (empty($service_requisition_token)){
            $service_requisition_token = md5(date("YmdHis"));
            Session::put('sess_servicerequisitiontoken', $service_requisition_token);
        }

        $service_requisition_token		= Session::get('sess_servicerequisitiontoken');

        $corekecamatan = CoreKecamatan::where('data_state', 0)
        ->pluck('kecamatan_name', 'kecamatan_id');

        $corekelurahan = array();

        $coreservice = CoreService::where('data_state', 0)
        ->where('service_id', $service_id)
        ->first();

        $coreserviceparameter = CoreServiceParameter::where('data_state', 0)
        ->where('service_id', $service_id)
        ->orderBy('service_parameter_no', 'ASC')
        ->get();

        $coreserviceterm = CoreServiceTerm::where('data_state', 0)
        ->where('service_id', $service_id)
        ->orderBy('service_term_no', 'ASC')
        ->get();

        return view('content/TransServiceRequisition/FormAddTransServiceRequisition',compact('corekecamatan', 'corekelurahan', 'coreservice', 'coreserviceparameter', 'coreserviceterm', 'service_requisition_token'));
    }

    public function detailTransServiceRequisition($service_requisition_id){
        $servicerequisition = TransServiceRequisition::findOrFail($service_requisition_id);

        $servicerequisitionterm = TransServiceRequisitionTerm::select('trans_service_requisition_term.*', 'core_service_term.*')
        ->join('core_service_term', 'core_service_term.service_term_id', 'trans_service_requisition_term.service_term_id')
        ->where('service_requisition_id', $service_requisition_id)
        ->where('trans_service_requisition_term.data_state', 0)
        ->get();

        $servicerequisitionparameter = TransServiceRequisitionParameter::select('trans_service_requisition_parameter.*', 'core_service_parameter.*')
        ->join('core_service_parameter', 'core_service_parameter.service_parameter_id', 'trans_service_requisition_parameter.service_parameter_id')
        ->where('service_requisition_id', $service_requisition_id)
        ->where('trans_service_requisition_parameter.data_state', 0)
        ->get();

        $corekecamatan = CoreKecamatan::where('data_state', 0)
        ->pluck('kecamatan_name', 'kecamatan_id');

        $corekelurahan = CoreKelurahan::where('data_state', 0)
        ->pluck('kelurahan_name', 'kelurahan_id');

        return view('content/TransServiceRequisition/FormDetailTransServiceRequisition',compact('servicerequisition', 'servicerequisitionterm', 'servicerequisitionparameter', 'service_requisition_id', 'corekecamatan', 'corekelurahan'));
    }

    public function deleteTransServiceRequisition($service_requisition_id){
        $servicerequisition = TransServiceRequisition::findOrFail($service_requisition_id);

        $servicerequisitionterm = TransServiceRequisitionTerm::select('trans_service_requisition_term.*', 'core_service_term.*')
        ->join('core_service_term', 'core_service_term.service_term_id', 'trans_service_requisition_term.service_term_id')
        ->where('service_requisition_id', $service_requisition_id)
        ->where('trans_service_requisition_term.data_state', 0)
        ->get();

        $servicerequisitionparameter = TransServiceRequisitionParameter::select('trans_service_requisition_parameter.*', 'core_service_parameter.*')
        ->join('core_service_parameter', 'core_service_parameter.service_parameter_id', 'trans_service_requisition_parameter.service_parameter_id')
        ->where('service_requisition_id', $service_requisition_id)
        ->where('trans_service_requisition_parameter.data_state', 0)
        ->get();

        $corekecamatan = CoreKecamatan::where('data_state', 0)
        ->pluck('kecamatan_name', 'kecamatan_id');

        return view('content/TransServiceRequisition/FormDeleteTransServiceRequisition',compact('servicerequisition', 'servicerequisitionterm', 'servicerequisitionparameter', 'service_requisition_id', 'corekecamatan'));
    }

    public function editTransServiceRequisition($service_requisition_id){
        $service_requisition_token_edit		    = Session::get('sess_servicerequisitiontokenedit');

        if (empty($service_requisition_token_edit)){
            $service_requisition_token_edit = md5(date("YmdHis"));
            Session::put('sess_servicerequisitiontokenedit', $service_requisition_token_edit);
        }

        $service_requisition_token_edit		= Session::get('sess_servicerequisitiontokenedit');

        $servicerequisition = TransServiceRequisition::findOrFail($service_requisition_id);

        $servicerequisitionterm = TransServiceRequisitionTerm::select('trans_service_requisition_term.*', 'core_service_term.*')
        ->join('core_service_term', 'core_service_term.service_term_id', 'trans_service_requisition_term.service_term_id')
        ->where('service_requisition_id', $service_requisition_id)
        ->where('trans_service_requisition_term.data_state', 0)
        ->get();

        $servicerequisitionparameter = TransServiceRequisitionParameter::select('trans_service_requisition_parameter.*', 'core_service_parameter.*')
        ->join('core_service_parameter', 'core_service_parameter.service_parameter_id', 'trans_service_requisition_parameter.service_parameter_id')
        ->where('service_requisition_id', $service_requisition_id)
        ->where('trans_service_requisition_parameter.data_state', 0)
        ->get();

        $corekecamatan = CoreKecamatan::where('data_state', 0)
        ->pluck('kecamatan_name', 'kecamatan_id');

        $corekelurahan = CoreKelurahan::where('data_state', 0)
        ->where('kecamatan_id', $servicerequisition['kecamatan_id'])
        ->pluck('kelurahan_name', 'kelurahan_id');

        return view('content/TransServiceRequisition/FormEditTransServiceRequisition',compact('servicerequisition', 'servicerequisitionterm', 'servicerequisitionparameter', 'service_requisition_id', 'service_requisition_token_edit', 'corekecamatan', 'corekelurahan'));
    }

    public function documentRequisitionTransServiceRequisition($service_requisition_id){
        $service_requisition_token_edit		= Session::get('sess_servicerequisitiontokenedit');

        if (empty($service_requisition_token_edit)){
            $service_requisition_token_edit = md5(date("YmdHis"));
            Session::put('sess_servicerequisitiontokenedit', $service_requisition_token_edit);
        }

        $service_requisition_token_edit		= Session::get('sess_servicerequisitiontokenedit');

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

    public function processAddTransServiceRequisition(Request $request)
    {
        $fields = $request->validate([
            'service_id'                => 'required',
            'service_requisition_name'  => 'required',
            'service_requisition_phone' => 'required',
            'service_requisition_token' => 'required',
        ]);

        $coreserviceterm = CoreServiceTerm::where('data_state', 0)
        ->where('service_id', $fields['service_id'])
        ->get();

        $coreserviceparameter = CoreServiceParameter::where('data_state', 0)
        ->where('service_id', $fields['service_id'])
        ->get();
        
        $allrequest = $request->all();
        
        $data = array(
            'service_id'		            => $fields['service_id'],
            'kecamatan_id'		            => $request->kecamatan_id,
            'kelurahan_id'		            => $request->kelurahan_id,
            'service_requisition_nik'	    => $request->service_requisition_nik,
            'service_requisition_address'	=> $request->service_requisition_address,
            'service_requisition_name'      => $fields['service_requisition_name'],
            'service_requisition_phone'     => $fields['service_requisition_phone'],
            'service_requisition_token'     => $fields['service_requisition_token'],
            'data_state' 				    => 0,
            'created_id' 				    => Auth::id(),
            'created_on' 				    => date('Y-m-d H:i:s'),
        );

        $service_requisition_token  = TransServiceRequisition::select('service_requisition_token')
        ->where('service_requisition_token', $data['service_requisition_token'])
        ->count();
        
        if($service_requisition_token == 0){
            if(TransServiceRequisition::create($data)){
                $username = User::select('name')->where('user_id','=',Auth::id())->first();

                $this->set_log(Auth::id(), $username['name'],'1089','Application.TransServiceRequisition.processAddTransServiceRequisition',$username['name'],'Add Trans Service Requisition');

                $transservicerequisition_last 		= TransServiceRequisition::select('service_requisition_id', 'service_requisition_no')
                ->where('created_id', $data['created_id'])
                ->orderBy('service_requisition_id', 'DESC')
                ->first();
                    
                foreach($coreserviceterm as $key => $val){
                    $fileNameToStore = '';

                    if($request->hasFile('file_'.$val['service_term_id'])){

                        //Storage::delete('/public/receipt_images/'.$user->receipt_image);

                        // Get filename with the extension
                        $filenameWithExt = $request->file('file_'.$val['service_term_id'])->getClientOriginalName();
                        //Get just filename
                        $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                        // Get just ext
                        $extension = $request->file('file_'.$val['service_term_id'])->getClientOriginalExtension();
                        // Filename to store
                        $fileNameToStore = $filename.'_'.time().'.'.$extension;
                        // Upload Image
                        $path = $request->file('file_'.$val['service_term_id'])->storeAs('public/term/'.$fields['service_id'],$fileNameToStore);

                    }
                    if(isset($allrequest['checkbox_'.$val['service_term_id']])){
                        $checkbox = $allrequest['checkbox_'.$val['service_term_id']];
                    }else{
                        $checkbox = 0;
                    }
                    $dataitem = array(
                        'service_requisition_id'			=> $transservicerequisition_last['service_requisition_id'],
                        'service_term_id'			        => $val['service_term_id'],
                        'service_requisition_term_value'	=> $fileNameToStore,
                        'service_requisition_term_status'	=> $checkbox,
                        'service_requisition_term_token'	=> $data['service_requisition_token'].$val['service_term_id']
                    );

                    $service_requisition_term_token = TransServiceRequisitionTerm::select('service_requisition_term_token')
                    ->where('service_requisition_term_token', '=', $dataitem['service_requisition_term_token'])
                    ->count();

                    if($service_requisition_term_token == 0){
                        if(TransServiceRequisitionTerm::create($dataitem)){	
                            $username = User::select('name')->where('user_id','=',Auth::id())->first();

                            $this->set_log(Auth::id(), $username['name'],'1089','Application.TransServiceRequisition.processAddTransServiceRequisition',$username['name'],'Add Trans Service Requisition');

                            $msg = "Tambah Pengajuan Bantuan Berhasil";
                            continue;
                        } else {
                            $msg = "Tambah Pengajuan Bantuan Gagal";
                            return redirect('/trans-service-requisition/add/'.$fields['service_id'])->with('msg',$msg);
                            break;
                        }
                    }
                }
                
                foreach($coreserviceparameter as $key => $val){
                    $dataitem = array(
                        'service_requisition_id'			    => $transservicerequisition_last['service_requisition_id'],
                        'service_parameter_id'			        => $val['service_parameter_id'],
                        'service_requisition_parameter_value'	=> $allrequest['parameter_'.$val['service_parameter_id']],
                        'service_requisition_parameter_token'	=> $data['service_requisition_token'].$val['service_parameter_id']
                    );

                    $service_requisition_parameter_token = TransServiceRequisitionParameter::select('service_requisition_parameter_token')
                    ->where('service_requisition_parameter_token', '=', $dataitem['service_requisition_parameter_token'])
                    ->count();

                    if($service_requisition_parameter_token == 0){
                        if(TransServiceRequisitionParameter::create($dataitem)){	
                            $username = User::select('name')->where('user_id','=',Auth::id())->first();

                            $this->set_log(Auth::id(), $username['name'],'1089','Application.TransServiceRequisition.processAddTransServiceRequisition',$username['name'],'Add Trans Service Requisition');

                            $msg = "Tambah Pengajuan Bantuan Berhasil";
                            continue;
                        } else {
                            $msg = "Tambah Pengajuan Bantuan Gagal";
                            return redirect('/trans-service-requisition/add/'.$fields['service_id'])->with('msg',$msg);
                            break;
                        }
                    }
                }

                $service_log = array(
                    'service_status'            => 1,
                    'service_requisition_no'    => $transservicerequisition_last['service_requisition_no'],
                    'section_id'                => 1,
                    'created_id'                => Auth::id(),
                );
                TransServiceLog::create($service_log);

                $wa_msg = "Siabas\r\n\r\n\r\nNama : ".$data['service_requisition_name']."\r\n\r\nNomor Pengajuan : ".$transservicerequisition_last['service_requisition_no']."\r\n\r\nJenis Pengajuan : ".$this->getServiceName($data['service_id'])."\r\n\r\nBagian : ".$this->getSectionName(1)."\r\n\r\nPesan : ".$this->getMessage(1);
                $wa_status = $this->getMessageStatus(1);
                $wa_no  = $data['service_requisition_phone'];
                $this->postWhatsappMessages($wa_msg, $wa_status, $wa_no);
                
                Session::forget('sess_servicerequisitiontoken');
                return redirect('/trans-service-requisition')->with('msg',$msg);
            }else{
                $msg = "Tambah Pengajuan Bantuan Gagal";
                return redirect('/trans-service-requisition/add/'.$fields['service_id'])->with('msg',$msg);
            }
        } else {
            $username = User::select('name')->where('user_id','=',Auth::id())->first();

            $this->set_log(Auth::id(), $username['name'],'1089','Application.CoreService.processAddCoreService',$username['name'],'Add Trans Service Requisition');

            $transservicerequisition_last   = TransServiceRequisition::select('service_requisition_id', 'service_requisition_no')
            ->where('created_id', '=', $data['created_id'])
            ->orderBy('service_requisition_id', 'DESC')
            ->first();
            
            $msg = "Tambah Pengajuan Bantuan Berhasil";
                
            foreach($coreserviceterm as $key => $val){
                $fileNameToStore = '';

                if($request->hasFile('file_'.$val['service_term_id'])){

                    //Storage::delete('/public/receipt_images/'.$user->receipt_image);

                    // Get filename with the extension
                    $filenameWithExt = $request->file('file_'.$val['service_term_id'])->getClientOriginalName();
                    //Get just filename
                    $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                    // Get just ext
                    $extension = $request->file('file_'.$val['service_term_id'])->getClientOriginalExtension();
                    // Filename to store
                    $fileNameToStore = $filename.'_'.time().'.'.$extension;
                    // Upload Image
                    $path = $request->file('file_'.$val['service_term_id'])->storeAs('public/term/'.$fields['service_id'],$fileNameToStore);

                }
                if(isset($allrequest['checkbox_'.$val['service_term_id']])){
                    $checkbox = $allrequest['checkbox_'.$val['service_term_id']];
                }else{
                    $checkbox = 0;
                }
                $dataitem = array(
                    'service_requisition_id'	        => $transservicerequisition_last['service_requisition_id'],
                    'service_term_id'	                => $val['service_term_id'],
                    'service_requisition_term_value'	=> $fileNameToStore,
                    'service_requisition_term_status'	=> $checkbox,
                    'service_requisition_term_token'	=> $data['service_requisition_token'].$val['service_term_id']
                );

                $service_requisition_term_token = TransServiceRequisitionTerm::select('service_requisition_term_token')
                    ->where('service_requisition_term_token', '=', $dataitem['service_requisition_term_token'])
                    ->count();

                if($service_requisition_term_token == 0){
                    if(TransServiceRequisitionTerm::create($dataitem)){	
                        $msg = "Tambah Pengajuan Bantuan Berhasil";
                        continue;
                    } else {
                        $msg = "Tambah Pengajuan Bantuan Tidak Berhasil";
                        return redirect('/trans-service-requisition/add/'.$fields['service_id'])->with('msg',$msg);
                        break;
                    }
                }	
            }
            
            foreach($coreserviceparameter as $key => $val){
                $dataitem = array(
                    'service_requisition_id'		        => $transservicerequisition_last['service_requisition_id'],
                    'service_parameter_id'	                => $val['service_parameter_id'],
                    'service_requisition_parameter_value'	=> $allrequest['parameter_'.$val['service_parameter_id']],
                    'service_requisition_parameter_token'	=> $data['service_requisition_token'].$val['service_parameter_id']
                );

                $service_requisition_parameter_token = TransServiceRequisitionParameter::select('service_requisition_parameter_token')
                    ->where('service_requisition_parameter_token', '=', $dataitem['service_requisition_parameter_token'])
                    ->count();

                if($service_requisition_parameter_token == 0){
                    if(TransServiceRequisitionParameter::create($dataitem)){	

                        $msg = "Tambah Pengajuan Bantuan Berhasil";
                        continue;
                    } else {
                        $msg = "Tambah Pengajuan Bantuan Tidak Berhasil";
                        return redirect('/trans-service-requisition/add/'.$fields['service_id'])->with('msg',$msg);
                        break;
                    }
                }	
            }

            $service_log = array(
                'service_status'            => 1,
                'service_requisition_no'    => $transservicerequisition_last['service_requisition_no'],
                'section_id'                => 1,
                'created_id'                => Auth::id(),
            );
            TransServiceLog::create($service_log);
            
            $wa_msg = "Siabas\r\n\r\n\r\nNama : ".$data['service_requisition_name']."\r\n\r\nNomor Pengajuan : ".$transservicerequisition_last['service_requisition_no']."\r\n\r\nJenis Pengajuan : ".$this->getServiceName($data['service_id'])."\r\n\r\nBagian : ".$this->getSectionName(1)."\r\n\r\nPesan : ".$this->getMessage(1);
            $wa_status = $this->getMessageStatus(1);
            $wa_no  = $data['service_requisition_phone'];
            $this->postWhatsappMessages($wa_msg, $wa_status, $wa_no);

            Session::forget('sess_servicerequisitiontoken');
            return redirect('/trans-service-requisition')->with('msg',$msg);
        }
    }

    public function processEditTransServiceRequisition(Request $request)
    {
        $fields = $request->validate([
            'service_id'                        => 'required',
            'service_requisition_id'            => 'required',
            'service_requisition_name'          => 'required',
            'service_requisition_phone'         => 'required',
            'service_requisition_token_edit'    => 'required',
        ]);

        $coreserviceterm = CoreServiceTerm::where('data_state', 0)
        ->where('service_id', $fields['service_id'])
        ->get();

        $coreserviceparameter = CoreServiceParameter::where('data_state', 0)
        ->where('service_id', $fields['service_id'])
        ->get();
        
        $allrequest = $request->all();

        $data = TransServiceRequisition::findOrFail($fields['service_requisition_id']);
        $data->service_requisition_name         = $fields['service_requisition_name'];
        $data->service_requisition_phone        = $fields['service_requisition_phone'];
        $data->service_requisition_token_edit   = $fields['service_requisition_token_edit'];
        $data->service_requisition_nik          = $request->service_requisition_nik;
        $data->service_requisition_address      = $request->service_requisition_address;
        $data->kecamatan_id                     = $request->kecamatan_id;
        $data->kelurahan_id                     = $request->kelurahan_id;
        $data->updated_id                       = Auth::id();
        $data->updated_at                       = date('Y-m-d H:i:s');

        $service_requisition_token_edit  = TransServiceRequisition::select('service_requisition_token_edit')
            ->where('service_requisition_token_edit', $data['service_requisition_token_edit'])
            ->count();
        
        if($data->save()){
            $username = User::select('name')->where('user_id','=',Auth::id())->first();

            $this->set_log(Auth::id(), $username['name'],'1089','Application.TransServiceRequisition.processEditTransServiceRequisition',$username['name'],'Edit Trans Service Requisition');
                
            foreach($coreserviceterm as $key => $val){
                $transservicerequisitionterm = TransServiceRequisitionTerm::where('service_requisition_id',$data['service_requisition_id'])
                ->where('service_term_id', $val['service_term_id'])
                ->first();
                
                if(isset($allrequest['checkbox_'.$val['service_term_id']])){
                    $checkbox = $allrequest['checkbox_'.$val['service_term_id']];
                }else{
                    $checkbox = 0;
                }

                $fileNameToStore = '';

                if($request->hasFile('file_'.$val['service_term_id'])){

                    //Storage::delete('/public/receipt_images/'.$user->receipt_image);

                    // Get filename with the extension
                    $filenameWithExt = $request->file('file_'.$val['service_term_id'])->getClientOriginalName();
                    //Get just filename
                    $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                    // Get just ext
                    $extension = $request->file('file_'.$val['service_term_id'])->getClientOriginalExtension();
                    // Filename to store
                    $fileNameToStore = $filename.'_'.time().'.'.$extension;
                    // Upload Image
                    $path = $request->file('file_'.$val['service_term_id'])->storeAs('public/term/'.$fields['service_id'],$fileNameToStore);

                    $transservicerequisitionterm->service_requisition_term_value    = $fileNameToStore;
                    $transservicerequisitionterm->service_requisition_term_status   = $checkbox;
                    $transservicerequisitionterm->updated_id                        = Auth::id();
                    $transservicerequisitionterm->updated_at                        = date('Y-m-d H:i:s');
                    
                    if($transservicerequisitionterm->save()){	
                        $username = User::select('name')->where('user_id','=',Auth::id())->first();

                        $this->set_log(Auth::id(), $username['name'],'1089','Application.TransServiceRequisition.processEditTransServiceRequisition',$username['name'],'Edit Trans Service Requisition');

                        $msg = "Edit Pengajuan Bantuan Berhasil";
                        continue;
                    } else {
                        $msg = "Edit Pengajuan Bantuan Gagal";
                        return redirect('/trans-service-requisition/edit/'.$fields['service_requisition_id'])->with('msg',$msg);
                        break;
                    }
                }else{

                    $transservicerequisitionterm->service_requisition_term_status   = $checkbox;
                    $transservicerequisitionterm->updated_id                        = Auth::id();
                    $transservicerequisitionterm->updated_at                        = date('Y-m-d H:i:s');
                    
                    if($transservicerequisitionterm->save()){	
                        $username = User::select('name')->where('user_id','=',Auth::id())->first();

                        $this->set_log(Auth::id(), $username['name'],'1089','Application.TransServiceRequisition.processEditTransServiceRequisition',$username['name'],'Edit Trans Service Requisition');

                        $msg = "Edit Pengajuan Bantuan Berhasil";
                        continue;
                    } else {
                        $msg = "Edit Pengajuan Bantuan Gagal";
                        return redirect('/trans-service-requisition/edit/'.$fields['service_requisition_id'])->with('msg',$msg);
                        break;
                    }
                }
            }
            
            foreach($coreserviceparameter as $key => $val){
                $transservicerequisitionparameter = TransServiceRequisitionParameter::where('service_requisition_id', $data['service_requisition_id'])
                ->where('service_parameter_id', $val['service_parameter_id'])
                ->first();

                $transservicerequisitionparameter->service_requisition_parameter_value = $allrequest['parameter_'.$val['service_parameter_id']];
                $transservicerequisitionparameter->updated_id = Auth::id();
                $transservicerequisitionparameter->updated_at = date('Y-m-d H:i:s');

                if($transservicerequisitionparameter->save()){	
                    $username = User::select('name')->where('user_id','=',Auth::id())->first();

                    $this->set_log(Auth::id(), $username['name'],'1089','Application.TransServiceRequisition.processEditTransServiceRequisition',$username['name'],'Edit Trans Service Requisition');

                    $msg = "Edit Pengajuan Bantuan Berhasil";
                    continue;
                } else {
                    $msg = "Edit Pengajuan Bantuan Gagal";
                    return redirect('/trans-service-requisition/edit/'.$fields['service_requisition_id'])->with('msg',$msg);
                    break;
                }
            }
            Session::forget('sess_servicerequisitiontokenedit');
            return redirect('/trans-service-requisition')->with('msg',$msg);
        }else{
            $msg = "Edit Pengajuan Bantuan Gagal";
            return redirect('/trans-service-requisition/edit/'.$fields['service_requisition_id'])->with('msg',$msg);
        }
    }

    public function processDeleteTransServiceRequisition(Request $request){   
        $fields = $request->validate([
            'service_requisition_id'=> 'required',
        ]);

        $servicerequisition = TransServiceRequisition::findOrFail($fields['service_requisition_id']);
        $servicerequisition->data_state = 1;
        $servicerequisition->deleted_id = Auth::id();
        $servicerequisition->deleted_at = date('Y-m-d H:i:s');
        $servicerequisition->delete_remark = $request->delete_remark;
        if($servicerequisition->save()){
            $msg = "Hapus Pengajuan Bantuan Berhasil";
            return redirect('/trans-service-requisition')->with('msg',$msg);
        }else{
            $msg = "Hapus Pengajuan Bantuan Tidak Berhasil";
            return redirect('/trans-service-requisition/delete/'.$fields['service_requisition_id'])->with('msg',$msg);
        }
    }

    public function editCoreService($service_id)
    {
        $service_token_edit		= Session::get('sess_servicerequisitiontoken');

        if (empty($service_token_edit)){
            $service_token_edit = md5(date("YmdHis"));
            Session::put('sess_servicerequisitiontoken', $service_token_edit);
        }

        $service_token_edit		    = Session::get('sess_servicerequisitiontoken');

        $coreservice                = CoreService::where('service_id', $service_id)->first();

        $data_coreserviceterm_first = Session::get('data_coreserviceterm_first');

        if ($data_coreserviceterm_first == null){
            $data_coreserviceterm_first     = [];
            
            $coreserviceterm    = CoreServiceTerm::select('service_term_id', 'service_term_no', 'service_term_description')
                ->where('service_id', '=', $service_id)
                ->where('data_state', '=', 0)
                ->get();

            foreach ($coreserviceterm  as $key => $val) {
                $record_id 						= $val['service_term_id'];

                $data_item = array(
                    'record_id'							=> $val['service_term_id'],
                    'service_term_no'	        		=> $val['service_term_no'],
                    'service_term_description' 			=> $val['service_term_description'],
                    'item_status'						=> 9,
                );

                array_push($data_coreserviceterm_first, $data_item);
                Session::push('data_coreserviceterm_first', $data_item);

                $data_coreserviceterm = Session::get('data_coreserviceterm');
                if($data_coreserviceterm !== null){
                    array_push($data_coreserviceterm, $data_item);
                    Session::put('data_coreserviceterm', $data_coreserviceterm);
                }else{
                    $data_coreserviceterm = [];
                    array_push($data_coreserviceterm, $data_item);
                    Session::push('data_coreserviceterm', $data_item);
                }
            }
        }

        $data_coreserviceparameter_first = Session::get('data_coreserviceparameter_first');

        if ($data_coreserviceparameter_first == null){
            $data_coreserviceparameter_first     = [];
            
            $coreserviceparameter    = CoreServiceParameter::select('service_parameter_id', 'service_parameter_no', 'service_parameter_description')
                ->where('service_id', '=', $service_id)
                ->where('data_state', '=', 0)
                ->get();

            foreach ($coreserviceparameter  as $key => $val) {
                $record_id 						= $val['service_parameter_id'];

                $data_item = array(
                    'record_id'							=> $val['service_parameter_id'],
                    'service_parameter_no'	        	=> $val['service_parameter_no'],
                    'service_parameter_description' 	=> $val['service_parameter_description'],
                    'item_status'						=> 9,
                );

                array_push($data_coreserviceparameter_first, $data_item);
                Session::push('data_coreserviceparameter_first', $data_item);

                $data_coreserviceparameter = Session::get('data_coreserviceparameter');
                if($data_coreserviceparameter !== null){
                    array_push($data_coreserviceparameter, $data_item);
                    Session::put('data_coreserviceparameter', $data_coreserviceparameter);
                }else{
                    $data_coreserviceparameter = [];
                    array_push($data_coreserviceparameter, $data_item);
                    Session::push('data_coreserviceparameter', $data_item);
                }
            }
        }

        $coreserviceterm        = Session::get('data_coreserviceterm');
        $coreserviceparameter   = Session::get('data_coreserviceparameter');

        return view('content/CoreService/FormEditCoreService',compact('coreservice', 'coreserviceterm', 'service_token_edit', 'coreserviceparameter'));
    }

    public function getCoreKelurahan(Request $request){
        $kecamatan_id = $request->kecamatan_id;
        $data='';

        $corekelurahan = CoreKelurahan::where('kecamatan_id', $kecamatan_id)
        ->where('data_state', 0)
        ->get();

        $data .= "<option value=''>--Choose One--</option>";
        foreach ($corekelurahan as $mp){
            $data .= "<option value='$mp[kelurahan_id]'>$mp[kelurahan_name]</option>\n";	
        }

        return $data;
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

    public function processDocumentRequisitionTransServiceRequisition(Request $request)
    {
        $fields = $request->validate([
            'service_id'                      => 'required',
            'service_requisition_id'          => 'required',
            'service_requisition_token_edit'  => 'required',
        ]);
        
        $allrequest = $request->all();

        $servicedocumentrequisition = TransServiceDocumentRequisition::where('data_state', 0)
        ->get();

        $servicerequisition = TransServiceRequisition::findOrFail($fields['service_requisition_id']);
        $servicerequisition->service_requisition_status = 0;

        $servicerequisitiontokenedit = TransServiceRequisition::where('data_state', 0)
        ->where('service_requisition_token_edit', $fields['service_requisition_token_edit'])
        ->count();

        if($servicerequisitiontokenedit == 0){
            if($servicerequisition->save()){
                foreach($servicedocumentrequisition as $key => $val){
                    $documentrequisition =TransServiceDocumentRequisition::where('service_document_requisition_id', $val['service_document_requisition_id'])
                    ->first();

                    $documentrequisition->data_state = 1;
                    if($documentrequisition->save()){
                        
                        if(isset($allrequest['checkbox_'.$val['service_term_id']])){
                            $checkbox = $allrequest['checkbox_'.$val['service_term_id']];
                        }else{
                            $checkbox = 0;
                        }

                        $servicerequisitionterm = TransServiceRequisitionTerm::where('data_state', 0)
                        ->where('service_requisition_id', $fields['service_requisition_id'])
                        ->where('service_term_id', $val['service_term_id'])
                        ->first();

                        $fileNameToStore = '';

                        if($request->hasFile('file_'.$val['service_term_id'])){

                            //Storage::delete('/public/receipt_images/'.$user->receipt_image);

                            // Get filename with the extension
                            $filenameWithExt = $request->file('file_'.$val['service_term_id'])->getClientOriginalName();
                            //Get just filename
                            $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                            // Get just ext
                            $extension = $request->file('file_'.$val['service_term_id'])->getClientOriginalExtension();
                            // Filename to store
                            $fileNameToStore = $filename.'_'.time().'.'.$extension;
                            // Upload Image
                            $path = $request->file('file_'.$val['service_term_id'])->storeAs('public/term/'.$fields['service_id'],$fileNameToStore);

                            $servicerequisitionterm->service_requisition_term_value    = $fileNameToStore;
                            $servicerequisitionterm->service_requisition_term_status   = $checkbox;
                            $servicerequisitionterm->updated_id                        = Auth::id();
                            $servicerequisitionterm->updated_at                        = date('Y-m-d H:i:s');
                                
                            if($servicerequisitionterm->save()){	
                                $username = User::select('name')->where('user_id','=',Auth::id())->first();

                                $this->set_log(Auth::id(), $username['name'],'1089','Application.TransServiceRequisition.processDocumentRequisitionTransServiceRequisition',$username['name'],'Add Document Requisition Trans Service Requisition');

                                $msg = "Tambah Dokumen Susulan Berhasil";
                                continue;
                            } else {
                                $msg = "Tambah Dokumen Susulan Gagal";
                                return redirect('/trans-service-requisition/document-requisition/'.$fields['service_requisition_id'])->with('msg',$msg);
                                break;
                            }
                        }else{
                            $servicerequisitionterm->service_requisition_term_status   = $checkbox;
                            $servicerequisitionterm->updated_id                        = Auth::id();
                            $servicerequisitionterm->updated_at                        = date('Y-m-d H:i:s');
                                
                            if($servicerequisitionterm->save()){	
                                $username = User::select('name')->where('user_id','=',Auth::id())->first();

                                $this->set_log(Auth::id(), $username['name'],'1089','Application.TransServiceRequisition.processDocumentRequisitionTransServiceRequisition',$username['name'],'Add Document Requisition Trans Service Requisition');

                                $msg = "Tambah Dokumen Susulan Berhasil";
                                continue;
                            } else {
                                $msg = "Tambah Dokumen Susulan Gagal";
                                return redirect('/trans-service-requisition/document-requisition/'.$fields['service_requisition_id'])->with('msg',$msg);
                                break;
                            }
                        }
                    }else{
                        $msg = "Tambah Dokumen Susulan Gagal";
                        return redirect('/trans-service-requisition/document-requisition/'.$fields['service_requisition_id'])->with('msg',$msg);
                        break;
                    }
                }

                $service_log = array(
                    'service_status'            => 8,
                    'service_requisition_no'    => $servicerequisition['service_requisition_no'],
                    'section_id'                => 1,
                    'created_id'                => Auth::id(),
                );
                TransServiceLog::create($service_log);

                Session::forget('sess_servicerequisitiontokenedit');
                return redirect('/trans-service-requisition')->with('msg',$msg);
            }else{
                $msg = "Tambah Dokumen Susulan Gagal";
                return redirect('/trans-service-requisition/document-requisition/'.$fields['service_requisition_id'])->with('msg',$msg);
            }
        }else{
            foreach($servicedocumentrequisition as $key => $val){
                $documentrequisition =TransServiceDocumentRequisition::where('service_document_requisition_id', $val['service_document_requisition_id'])
                ->first();

                $documentrequisition->data_state = 1;
                if($documentrequisition->save()){
                        
                    if(isset($allrequest['checkbox_'.$val['service_term_id']])){
                        $checkbox = $allrequest['checkbox_'.$val['service_term_id']];
                    }else{
                        $checkbox = 0;
                    }

                    $servicerequisitionterm = TransServiceRequisitionTerm::where('data_state', 0)
                    ->where('service_requisition_id', $fields['service_requisition_id'])
                    ->where('service_term_id', $val['service_term_id'])
                    ->first();

                    $fileNameToStore = '';

                    if($request->hasFile('file_'.$val['service_term_id'])){

                        //Storage::delete('/public/receipt_images/'.$user->receipt_image);

                        // Get filename with the extension
                        $filenameWithExt = $request->file('file_'.$val['service_term_id'])->getClientOriginalName();
                        //Get just filename
                        $filename = pathinfo($filenameWithExt, PATHINFO_FILENAME);
                        // Get just ext
                        $extension = $request->file('file_'.$val['service_term_id'])->getClientOriginalExtension();
                        // Filename to store
                        $fileNameToStore = $filename.'_'.time().'.'.$extension;
                        // Upload Image
                        $path = $request->file('file_'.$val['service_term_id'])->storeAs('public/term/'.$fields['service_id'],$fileNameToStore);

                        $servicerequisitionterm->service_requisition_term_value    = $fileNameToStore;
                        $servicerequisitionterm->service_requisition_term_status   = $checkbox;
                        $servicerequisitionterm->updated_id                        = Auth::id();
                        $servicerequisitionterm->updated_at                        = date('Y-m-d H:i:s');
                            
                        if($servicerequisitionterm->save()){	
                            $username = User::select('name')->where('user_id','=',Auth::id())->first();

                            $this->set_log(Auth::id(), $username['name'],'1089','Application.TransServiceRequisition.processDocumentRequisitionTransServiceRequisition',$username['name'],'Add Document Requisition Trans Service Requisition');

                            $msg = "Tambah Dokumen Susulan Berhasil";
                            continue;
                        } else {
                            $msg = "Tambah Dokumen Susulan Gagal";
                            return redirect('/trans-service-requisition/document-requisition/'.$fields['service_requisition_id'])->with('msg',$msg);
                            break;
                        }
                
                    }else{
                        $servicerequisitionterm->service_requisition_term_status   = $checkbox;
                        $servicerequisitionterm->updated_id                        = Auth::id();
                        $servicerequisitionterm->updated_at                        = date('Y-m-d H:i:s');
                            
                        if($servicerequisitionterm->save()){	
                            $username = User::select('name')->where('user_id','=',Auth::id())->first();

                            $this->set_log(Auth::id(), $username['name'],'1089','Application.TransServiceRequisition.processDocumentRequisitionTransServiceRequisition',$username['name'],'Add Document Requisition Trans Service Requisition');

                            $msg = "Tambah Dokumen Susulan Berhasil";
                            continue;
                        } else {
                            $msg = "Tambah Dokumen Susulan Gagal";
                            return redirect('/trans-service-requisition/document-requisition/'.$fields['service_requisition_id'])->with('msg',$msg);
                            break;
                        }
                    }
                }else{
                    $msg = "Tambah Dokumen Susulan Gagal";
                    return redirect('/trans-service-requisition/document-requisition/'.$fields['service_requisition_id'])->with('msg',$msg);
                    break;
                }
            }

            $service_log = array(
                'service_status'            => 8,
                'service_requisition_no'    => $servicerequisition['service_requisition_no'],
                'section_id'                => 1,
                'created_id'                => Auth::id(),
            );
            TransServiceLog::create($service_log);

            Session::forget('sess_servicerequisitiontokenedit');
            return redirect('/trans-service-requisition')->with('msg',$msg);
        }
    }

    public function deleteCoreService($service_id)
    {
        $item = CoreService::findOrFail($service_id);
        $item->data_state = 1;
        $item->deleted_id = Auth::id();
        $item->deleted_at = date("Y-m-d H:i:s");
        if($item->save())
        {
            $msg = 'Hapus Layanan Berhasil';
        }else{
            $msg = 'Hapus Layanan Gagal';
        }

        return redirect('/service')->with('msg',$msg);
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

    public function downloadTransServiceRequisitionTerm($service_id, $service_requisition_term_id){
        $requisitionterm = TransServiceRequisitionTerm::findOrFail($service_requisition_term_id);
        
        return response()->download(
            storage_path('app/public/term/'.$service_id.'/'.$requisitionterm['service_requisition_term_value']),
            'term_'.$requisitionterm['service_requisition_term_id'].'.png',
        );
        // print_r($service_requisition_term_id);
        // print_r($service_id);
    }

    public function getServiceRequisitionTermStatus($service_requisition_id, $service_term_id){
        $servicerequisitionterm = TransServiceRequisitionTerm::where('data_state', 0)
        ->where('service_requisition_id', $service_requisition_id)
        ->where('service_term_id', $service_term_id)
        ->first();

        return $servicerequisitionterm['service_requisition_term_status'];
    }

    public function getServiceRequisitionTermValue($service_requisition_id, $service_term_id){
        $servicerequisitionterm = TransServiceRequisitionTerm::where('data_state', 0)
        ->where('service_requisition_id', $service_requisition_id)
        ->where('service_term_id', $service_term_id)
        ->first();

        return $servicerequisitionterm['service_requisition_term_value'];
    }
    
    public function print($service_requisition_id){
        $transservicerequisition = TransServiceRequisition::findOrFail($service_requisition_id);

        $coreservice = CoreService::where('service_id', $transservicerequisition['service_id'])
        ->where('data_state', 0)
        ->first();

        $username = User::select('name')->where('user_id','=',Auth::id())->first();

        $this->set_log(Auth::id(), $username['name'],'1089','Application.TransServiecRequisition.print',$username['name'],'Export');

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

        $datetime = strtotime($transservicerequisition['created_at']);
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
                    <a style=\"color: green; text-decoration: none; font-size: 18px\">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Kota Sragen</a>
                </div>
            </section>
        <br>
        </section>
        <hr style=\"width:100%; text-align:left !important; \"></hr>
        </br>
        <div style=\"text-align:center !important;\">
            <a style=\"color: black; text-decoration: none; font-size: 15px\">TANDA BUKTI PENGAJUAN BANTUAN</a>
            <br/>
            <a style=\"color: black; text-decoration: none; font-size: 12px\">".$transservicerequisition['service_requisition_no']."</a>
        </div>
        </br>
        <hr style=\"width:100%; text-align:left !important; \"></hr>
        </br>
        <div style=\"text-align:left;\">          
            <a style=\"color: black; text-decoration: none; font-size: 15px\">&nbsp;&nbsp;&nbsp;&nbsp;Dengan ini menjadi tanda bukti pengajuan bantuan kepada Badan Amil Zakat Nasional Kota Sragen yang diajukan oleh : </a>
        </div>
        <div style=\"text-align:left;\">            
            <a style=\"color: black; text-decoration: none; font-size: 15px\">Nama &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: ".$transservicerequisition['service_requisition_name']."</a>
            <br/>
            <a style=\"color: black; text-decoration: none; font-size: 15px\"> Tanggal &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: ".$date."</a>
            <br/>
            <a style=\"color: black; text-decoration: none; font-size: 15px\"> Nomor Whatsapp : ".$transservicerequisition['service_requisition_phone']."</a>
            <br/>
            <a style=\"color: black; text-decoration: none; font-size: 15px\"> Jenis Bantuan &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;: ".Str::upper($coreservice['service_name'])."</a>
        </div>
        <div style=\"text-align:left;\">          
            <a style=\"color: black; text-decoration: none; font-size: 15px\">&nbsp;&nbsp;&nbsp;&nbsp;Proses - proses sampai disetujuinya pengajuan bantuan ini akan disampaikan melalui pesan Whatsapp dengan nomor yang tertera diatas. Scan kode QR yang tertera di kantor kami (Komplek Masjid Bazis, Pilangsari, Kebayanan Jetis, Pilangsari, Kec. Sragen, Kota Sragen, Jawa Tengah 57252) untuk melihat track tahapan pengajuan bantuan anda.</a>
            <p style=\"color: black; text-decoration: none; font-size: 15px\">&nbsp;&nbsp;&nbsp;&nbsp;Terimakasih telah mengajukan bantuan kepada kami. Semoga pengajuan bantuan yang diberikan kepada Badan Amil Zakat Nasional Kota Sragen dapat di proses dengan baik dan memenuhi persyaratan yang ada.</p>
        </div>
        <div style=\"text-align:left;\">          
            <a style=\"color: black; text-decoration: none; font-size: 15px\">Sragen, ".$date."</a>
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
        $pdf::write2DBarcode($transservicerequisition['service_requisition_no'], 'QRCODE,H', 167, 5, 40, 40, $style, 'N');
        $pdf::Image( $path, 7, 4, 38, 38, 'PNG', '', 'LT', false, 300, '', false, false, 1, false, false, false);

        if (ob_get_contents()) ob_end_clean();
        // -----------------------------------------------------------------------------
        
        //Close and output PDF document
        $filename = 'Bukti Pengajuan Bantuan_'.$transservicerequisition['service_requisition_id'].'.pdf';
        $pdf::Output($filename, 'I');

        //============================================================+
        // END OF FILE
        //============================================================+
    }

    public function checkNIK(Request $request){
        $year   = date('Y');

        if($request->nik != "" && $request->nik != null ){
            $check1 = TransServiceRequisition::select('service_requisition_id')
            ->whereYear('created_at', $year)
            ->where('service_requisition_nik', $request->nik)
            ->where('service_requisition_status', 4)
            ->get();

            $check2 = TransServiceRequisition::select('service_requisition_id')
            ->whereYear('created_at', $year)
            ->where('service_requisition_nik', $request->nik)
            ->where('service_requisition_status', 8)
            ->get();

            $check3 = TransServiceRequisition::select('service_requisition_id')
            ->whereYear('created_at', ($year-1))
            ->where('service_requisition_nik', $request->nik)
            ->where('service_requisition_status', 4)
            ->get();

            $check4 = TransServiceRequisition::select('service_requisition_id')
            ->whereYear('created_at', ($year-1))
            ->where('service_requisition_nik', $request->nik)
            ->where('service_requisition_status', 8)
            ->get();

            $check = $check1->merge($check2);
            $check = $check->merge($check3);
            $check = $check->merge($check4);

            if(count($check) > 0){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    public function checkName(Request $request){
        $year   = date('Y');

        if($request->name != "" && $request->name != null ){
            $check1 = TransServiceRequisition::select('service_requisition_id')
            ->whereYear('created_at', $year)
            ->where('service_requisition_name', $request->name)
            ->where('service_requisition_status', 4)
            ->get();

            $check2 = TransServiceRequisition::select('service_requisition_id')
            ->whereYear('created_at', $year)
            ->where('service_requisition_name', $request->name)
            ->where('service_requisition_status', 8)
            ->get();

            $check3 = TransServiceRequisition::select('service_requisition_id')
            ->whereYear('created_at', ($year-1))
            ->where('service_requisition_name', $request->name)
            ->where('service_requisition_status', 4)
            ->get();

            $check4 = TransServiceRequisition::select('service_requisition_id')
            ->whereYear('created_at', ($year-1))
            ->where('service_requisition_name', $request->name)
            ->where('service_requisition_status', 8)
            ->get();

            $check = $check1->merge($check2);
            $check = $check->merge($check3);
            $check = $check->merge($check4);

            if(count($check) > 0){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    public function checkAddress(Request $request){
        $year   = date('Y');

        if($request->address != "" && $request->address != null ){
            $check1 = TransServiceRequisition::select('service_requisition_id')
            ->whereYear('created_at', $year)
            ->where('service_requisition_address', $request->address)
            ->where('service_requisition_status', 4)
            ->get();

            $check2 = TransServiceRequisition::select('service_requisition_id')
            ->whereYear('created_at', $year)
            ->where('service_requisition_address', $request->address)
            ->where('service_requisition_status', 8)
            ->get();

            $check3 = TransServiceRequisition::select('service_requisition_id')
            ->whereYear('created_at', ($year-1))
            ->where('service_requisition_address', $request->address)
            ->where('service_requisition_status', 4)
            ->get();

            $check4 = TransServiceRequisition::select('service_requisition_id')
            ->whereYear('created_at', ($year-1))
            ->where('service_requisition_address', $request->address)
            ->where('service_requisition_status', 8)
            ->get();

            $check = $check1->merge($check2);
            $check = $check->merge($check3);
            $check = $check->merge($check4);

            if(count($check) > 0){
                return true;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }
}
