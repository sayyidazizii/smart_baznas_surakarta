<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\CoreService;
use App\Models\CoreServiceTerm;
use App\Models\CoreServiceParameter;
use App\Models\User;
use App\Models\SystemLogUser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CoreServiceController extends Controller
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
        Session::forget('data_coreservice');
        Session::forget('data_coreserviceterm');
        Session::forget('data_coreserviceparameter');
        Session::forget('sess_servicetoken');
        Session::forget('data_coreserviceterm_first');
        Session::forget('data_coreserviceparameter_first');

        $coreservice = CoreService::where('data_state','=',0)->get();

        return view('content/CoreService/ListCoreService',compact('coreservice'));
    }

    public function addReset()
    {
        Session::forget('sess_servicetoken');
        Session::forget('data_coreservice');
        Session::forget('data_coreserviceterm');
        Session::forget('data_coreserviceparameter');

        return redirect('/service/add');
    }

    public function addCoreService(Request $request)
    {
        $coreservice            = Session::get('data_coreservice');
        $coreserviceterm        = Session::get('data_coreserviceterm');
        $coreserviceparameter   = Session::get('data_coreserviceparameter');

        $service_token		    = Session::get('sess_servicetoken');

        if (empty($service_token)){
            $service_token = md5(date("YmdHis"));
            Session::put('sess_servicetoken', $service_token);
        }

        $service_token		= Session::get('sess_servicetoken');

        return view('content/CoreService/FormAddCoreService',compact('coreservice', 'coreserviceterm', 'service_token', 'coreserviceparameter'));
    }
    

    public function addElementsCoreService(Request $request)
    {
        $data_coreservice[$request->name] = $request->value;

        Session::put('data_coreservice', $data_coreservice);
        
        return redirect('/service/add');
    }

    public function processAddArrayCoreServiceTerm(Request $request)
    {
        $data_coreserviceterm = array(
            'record_id'				    => date('YmdHis'),
            'service_term_no'		    => $request->service_term_no,
            'service_term_description'	=> $request->service_term_description,
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
        
        return redirect('/service/add');
    }

    public function deleteAddArrayCoreServiceTerm($record_id)
    {
        $arrayBaru			= array();
        $dataArrayHeader	= Session::get('data_coreserviceterm');

        foreach($dataArrayHeader as $key=>$val){
            if($key != $record_id){
                $arrayBaru[$key] = $val;
            }
        }
        Session::forget('data_coreserviceterm');
        Session::put('data_coreserviceterm', $arrayBaru);

        return redirect('/service/add');
    }


    public function processAddArrayCoreServiceParameter(Request $request)
    {
        $data_coreserviceparameter = array(
            'record_id'		    		    => date('YmdHis'),
            'service_parameter_no'		    => $request->service_parameter_no,
            'service_parameter_description'	=> $request->service_parameter_description,
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
        
        return redirect('/service/add');
    }

    public function deleteAddArrayCoreServiceParameter($record_id)
    {
        $arrayBaru			= array();
        $dataArrayHeader	= Session::get('data_coreserviceparameter');

        foreach($dataArrayHeader as $key=>$val){
            if($key != $record_id){
                $arrayBaru[$key] = $val;
            }
        }
        Session::forget('data_coreserviceparameter');
        Session::put('data_coreserviceparameter', $arrayBaru);

        return redirect('/service/add');
    }

    public function processAddCoreService(Request $request)
    {
        $session_coreserviceterm		= Session::get('data_coreserviceterm');
        $session_coreserviceparameter	= Session::get('data_coreserviceparameter');

        $fields = $request->validate([
            'service_name'  => 'required',
            'service_token' => 'required',
        ]);
        
        $data = array(
            'service_name'		        => $fields['service_name'],
            'service_token' 			=> $fields['service_token'],
            'data_state' 				=> 0,
            'created_id' 				=> Auth::id(),
            'created_on' 				=> date('Y-m-d H:i:s'),
        );

        $service_token 				    = CoreService::select('service_token')
            ->where('service_token', '=', $data['service_token'])
            ->count();
        
        if (!empty($session_coreserviceterm) && !empty($session_coreserviceparameter)){
            if($service_token == 0){
                if(CoreService::create($data)){
                    $username = User::select('name')->where('user_id','=',Auth::id())->first();

                    $this->set_log(Auth::id(), $username['name'],'1089','Application.CoreService.processAddCoreService',$username['name'],'Add Core Service');

                    $coreservice_last 		= CoreService::select('service_id')
                        ->where('created_id', '=', $data['created_id'])
                        ->orderBy('service_id', 'DESC')
                        ->first();
                    
                    foreach($session_coreserviceterm as $key => $val){
                        $dataitem = array(
                            'service_id'			    => $coreservice_last['service_id'],
                            'service_term_no'			=> $val['service_term_no'],
                            'service_term_description'	=> $val['service_term_description'],
                            'service_term_token'	    => $data['service_token'].$val['record_id']
                        );

                        $service_item_token = CoreServiceTerm::select('service_term_token')
                            ->where('service_term_token', '=', $dataitem['service_term_token'])
                            ->count();

                        if($service_item_token == 0){
                            if(CoreServiceTerm::create($dataitem)){	
                                $username = User::select('name')->where('user_id','=',Auth::id())->first();

                                $this->set_log(Auth::id(), $username['name'],'1089','Application.CoreService.processAddCoreService',$username['name'],'Add Core Service');

                                $msg = "Tambah Data Layanan Berhasil";
                                continue;
                            } else {
                                $msg = "Tambah Data Layanan Gagal";
                                return redirect('/service/add')->with('msg',$msg);
                                break;
                            }
                        }
                    }
                    
                    foreach($session_coreserviceparameter as $key => $val){
                        $dataitem = array(
                            'service_id'			        => $coreservice_last['service_id'],
                            'service_parameter_no'			=> $val['service_parameter_no'],
                            'service_parameter_description'	=> $val['service_parameter_description'],
                            'service_parameter_token'	    => $data['service_token'].$val['record_id']
                        );

                        $service_item_token = CoreServiceParameter::select('service_parameter_token')
                            ->where('service_parameter_token', '=', $dataitem['service_parameter_token'])
                            ->count();

                        if($service_item_token == 0){
                            if(CoreServiceParameter::create($dataitem)){	
                                $username = User::select('name')->where('user_id','=',Auth::id())->first();

                                $this->set_log(Auth::id(), $username['name'],'1089','Application.CoreService.processAddCoreService',$username['name'],'Add Core Service');

                                $msg = "Tambah Data Layanan Berhasil";
                                continue;
                            } else {
                                $msg = "Tambah Data Layanan Gagal";
                                return redirect('/service/add')->with('msg',$msg);
                                break;
                            }
                        }
                    }
                    Session::forget('data_coreserviceterm');
                    Session::forget('data_coreserviceparameter');
                    Session::forget('sess_servicetoken');
                    $msg = "Tambah Data Layanan Berhasil";
                    return redirect('/service')->with('msg',$msg);
                }else{
                    $msg = "Tambah Data Data Layanan Tidak Berhasil";
                    Session::forget('data_coreserviceterm');
                    return redirect('/service/add')->with('msg',$msg);
                }
            } else {
                $username = User::select('name')->where('user_id','=',Auth::id())->first();

                $this->set_log(Auth::id(), $username['name'],'1089','Application.CoreService.processAddCoreService',$username['name'],'Add Core Service');

                $coreservice_last 		= CoreService::select('service_id')
                    ->where('created_id', '=', $data['created_id'])
                    ->orderBy('service_id', 'DESC')
                    ->first();
                    
                
                foreach($session_coreserviceterm as $key => $val){
                    $dataitem = array(
                        'service_id'			    => $coreservice_last['service_id'],
                        'service_term_no'	        => $val['service_item_title'],
                        'service_term_description'	=> $val['service_item_amount'],
                        'service_term_token'	    => $data['service_token'].$val['record_id']
                    );

                    $service_item_token = CoreServiceTerm::select('service_item_token')
                        ->where('service_item_token', '=', $dataitem['service_item_token'])
                        ->count();

                    if($service_item_token == 0){
                        if(CoreServiceTerm::create($dataitem)){	

                            $msg = "Tambah Data Layanan Berhasil";
                            continue;
                        } else {
                            $msg = "Tambah Data Layanan Tidak Berhasil";
                            return redirect('/service/add')->with('msg',$msg);
                            break;
                        }
                    }	
                }
                    
                
                foreach($session_coreserviceparameter as $key => $val){
                    $dataitem = array(
                        'service_id'			        => $coreservice_last['service_id'],
                        'service_parameter_no'	        => $val['service_item_title'],
                        'service_parameter_description'	=> $val['service_item_amount'],
                        'service_parameter_token'	    => $data['service_token'].$val['record_id']
                    );

                    $service_item_token = CoreServiceParameter::select('service_item_token')
                        ->where('service_item_token', '=', $dataitem['service_item_token'])
                        ->count();

                    if($service_item_token == 0){
                        if(CoreServiceParameter::create($dataitem)){	

                            $msg = "Tambah Data Layanan Berhasil";
                            continue;
                        } else {
                            $msg = "Tambah Data Layanan Tidak Berhasil";
                            return redirect('/service/add')->with('msg',$msg);
                            break;
                        }
                    }	
                }
                Session::forget('data_coreserviceterm');
                Session::forget('data_coreserviceparameter');
                Session::forget('sess_servicetoken');
                $msg = "Tambah Data Layanan Berhasil";
                return redirect('/service')->with('msg',$msg);
            }
            
        } else {
            $msg = "Data Layanan Kosong";
            Session::forget('data_coreserviceterm');
            return redirect('/service/add')->with('msg',$msg);
        }
    }

    public function editCoreService($service_id)
    {
        $service_token_edit		= Session::get('sess_servicetoken');

        if (empty($service_token_edit)){
            $service_token_edit = md5(date("YmdHis"));
            Session::put('sess_servicetoken', $service_token_edit);
        }

        $service_token_edit		    = Session::get('sess_servicetoken');

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

    public function processEditCoreService(Request $request)
    {
        $session_coreserviceterm		= Session::get('data_coreserviceterm');
        $session_coreserviceparameter	= Session::get('data_coreserviceparameter');

        $fields = $request->validate([
            'service_id'            => 'required',
            'service_name'          => 'required',
            'service_token_edit'    => 'required',
        ]);
        

        $item                       = CoreService::findOrFail($fields['service_id']);
        $item->service_name         = $fields['service_name'];
        $item->service_token_edit   = $fields['service_token_edit'];
        $item->updated_id           = Auth::id();

        $service_token_edit         = CoreService::select('service_token_edit')
            ->where('service_token_edit', '=', $fields['service_token_edit'])
            ->count(); 

        if (!empty($session_coreserviceterm) && !empty($session_coreserviceparameter)){
            if ($service_token_edit == 0){
                if($item->save()){
                    $username = User::select('name')->where('user_id','=',Auth::id())->first();

                    $this->set_log(Auth::id(), $username['name'],'1089','Application.CoreService.processEditCoreService',$username['name'],'Edit Core Service');

                    foreach($session_coreserviceterm as $key => $val){
                        if ($val['item_status'] == 1){
                            $dataitem = array(
                                'service_id'			    => $fields['service_id'],
                                'service_term_no'			=> $val['service_term_no'],
                                'service_term_description'	=> $val['service_term_description'],
                                'service_term_token'	    => $fields['service_token_edit'].$val['record_id']
                            );
        
                            $service_item_token = CoreServiceTerm::select('service_term_token')
                                ->where('service_term_token', '=', $dataitem['service_term_token'])
                                ->count();
        
                            if($service_item_token == 0){
                                if(CoreServiceTerm::create($dataitem)){	
        
                                    $msg = "Edit Data Layanan Berhasil";
                                    continue;
                                } else {
                                    $msg = "Edit Data Layanan Gagal";
                                    return redirect('/service/edit/'.$fields['service_id'])->with('msg',$msg);
                                    break;
                                }
                            }
                        } else if ($val['item_status'] == 2){
                            $item                   = CoreServiceTerm::findOrFail($val['record_id']);
                            $item->data_state       = 2;
                            $item->updated_id       = Auth::id();

                            if($item->save()){	
                                $msg = "Edit Data Layanan Berhasil";
                                continue;
                            } else {
                                $msg = "Edit Data Layanan Gagal";
                                return redirect('/service/edit/'.$fields['service_id'])->with('msg',$msg);
                                break;
                            }
                        }
                        
                    }

                    foreach($session_coreserviceparameter as $key => $val){
                        if ($val['item_status'] == 1){
                            $dataitem = array(
                                'service_id'			        => $fields['service_id'],
                                'service_parameter_no'			=> $val['service_parameter_no'],
                                'service_parameter_description'	=> $val['service_parameter_description'],
                                'service_parameter_token'	    => $fields['service_token_edit'].$val['record_id']
                            );
        
                            $service_item_token = CoreServiceParameter::select('service_parameter_token')
                                ->where('service_parameter_token', '=', $dataitem['service_parameter_token'])
                                ->count();
        
                            if($service_item_token == 0){
                                if(CoreServiceParameter::create($dataitem)){	
        
                                    $msg = "Edit Data Layanan Berhasil";
                                    continue;
                                } else {
                                    $msg = "Edit Data Layanan Gagal";
                                    return redirect('/service/edit/'.$fields['service_id'])->with('msg',$msg);
                                    break;
                                }
                            }
                        } else if ($val['item_status'] == 2){
                            $item                   = CoreServiceParameter::findOrFail($val['record_id']);
                            $item->data_state       = 2;
                            $item->updated_id       = Auth::id();

                            if($item->save()){	
                                $msg = "Edit Data Layanan Berhasil";
                                continue;
                            } else {
                                $msg = "Edit Data Layanan Gagal";
                                return redirect('/service/edit/'.$fields['service_id'])->with('msg',$msg);
                                break;
                            }
                        }
                        
                    }
                    Session::forget('data_coreserviceterm');
                    Session::forget('data_coreserviceterm_first');
                    Session::forget('data_coreserviceparameter');
                    Session::forget('data_coreserviceparameter_first');
                    Session::forget('sess_servicetoken');
                    $msg = "Edit Layanan Berhasil";

                    return redirect('/service/edit/'.$fields['service_id'])->with('msg',$msg);
                }else{
                    $msg = "Edit Layanan Gagal";
                    return redirect('/service/edit/'.$fields['service_id'])->with('msg',$msg);
                }
            } else {
                $username = User::select('name')->where('user_id','=',Auth::id())->first();

                $this->set_log(Auth::id(), $username['name'],'1089','Application.CoreService.processEditCoreService',$username['name'],'Edit Core Service');

                foreach($session_coreserviceterm as $key => $val){
                    if ($val['item_status'] == 1){
                        $dataitem = array(
                            'service_id'			    => $fields['service_id'],
                            'service_term_no'			=> $val['service_term_no'],
                            'service_term_description'	=> $val['service_term_description'],
                            'service_term_token'	    => $fields['service_token_edit'].$val['record_id']
                        );
    
                        $service_item_token = CoreServiceTerm::select('service_term_token')
                            ->where('service_term_token', '=', $dataitem['service_term_token'])
                            ->count();
    
                        if($service_item_token == 0){
                            if(CoreServiceTerm::create($dataitem)){	
    
                                $msg = "Edit Data Layanan Berhasil";
                                continue;
                            } else {
                                $msg = "Edit Data Layanan Gagal";
                                return redirect('/service/edit/'.$fields['service_id'])->with('msg',$msg);
                                break;
                            }
                        }
                    } else if ($val['item_status'] == 2){
                        $item                   = CoreServiceTerm::findOrFail($val['record_id']);
                        $item->data_state       = 2;
                        $item->updated_id       = Auth::id();

                        if($item->save()){	
                            $msg = "Edit Data Layanan Berhasil";
                            continue;
                        } else {
                            $msg = "Edit Data Layanan Gagal";
                            return redirect('/service/edit/'.$fields['service_id'])->with('msg',$msg);
                            break;
                        }
                    }
                    
                }

                foreach($session_coreserviceparameter as $key => $val){
                    if ($val['item_status'] == 1){
                        $dataitem = array(
                            'service_id'			        => $fields['service_id'],
                            'service_parameter_no'			=> $val['service_parameter_no'],
                            'service_parameter_description'	=> $val['service_parameter_description'],
                            'service_parameter_token'	    => $fields['service_token_edit'].$val['record_id']
                        );
    
                        $service_item_token = CoreServiceParameter::select('service_parameter_token')
                            ->where('service_parameter_token', '=', $dataitem['service_parameter_token'])
                            ->count();
    
                        if($service_item_token == 0){
                            if(CoreServiceParameter::create($dataitem)){	
    
                                $msg = "Edit Data Layanan Berhasil";
                                continue;
                            } else {
                                $msg = "Edit Data Layanan Gagal";
                                return redirect('/service/edit/'.$fields['service_id'])->with('msg',$msg);
                                break;
                            }
                        }
                    } else if ($val['item_status'] == 2){
                        $item                   = CoreServiceParameter::findOrFail($val['record_id']);
                        $item->data_state       = 2;
                        $item->updated_id       = Auth::id();

                        if($item->save()){	
                            $msg = "Edit Data Layanan Berhasil";
                            continue;
                        } else {
                            $msg = "Edit Data Layanan Gagal";
                            return redirect('/service/edit/'.$fields['service_id'])->with('msg',$msg);
                            break;
                        }
                    }
                    
                }
                Session::forget('data_coreserviceterm');
                Session::forget('data_coreserviceterm_first');
                Session::forget('data_coreserviceparameter');
                Session::forget('data_coreserviceparameter_first');
                Session::forget('sess_servicetoken');
                $msg = "Edit Layanan Berhasil";

                return redirect('/service/edit/'.$fields['service_id'])->with('msg',$msg);
            }
        } else {
            $msg = "Data Layanan Kosong";
            Session::forget('data_coreserviceterm');
            return redirect('/service/edit/'.$fields['service_id'])->with('msg',$msg);
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
}
