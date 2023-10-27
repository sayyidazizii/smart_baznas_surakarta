<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\PublicController;
use App\Providers\RouteServiceProvider;
use App\Models\CoreKecamatan;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CoreKecamatanController extends PublicController
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

    public function index()
    {
        Session::forget('sess_kecamatantoken');
        Session::forget('data_corekecamatan');

        $corekecamatan = CoreKecamatan::where('data_state', 0)
        ->get();

        return view('content/CoreKecamatan/ListCoreKecamatan',compact('corekecamatan'));
    }

    public function addCoreKecamatan(Request $request)
    {
        $kecamatan_token		= Session::get('sess_kecamatantoken');

        if (empty($kecamatan_token)){
            $kecamatan_token = md5(date("YmdHis"));
            Session::put('sess_kecamatantoken', $kecamatan_token);
        }

        $kecamatan_token	= Session::get('sess_kecamatantoken');
        $corekecamatan      = Session::get('data_corekecamatan');

        return view('content/CoreKecamatan/FormAddCoreKecamatan', compact('kecamatan_token', 'corekecamatan'));
    }

    public function addReset()
    {
        Session::forget('sess_kecamatantoken');
        Session::forget('data_corekecamatan');

        return redirect('/kecamatan/add');
    }

    public function processAddCoreKecamatan(Request $request)
    {
        $fields = $request->validate([
            'kecamatan_name' => 'required',
        ]);
        
        $data = array(
            'kecamatan_name'    => $fields['kecamatan_name'], 
            'kecamatan_token'   => $request->kecamatan_token, 
            'created_id'        => Auth::id(),
            'data_state'        => 0
        );

        $kecamatan_token    = CoreKecamatan::select('kecamatan_token')
        ->where('kecamatan_token', '=', $data['kecamatan_token'])
        ->get();

        if(count($kecamatan_token) == 0){
            if(CoreKecamatan::create($data)){
                $this->set_log(Auth::id(), Auth::user()->name, '1089', 'Application.CoreKecamatan.processAddCoreKecamatan', Auth::user()->name, 'Add Core Service');

                $msg = 'Tambah Data Kecamatan Berhasil';

                Session::forget('sess_kecamatantoken');
                Session::forget('data_corekecamatan');
                return redirect('/kecamatan/add')->with('msg',$msg);
            } else {
                $msg = 'Tambah Data Kecamatan Gagal';
                return redirect('/kecamatan/add')->with('msg',$msg);
            }
        } else {
            $msg = 'Tambah Data Kecamatan Gagal - Data Kecamatan Sudah Ada';
            return redirect('/kecamatan/add')->with('msg',$msg);
        }
        
    }

    public function editCoreKecamatan($kecamatan_id)
    {
        $corekecamatan = CoreKecamatan::where('kecamatan_id',$kecamatan_id)->first();

        return view('content/CoreKecamatan/FormEditCoreKecamatan',compact('corekecamatan'));
    }

    public function processEditCoreKecamatan(Request $request)
    {
        $fields = $request->validate([
            'kecamatan_id'   => 'required',
            'kecamatan_name' => 'required',
        ]);

        $item                   = CoreKecamatan::findOrFail($fields['kecamatan_id']);
        $item->kecamatan_name   = $fields['kecamatan_name'];
        $item->updated_id       = Auth::id();

        if($item->save()){
            $msg = 'Edit Kecamatan Berhasil';
            return redirect('/kecamatan')->with('msg',$msg);
        }else{
            $msg = 'Edit Kecamatan Gagal';
            return redirect('/kecamatan')->with('msg',$msg);
        }
    }

    public function deleteCoreKecamatan($kecamatan_id)
    {
        $item               = CoreKecamatan::findOrFail($kecamatan_id);
        $item->data_state   = 1;
        $item->deleted_id   = Auth::id();
        $item->deleted_at   = date("Y-m-d H:i:s");
        if($item->save())
        {
            $msg = 'Hapus Kecamatan Berhasil';
        }else{
            $msg = 'Hapus Kecamatan Gagal';
        }

        return redirect('/kecamatan')->with('msg',$msg);
    }
}
