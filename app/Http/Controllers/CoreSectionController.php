<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\PublicController;
use App\Providers\RouteServiceProvider;
use App\Models\CoreSection;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CoreSectionController extends PublicController
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
        Session::forget('sess_servicetoken');
        Session::forget('data_coresection');
        $coresection = CoreSection::where('data_state','=',0)->get();

        return view('content/CoreSection/ListCoreSection',compact('coresection'));
    }

    public function addCoreSection(Request $request)
    {
        $section_token		= Session::get('sess_servicetoken');

        if (empty($section_token)){
            $section_token = md5(date("YmdHis"));
            Session::put('sess_servicetoken', $section_token);
        }

        $section_token		= Session::get('sess_servicetoken');
        $coresection        = Session::get('data_coresection');

        return view('content/CoreSection/FormAddCoreSection', compact('section_token', 'coresection'));
    }

    public function addElementsCoreSection(Request $request)
    {
        $data_coresection[$request->name] = $request->value;

        Session::put('data_coresection', $data_coresection);
        
        return redirect('/section/add');
    }

    public function addReset()
    {
        Session::forget('sess_servicetoken');
        Session::forget('data_coresection');

        return redirect('/section/add');
    }

    public function processAddCoreSection(Request $request)
    {
        $fields = $request->validate([
            'section_name' => 'required',
        ]);
        
        $data = array(
            'section_name'              => $fields['section_name'], 
            'section_token'             => $request->section_token, 
            'created_id'                => Auth::id(),
            'data_state'                => 0
        );

        $section_token 				    = CoreSection::select('section_token')
            ->where('section_token', '=', $data['section_token'])
            ->get();

        if(count($section_token) == 0){
            if(CoreSection::create($data)){
                $this->set_log(Auth::id(), Auth::user()->name, '1089', 'Application.CoreSection.processAddCoreSection', Auth::user()->name, 'Add Core Service');

                $msg = 'Tambah Data Bidang Berhasil';

                Session::forget('sess_servicetoken');
                Session::forget('data_coresection');
                return redirect('/section/add')->with('msg',$msg);
            } else {
                $msg = 'Tambah Data Bidang Gagal';
                return redirect('/section/add')->with('msg',$msg);
            }
        } else {
            $msg = 'Tambah Data Bidang Gagal - Data Bidang Sudah Ada';
            return redirect('/section/add')->with('msg',$msg);
        }
        
    }

    public function editCoreSection($section_id)
    {
        $coresection = CoreSection::where('section_id',$section_id)->first();

        return view('content/CoreSection/FormEditCoreSection',compact('coresection'));
    }

    public function processEditCoreSection(Request $request)
    {
        $fields = $request->validate([
            'section_id'   => 'required',
            'section_name' => 'required',
        ]);

        $item                   = CoreSection::findOrFail($fields['section_id']);
        $item->section_name     = $fields['section_name'];
        $item->updated_id       = Auth::id();


        if($item->save()){
            $msg = 'Edit Bidang Berhasil';
            return redirect('/section')->with('msg',$msg);
        }else{
            $msg = 'Edit Bidang Gagal';
            return redirect('/section')->with('msg',$msg);
        }
    }

    public function deleteCoreSection($section_id)
    {
        $item               = CoreSection::findOrFail($section_id);
        $item->data_state   = 1;
        $item->deleted_id   = Auth::id();
        $item->deleted_at   = date("Y-m-d H:i:s");
        if($item->save())
        {
            $msg = 'Hapus Bidang Berhasil';
        }else{
            $msg = 'Hapus Bidang Gagal';
        }

        return redirect('/section')->with('msg',$msg);
    }
}
