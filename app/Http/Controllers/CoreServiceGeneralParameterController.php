<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\CoreServiceGeneralParameter;
use App\Models\User;
use App\Models\SystemLogUser;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CoreServiceGeneralParameterController extends Controller
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

        $coreservicegeneralparameter = CoreServiceGeneralParameter::where('data_state','=',0)->get();

        return view('content/CoreServiceGeneralParameter/ListCoreServiceGeneralParameter',compact('coreservicegeneralparameter'));
    }

    public function addReset()
    {
        Session::forget('sess_servicetoken');
        Session::forget('data_coreservice');
        Session::forget('data_coreserviceterm');
        Session::forget('data_coreserviceparameter');

        return redirect('/service-general-parameter/add');
    }

    public function addCoreServiceGeneralParameter(Request $request)
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

        return view('content/CoreServiceGeneralParameter/FormAddCoreServiceGeneralParameter',compact('coreservice', 'coreserviceterm', 'service_token', 'coreserviceparameter'));
    }
    

    public function addElementsCoreServiceGeneralParameter(Request $request)
    {
        $data_coreservice[$request->name] = $request->value;

        Session::put('data_coreservice', $data_coreservice);
        
        return redirect('/service-general-parameter/add');
    }

    public function processAddArrayCoreServiceGeneralParameterTerm(Request $request)
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
        
        return redirect('/service-general-parameter/add');
    }

    public function deleteAddArrayCoreServiceGeneralParameterTerm($record_id)
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

        return redirect('/service-general-parameter/add');
    }


    public function processAddArrayCoreServiceGeneralParameter(Request $request)
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
        
        return redirect('/service-general-parameter/add');
    }

    public function deleteAddArrayCoreServiceGeneralParameter($record_id)
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

        return redirect('/service-general-parameter/add');
    }

    public function processAddCoreServiceGeneralParameter(Request $request)
    {

        $fields = $request->validate([
            'service_general_parameter_name'  => 'required',
            'service_general_parameter_no'    => 'required',
            'service_general_parameter_token' => 'required',
        ]);
        
        $data = array(
            'service_general_parameter_name'	=> $fields['service_general_parameter_name'],
            'service_general_parameter_no'	    => $fields['service_general_parameter_no'],
            'service_general_parameter_token'   => $fields['service_general_parameter_token'],
            'data_state' 				        => 0,
            'created_id' 				        => Auth::id(),
            'created_on' 				        => date('Y-m-d H:i:s'),
        );

        $service_general_parameter_token 				    = CoreServiceGeneralParameter::select('service_general_parameter_token')
            ->where('service_general_parameter_token', '=', $data['service_general_parameter_token'])
            ->count();
        
        if($service_general_parameter_token == 0){
            if(CoreServiceGeneralParameter::create($data)){
                $msg = "Tambah Data Isian Surat Umum Berhasil";
                Session::forget('sess_servicetoken');
                return redirect('/service-general-parameter')->with('msg',$msg);
            }else{
                $msg = "Tambah Data Isian Surat Umum Tidak Berhasil";
                return redirect('/service-general-parameter/add')->with('msg',$msg);
            }
        } else {
            $msg = "Tambah Data Isian Surat Umum Berhasil";
            Session::forget('sess_servicetoken');
            return redirect('/service-general-parameter')->with('msg',$msg);
        }
    }

    public function editCoreServiceGeneralParameter($service_general_parameter_id)
    {
        $service_token_edit		= Session::get('sess_servicetoken');

        if (empty($service_token_edit)){
            $service_token_edit = md5(date("YmdHis"));
            Session::put('sess_servicetoken', $service_token_edit);
        }

        $service_general_token_edit		        = Session::get('sess_servicetoken');

        $coreservicegeneralparameter = CoreServiceGeneralParameter::findOrFail($service_general_parameter_id);

        return view('content/CoreServiceGeneralParameter/FormEditCoreServiceGeneralParameter',compact('coreservicegeneralparameter', 'service_general_token_edit'));
    }

    public function editReset($service_id)
    {
        Session::forget('data_coreserviceterm');
        Session::forget('data_coreserviceterm_first');
        Session::forget('data_coreserviceparameter_first');

        return redirect('/service-general-parameter/edit/'.$service_id);
    }

    public function deleteEditArrayCoreServiceGeneralParameter($record_id, $service_id)
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

        return redirect('/service-general-parameter/edit/'.$service_id);
    }

    public function processEditCoreServiceGeneralParameter(Request $request)
    {
        $fields = $request->validate([
            'service_general_parameter_id'          => 'required',
            'service_general_parameter_no'          => 'required',
            'service_general_parameter_name'        => 'required',
            'service_general_parameter_token_edit'  => 'required',
        ]);
        

        $item = CoreServiceGeneralParameter::findOrFail($fields['service_general_parameter_id']);
        $item->service_general_parameter_no         = $fields['service_general_parameter_no'];
        $item->service_general_parameter_name       = $fields['service_general_parameter_name'];
        $item->service_general_parameter_token_edit = $fields['service_general_parameter_token_edit'];
        $item->updated_id                           = Auth::id();

        $service_general_parameter_token_edit       = CoreServiceGeneralParameter::select('service_general_parameter_token_edit')
            ->where('service_general_parameter_token_edit', '=', $fields['service_general_parameter_token_edit'])
            ->count(); 

        if ($service_general_parameter_token_edit == 0){
            if($item->save()){
                $username = User::select('name')->where('user_id','=',Auth::id())->first();

                $this->set_log(Auth::id(), $username['name'],'1089','Application.CoreServiceGeneralParameter.processEditCoreServiceGeneralParameter',$username['name'],'Edit Core Service');
                
                $msg = "Edit Surat Umum Berhasil";
                Session::forget('sess_servicetoken');

                return redirect('/service-general-parameter/edit/'.$fields['service_general_parameter_id'])->with('msg',$msg);
            }else{
                $msg = "Edit Surat Umum Gagal";
                return redirect('/service-general-parameter/edit/'.$fields['service_general_parameter_id'])->with('msg',$msg);
            }
        } else {
            $username = User::select('name')->where('user_id','=',Auth::id())->first();

            $this->set_log(Auth::id(), $username['name'],'1089','Application.CoreServiceGeneralParameter.processEditCoreServiceGeneralParameter',$username['name'],'Edit Core Service');

            $msg = "Edit Surat Umum Berhasil";
            Session::forget('sess_servicetoken');

            return redirect('/service-general-parameter/edit/'.$fields['service_general_parameter_id'])->with('msg',$msg);
        }
    }

    public function deleteCoreServiceGeneralParameter($service_general_parameter_id)
    {
        $item = CoreServiceGeneralParameter::findOrFail($service_general_parameter_id);
        $item->data_state = 1;
        $item->deleted_id = Auth::id();
        $item->deleted_at = date("Y-m-d H:i:s");
        if($item->save())
        {
            $msg = 'Hapus Isian Surat Umum Berhasil';
        }else{
            $msg = 'Hapus Isian Surat Umum Gagal';
        }

        return redirect('/service-general-parameter')->with('msg',$msg);
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
