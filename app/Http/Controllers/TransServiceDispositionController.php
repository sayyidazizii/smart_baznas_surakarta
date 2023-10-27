<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\CoreKecamatan;
use App\Models\CoreKelurahan;
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
use App\Models\MustahikWorksheetRequisition;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class TransServiceDispositionController extends Controller
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
        Session::forget('sess_servicedispositiontoken');
        Session::forget('sess_servicedispositiontokenedit');
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

        $mustahiksurveyor = User::select('user_id', 'full_name')
        ->where('mustahik_surveyor', 1)
        ->pluck('full_name', 'user_id');

        $transservicedisposition = TransServiceDisposition::where('data_state', 0)
        ->where('created_at','>=',$start_date)
        ->where('created_at','<=',$stop_date)
        ->get();

        return view('content/TransServiceDisposition/ListTransServiceDisposition',compact('mustahiksurveyor', 'transservicedisposition', 'start_date', 'end_date'));
    }
    

    public function filter(Request $request){
        $start_date     = $request->start_date;
        $end_date       = $request->end_date;

        Session::put('start_date', $start_date);
        Session::put('end_date', $end_date);

        return redirect('/trans-service-disposition');
    }

    public function search()
    {
        Session::forget('sess_servicedispositiontoken');
        $transservicerequisition = TransServiceRequisition::select('trans_service_requisition.*', 'core_service.service_name')
        ->where('trans_service_requisition.data_state', 0)
        ->where('service_requisition_status', 0)
        ->join('core_service', 'core_service.service_id', 'trans_service_requisition.service_id')
        ->get();

        return view('content/TransServiceDisposition/SearchTransServiceRequisition',compact('transservicerequisition'));
    }

    public function addReset($service_requisition_id)
    {
        Session::forget('sess_servicerequisitiontokenedit');

        return redirect('/trans-service-disposition/add/'.$service_requisition_id);
    }

    public function addTransServiceDisposition($service_requisition_id)
    {
        $service_disposition_token		    = Session::get('sess_servicedispositiontoken');

        if (empty($service_disposition_token)){
            $service_disposition_token = md5(date("YmdHis"));
            Session::put('sess_servicedispositiontoken', $service_disposition_token);
        }

        $service_disposition_token		= Session::get('sess_servicedispositiontoken');

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

        $coresection    = CoreSection::where('data_state', 0)
        ->pluck('section_name', 'section_id');

        return view('content/TransServiceDisposition/FormAddTransServiceDisposition',compact('servicerequisition', 'servicerequisitionparameter', 'servicerequisitionterm', 'service_disposition_token', 'service_requisition_id', 'coresection'));
    }

    public function detailTransServiceDisposition($service_disposition_id){
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

        return view('content/TransServiceDisposition/FormDetailTransServiceDisposition',compact('servicedisposition', 'servicedispositionterm', 'servicedispositionparameter', 'service_disposition_id'));
    }

    public function editTransServiceDisposition($service_disposition_id){
        $service_disposition_token_edit		    = Session::get('sess_servicedispositiontokenedit');

        if (empty($service_disposition_token_edit)){
            $service_disposition_token_edit = md5(date("YmdHis"));
            Session::put('sess_servicedispositiontoken', $service_disposition_token_edit);
        }

        $service_disposition_token_edit		= Session::get('sess_servicedispositiontoken');

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

        return view('content/TransServiceDisposition/FormEditTransServiceDisposition',compact('servicedisposition', 'servicedispositionparameter', 'servicedispositionterm', 'service_disposition_token_edit', 'service_disposition_id', 'coresection'));
    }
    

    public function addElementsCoreService(Request $request)
    {
        $data_coreservice[$request->name] = $request->value;

        Session::put('data_coreservice', $data_coreservice);
        
        return redirect('/service/add');
    }

    public function processDocumentRequisitionTransServiceDisposition(Request $request){
        $fields = $request->validate([
            'service_requisition_id'    => 'required',
            'service_disposition_token'  => 'required',
        ]);
        
        $allrequest = $request->all();

        $servicerequisitionterm = TransServiceRequisitionTerm::where('data_state', 0)
        ->get();

        $servicerequisition = TransServiceRequisition::findOrFail($fields['service_requisition_id']);
        $servicerequisition->service_requisition_status = 2;

        if($servicerequisition->save()){
            
            $username = User::select('name')->where('user_id','=',Auth::id())->first();

            $this->set_log(Auth::id(), $username['name'],'1089','Application.TransServiceDisposition.processDocumentRequisitionTransServiceDisposition',$username['name'],'Document Requisition');

            foreach($servicerequisitionterm as $key => $val){
                if(isset($allrequest['checkbox_'.$val['service_term_id']])){
                    $servicedocumentrequisition = array(
                        'service_requisition_id'                => $fields['service_requisition_id'],
                        'service_term_id'                       => $val['service_term_id'],
                        'service_document_requisition_remark'   => $allrequest['service_document_requisition_remark_'.$val['service_term_id']],
                        'service_requisition_term_id'           => $allrequest['service_requisition_term_id_'.$val['service_term_id']],
                        'service_document_requisition_token'    => $fields['service_disposition_token'].$val['service_term_id'],
                    );
                    
                    $servicedocumentrequisitiontoken = TransServiceDocumentRequisition::where('service_document_requisition_token', $servicedocumentrequisition['service_document_requisition_token'])
                    ->count();

                    if($servicedocumentrequisitiontoken == 0){
                        if(TransServiceDocumentRequisition::create($servicedocumentrequisition)){
                            $msg = "Request Dokumen Susulan Berhasil";
                        }else{
                            $msg = "Request Dokumen Susulan Gagal";
                            return redirect('/trans-service-disposition/add/'.$fields['service_requisition_id'])->with('msg',$msg);
                        }
                    }
                }
            }
            
            $requisition_data = TransServiceRequisition::findOrFail($fields['service_requisition_id']);

            $service_log = array(
                'service_status'            => 5,
                'service_requisition_no'    => $requisition_data['service_requisition_no'],
                'section_id'                => 1,
                'created_id'                => Auth::id(),
            );
            TransServiceLog::create($service_log);

            $wa_msg = "Siabas\r\n\r\n\r\nNama : ".$requisition_data['service_requisition_name']."\r\n\r\nNomor Pengajuan : ".$requisition_data['service_requisition_no']."\r\n\r\nJenis Pengajuan : ".$this->getServiceName($requisition_data['service_id'])."\r\n\r\nBagian : ".$this->getSectionName(1)."\r\n\r\nPesan : ".$this->getMessage(5);
            $wa_status = $this->getMessageStatus(5);
            $wa_no  = $requisition_data['service_requisition_phone'];
            $this->postWhatsappMessages($wa_msg, $wa_status, $wa_no);
            
            Session::forget('sess_servicedispositiontoken');
            return redirect('/trans-service-disposition')->with('msg',$msg);
        }else{
            $msg = "Request Dokumen Susulan Gagal";
            return redirect('/trans-service-disposition'.$fields['service_requisition_id'])->with('msg',$msg);
        }
    }

    public function processDocumentRequisitionTransServiceDispositionEdit(Request $request){
        $fields = $request->validate([
            'service_disposition_id'    => 'required',
            'service_disposition_token'  => 'required',
        ]);
        
        $allrequest = $request->all();

        $servicerequisitionterm = TransServiceRequisitionTerm::where('data_state', 0)
        ->get();
        
        $disposition_data = TransServiceDisposition::findOrFail($fields['service_disposition_id']);

        $service_log = array(
            'service_status'            => 5,
            'service_requisition_no'    => $disposition_data['service_requisition_no'],
            'section_id'                => 1,
            'created_id'                => Auth::id(),
        );
        TransServiceLog::create($service_log);

        $wa_msg = "Siabas\r\n\r\n\r\nNama : ".$disposition_data['service_requisition_name']."\r\n\r\nNomor Pengajuan : ".$disposition_data['service_requisition_no']."\r\n\r\nJenis Pengajuan : ".$this->getServiceName($disposition_data['service_id'])."\r\n\r\nBagian : ".$this->getSectionName(1)."\r\n\r\nPesan : ".$this->getMessage(5);
        $wa_status = $this->getMessageStatus(5);
        $wa_no  = $disposition_data['service_requisition_phone'];
        $this->postWhatsappMessages($wa_msg, $wa_status, $wa_no);

        $servicedisposition = TransServiceDisposition::findOrFail($fields['service_disposition_id']);
        $service_requisition_id = $servicedisposition->service_requisition_id;
        if($servicedisposition){
            if($servicedisposition->delete()){
                $msg = "Request Dokumen Susulan Berhasil";
            }else{
                $msg = "Request Dokumen Susulan Gagal";
                return redirect('/trans-service-disposition/edit/'.$fields['service_disposition_id'])->with('msg',$msg);
            }
        }

        $servicerequisition = TransServiceRequisition::findOrFail($service_requisition_id);
        $servicerequisition->service_requisition_status = 2;

        if($servicerequisition->save()){
            
            $username = User::select('name')->where('user_id','=',Auth::id())->first();

            $this->set_log(Auth::id(), $username['name'],'1089','Application.TransServiceDisposition.processDocumentRequisitionTransServiceDispositionEdit',$username['name'],'Document Requisition Edit');

            foreach($servicerequisitionterm as $key => $val){
                if(isset($allrequest['checkbox_'.$val['service_term_id']])){
                    $servicedocumentrequisition = array(
                        'service_requisition_id'                => $service_requisition_id,
                        'service_term_id'                       => $val['service_term_id'],
                        'service_document_requisition_remark'   => $allrequest['service_document_requisition_remark_'.$val['service_term_id']],
                        'service_requisition_term_id'           => $allrequest['service_requisition_term_id_'.$val['service_term_id']],
                        'service_document_requisition_token'    => $fields['service_disposition_token'].$val['service_term_id'],
                    );
                    
                    $servicedocumentrequisitiontoken = TransServiceDocumentRequisition::where('service_document_requisition_token', $servicedocumentrequisition['service_document_requisition_token'])
                    ->count();

                    if($servicedocumentrequisitiontoken == 0){
                        if(TransServiceDocumentRequisition::create($servicedocumentrequisition)){
                            $msg = "Request Dokumen Susulan Berhasil";
                        }else{
                            $msg = "Request Dokumen Susulan Gagal";
                            return redirect('/trans-service-disposition/edit/'.$fields['service_disposition_id'])->with('msg',$msg);
                        }
                    }
                }
            }
            
            Session::forget('sess_servicedispositiontoken');
            return redirect('/trans-service-disposition')->with('msg',$msg);
        }else{
            $msg = "Request Dokumen Susulan Gagal";
            return redirect('/trans-service-disposition'.$fields['service_requisition_id'])->with('msg',$msg);
        }
    }

    public function processAddTransServiceDisposition(Request $request)
    {
        $fields = $request->validate([
            'service_requisition_id'    => 'required',
            'service_requisition_no'    => 'required',
            'service_id'                => 'required',
            'section_id'                => 'required',
            'service_requisition_name'  => 'required',
            'service_requisition_phone' => 'required',
            'service_disposition_token' => 'required',
        ]);

        $servicerequisitionterm = TransServiceRequisitionTerm::where('data_state', 0)
        ->where('service_requisition_id', $fields['service_requisition_id'])
        ->get();

        $servicerequisitionparameter = TransServiceRequisitionParameter::where('data_state', 0)
        ->where('service_requisition_id', $fields['service_requisition_id'])
        ->get();
        
        $allrequest = $request->all();
        
        $data = array(
            'service_requisition_id'        => $fields['service_requisition_id'],
            'service_requisition_name'		=> $fields['service_requisition_name'],
            'service_requisition_phone'		=> $fields['service_requisition_phone'],
            'service_requisition_no'		=> $fields['service_requisition_no'],
            'service_id'		            => $fields['service_id'],
            'section_id'		            => $fields['section_id'],
            'kecamatan_id'		            => $request->kecamatan_id,
            'kelurahan_id'		            => $request->kelurahan_id,
            'service_requisition_nik'		=> $request->service_requisition_nik,
            'service_disposition_address'	=> $request->service_requisition_address,
            'service_disposition_remark'	=> $request->service_disposition_remark,
            'service_disposition_token'     => $fields['service_disposition_token'],
            'data_state' 				    => 0,
            'created_id' 				    => Auth::id(),
            'created_on' 				    => date('Y-m-d H:i:s'),
        );

        $service_disposition_token  = TransServiceDisposition::select('service_disposition_token')
            ->where('service_disposition_token', $data['service_disposition_token'])
            ->count();
            
        $servicerequisition = TransServiceRequisition::findOrFail($fields['service_requisition_id']);
        $servicerequisition->service_requisition_status = 1;
        
        if($servicerequisition->save()){
            if($service_disposition_token == 0){
                if(TransServiceDisposition::create($data)){
                    $username = User::select('name')->where('user_id','=',Auth::id())->first();

                    $this->set_log(Auth::id(), $username['name'],'1089','Application.TransServiceDisposition.processAddTransServiceDisposition',$username['name'],'Add Trans Service Disposition');

                    $transservicedisposition_last 		= TransServiceDisposition::select('service_disposition_id', 'service_requisition_no')
                        ->where('created_id', $data['created_id'])
                        ->orderBy('service_disposition_id', 'DESC')
                        ->first();
                        
                    foreach($servicerequisitionterm as $key => $val){
                        $dataitem = array(
                            'service_disposition_id'             => $transservicedisposition_last['service_disposition_id'],
                            'service_term_id'                    => $val['service_term_id'],
                            'service_requisition_term_id'        => $val['service_requisition_term_id'],
                            'service_disposition_term_status'    => $val['service_requisition_term_status'],
                            'service_disposition_term_value'     => $val['service_requisition_term_value'],
                            'service_disposition_term_token'     => $data['service_disposition_token'].$val['service_term_id'],
                        );

                        $service_disposition_term_token = TransServiceDispositionTerm::select('service_disposition_term_token')
                            ->where('service_disposition_term_token', '=', $dataitem['service_disposition_term_token'])
                            ->count();

                        if($service_disposition_term_token == 0){
                            if(TransServiceDispositionTerm::create($dataitem)){	
                                $username = User::select('name')->where('user_id','=',Auth::id())->first();

                                $this->set_log(Auth::id(), $username['name'],'1089','Application.TransServiceDisposition.processAddTransServiceDisposition',$username['name'],'Add Trans Service Disposition');

                                $msg = "Tambah Disposisi Bantuan Berhasil";
                                continue;
                            } else {
                                $msg = "Tambah Disposisi Bantuan Gagal";
                                return redirect('/trans-service-disposition/add/'.$fields['service_requisition_id'])->with('msg',$msg);
                                break;
                            }
                        }
                    }
                    
                    foreach($servicerequisitionparameter as $key => $val){
                        $dataitem = array(
                            'service_disposition_id'                 => $transservicedisposition_last['service_disposition_id'],
                            'service_parameter_id'                  => $val['service_parameter_id'],
                            'service_requisition_parameter_id'      => $val['service_requisition_parameter_id'],
                            'service_disposition_parameter_value'    => $val['service_requisition_parameter_value'],
                            'service_disposition_parameter_token'    => $data['service_disposition_token'].$val['service_parameter_id'],
                        );

                        $service_disposition_parameter_token = TransServiceDispositionParameter::select('service_disposition_parameter_token')
                            ->where('service_disposition_parameter_token', '=', $dataitem['service_disposition_parameter_token'])
                            ->count();

                        if($service_disposition_parameter_token == 0){
                            if(TransServiceDispositionParameter::create($dataitem)){	
                                $username = User::select('name')->where('user_id','=',Auth::id())->first();

                                $this->set_log(Auth::id(), $username['name'],'1089','Application.TransServiceDisposition.processAddTransServiceDisposition',$username['name'],'Add Trans Service Disposition');

                                $msg = "Tambah Disposisi Bantuan Berhasil";
                                continue;
                            } else {
                                $msg = "Tambah Disposisi Bantuan Gagal";
                                return redirect('/trans-service-disposition/add/'.$fields['service_requisition_id'])->with('msg',$msg);
                                break;
                            }
                        }
                    }

                    $service_log = array(
                        'service_status'            => 2,
                        'service_requisition_no'    => $transservicedisposition_last['service_requisition_no'],
                        'section_id'                => $data['section_id'],
                        'created_id'                => Auth::id(),
                    );
                    TransServiceLog::create($service_log);
            
                    $wa_msg = "Siabas\r\n\r\n\r\nNama : ".$data['service_requisition_name']."\r\n\r\nNomor Pengajuan : ".$transservicedisposition_last['service_requisition_no']."\r\n\r\nJenis Pengajuan : ".$this->getServiceName($data['service_id'])."\r\n\r\nBagian : ".$this->getSectionName($data['section_id'])."\r\n\r\nPesan : ".$this->getMessage(2);
                    $wa_status = $this->getMessageStatus(2);
                    $wa_no  = $data['service_requisition_phone'];
                    $this->postWhatsappMessages($wa_msg, $wa_status, $wa_no);
                    
                    Session::forget('sess_servicedispositiontoken');
                    return redirect('/trans-service-disposition')->with('msg',$msg);
                }else{
                    $msg = "Tambah Disposisi Bantuan Gagal";
                    return redirect('/trans-service-disposition/add/'.$fields['service_requisition_id'])->with('msg',$msg);
                }
            } else {
                $username = User::select('name')->where('user_id','=',Auth::id())->first();

                $this->set_log(Auth::id(), $username['name'],'1089','Application.TransServiceDisposition.processAddTransServiceDisposition',$username['name'],'Add Trans Service Disposition');

                $transservicedisposition_last   = TransServiceDisposition::select('service_disposition_id', 'service_requisition_no')
                    ->where('created_id', '=', $data['created_id'])
                    ->orderBy('service_disposition_id', 'DESC')
                    ->first();

                $msg = "Tambah Disposisi Bantuan Berhasil";
                        
                foreach($servicerequisitionterm as $key => $val){
                    $dataitem = array(
                        'service_disposition_id'             => $transservicedisposition_last['service_disposition_id'],
                        'service_term_id'                   => $val['service_term_id'],
                        'service_requisition_term_id'       => $val['service_requisition_term_id'],
                        'service_disposition_term_status'    => $val['service_requisition_term_status'],
                        'service_disposition_term_value'     => $val['service_requisition_term_value'],
                        'service_disposition_term_token'     => $data['service_disposition_token'].$val['service_term_id'],
                    );

                    $service_disposition_term_token = TransServiceDispositionTerm::select('service_disposition_term_token')
                        ->where('service_disposition_term_token', '=', $dataitem['service_disposition_term_token'])
                        ->count();

                    if($service_disposition_term_token == 0){
                        if(TransServiceDispositionTerm::create($dataitem)){	
                            $username = User::select('name')->where('user_id','=',Auth::id())->first();

                            $this->set_log(Auth::id(), $username['name'],'1089','Application.TransServiceDisposition.processAddTransServiceDisposition',$username['name'],'Add Trans Service Disposition');

                            $msg = "Tambah Disposisi Bantuan Berhasil";
                            continue;
                        } else {
                            $msg = "Tambah Disposisi Bantuan Gagal";
                            return redirect('/trans-service-disposition/add/'.$fields['service_requisition_id'])->with('msg',$msg);
                            break;
                        }
                    }
                }
                
                foreach($servicerequisitionparameter as $key => $val){
                    $dataitem = array(
                        'service_disposition_id'                 => $transservicedisposition_last['service_disposition_id'],
                        'service_parameter_id'                  => $val['service_parameter_id'],
                        'service_requisition_parameter_id'      => $val['service_requisition_parameter_id'],
                        'service_disposition_parameter_value'    => $val['service_requisition_parameter_value'],
                        'service_disposition_parameter_token'    => $data['service_disposition_token'].$val['service_parameter_id'],
                    );

                    $service_disposition_parameter_token = TransServiceDispositionParameter::select('service_disposition_parameter_token')
                        ->where('service_disposition_parameter_token', '=', $dataitem['service_disposition_parameter_token'])
                        ->count();

                    if($service_disposition_parameter_token == 0){
                        if(TransServiceDispositionParameter::create($dataitem)){	
                            $username = User::select('name')->where('user_id','=',Auth::id())->first();

                            $this->set_log(Auth::id(), $username['name'],'1089','Application.TransServiceDisposition.processAddTransServiceDisposition',$username['name'],'Add Trans Service Disposition');

                            $msg = "Tambah Disposisi Bantuan Berhasil";
                            continue;
                        } else {
                            $msg = "Tambah Disposisi Bantuan Gagal";
                            return redirect('/trans-service-disposition/add/'.$fields['service_requisition_id'])->with('msg',$msg);
                            break;
                        }
                    }
                }

                $service_log = array(
                    'service_status'            => 2,
                    'service_requisition_no'    => $transservicedisposition_last['service_requisition_no'],
                    'section_id'                => $data['section_id'],
                    'created_id'                => Auth::id(),
                );
                TransServiceLog::create($service_log);

                $wa_msg = "Siabas\r\n\r\n\r\nNama : ".$data['service_requisition_name']."\r\n\r\nNomor Pengajuan : ".$transservicedisposition_last['service_requisition_no']."\r\n\r\nJenis Pengajuan : ".$this->getServiceName($data['service_id'])."\r\n\r\nBagian : ".$this->getSectionName($data['section_id'])."\r\n\r\nPesan : ".$this->getMessage(2);
                $wa_status = $this->getMessageStatus(2);
                $wa_no  = $data['service_requisition_phone'];
                $this->postWhatsappMessages($wa_msg, $wa_status, $wa_no);
                
                Session::forget('sess_servicedispositiontoken');
                return redirect('/trans-service-disposition')->with('msg',$msg);
            }
        }else{
            $msg = "Tambah Disposisi Bantuan Gagal";
            return redirect('/trans-service-disposition/add/'.$fields['service_requisition_id'])->with('msg',$msg);
        }
    }

    public function processEditTransServiceDisposition(Request $request)
    {
        $fields = $request->validate([
            'service_disposition_id'         => 'required',
            'service_id'                    => 'required',
            'section_id'                    => 'required',
            'service_disposition_token_edit' => 'required',
        ]);

        $servicedisposition = TransServiceDisposition::findOrFail($fields['service_disposition_id']);
        $servicedisposition->section_id                      = $fields['section_id'];
        $servicedisposition->service_disposition_remark      = $request->service_disposition_remark;
        $servicedisposition->service_disposition_token_edit  = $fields['service_disposition_token_edit'];
        $servicedisposition->updated_id                      = Auth::id();
        $servicedisposition->updated_at                      = date('Y-m-d H:i:s');

        $service_disposition_token_edit  = TransServiceDisposition::select('service_disposition_token_edit')
            ->where('service_disposition_token_edit', $fields['service_disposition_token_edit'])
            ->count();

        $msg = "Edit Disposisi Bantuan Berhasil";
        
        if($service_disposition_token_edit == 0){
            if($servicedisposition->save()){
                $msg = "Edit Disposisi Bantuan Berhasil";
                Session::forget('sess_servicedispositiontokenedit');
                return redirect('/trans-service-disposition')->with('msg',$msg);
            }else{
                $msg = "Tambah Disposisi Bantuan Gagal";
                return redirect('/trans-service-disposition/edit/'.$fields['service_disposition_id'])->with('msg',$msg);
            }
        } else {
            $msg = "Edit Disposisi Bantuan Berhasil";
            Session::forget('sess_servicedispositiontokenedit');
            return redirect('/trans-service-disposition')->with('msg',$msg);
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

    public function getKecamatanName($kecamatan_id){
        $kecamatan = CoreKecamatan::select('kecamatan_name')
        ->where('kecamatan_id', $kecamatan_id)
        ->where('data_state', 0)
        ->first();

        if($kecamatan){
            $kecamatan_name = $kecamatan['kecamatan_name'];
        }else{
            $kecamatan_name = "-";
        }

        return $kecamatan_name;
    }

    public function getKelurahanName($kelurahan_id){
        $kelurahan = CoreKelurahan::select('kelurahan_name')
        ->where('kelurahan_id', $kelurahan_id)
        ->where('data_state', 0)
        ->first();

        if($kelurahan){
            $kelurahan_name = $kelurahan['kelurahan_name'];
        }else{
            $kelurahan_name = "-";
        }

        return $kelurahan_name;
    }

    public function downloadTransServiceDispositionTerm($service_id, $service_disposition_term_id){
        $dispositionterm = TransServiceDispositionTerm::findOrFail($service_disposition_term_id);
        
        return response()->download(
            storage_path('app/public/term/'.$service_id.'/'.$dispositionterm['service_disposition_term_value']),
            'term_'.$dispositionterm['service_disposition_term_id'].'.png',
        );
    }
    
    public function addMustahikRequisition(Request $request)
    {
        $fields = $request->validate([
            'mustahik_service_disposition_id'   => 'required',
            'user_id'                           => 'required',
        ]);
        
        $servicedisposition = TransServiceDisposition::findOrFail($fields['mustahik_service_disposition_id']);

        $data = array(
            'service_requisition_id'        => $servicedisposition['service_requisition_id'],
            'service_id'                    => $servicedisposition['service_id'],
            'user_id'                       => $fields['user_id'],
            'worksheet_requisition_date'    => date("Y-m-d"),
        );

        $servicedisposition->mustahik_status = 1;
        if($servicedisposition->save()){
            $mustahikrequisition_cek = MustahikWorksheetRequisition::select('*')
            ->where('service_requisition_id', $servicedisposition['service_requisition_id'])
            ->get();
            
            if(count($mustahikrequisition_cek) == 0){
                if(MustahikWorksheetRequisition::create($data)){
                    $msg = "Permintaan Penilaian Berhasil";
                    return redirect('/trans-service-disposition')->with('msg',$msg);
                }else{
                    $msg = "Permintaan Penilaian Gagal";
                    return redirect('/trans-service-disposition')->with('msg',$msg);
                }
            }else{
                $msg = "Permintaan Penilaian Sudah Dilakukan";
                return redirect('/trans-service-disposition')->with('msg',$msg);
            }
        }else{
            $msg = "Permintaan Penilaian Gagal";
            return redirect('/trans-service-disposition')->with('msg',$msg);
        }
    }
}
