<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\PublicController;
use App\Providers\RouteServiceProvider;
use App\Models\CoreKecamatan;
use App\Models\CoreKelurahan;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CoreKelurahanController extends PublicController
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
        Session::forget('sess_kelurahantoken');
        Session::forget('data_corekelurahan');

        $corekelurahan = CoreKelurahan::select('core_kecamatan.kecamatan_name', 'core_kelurahan.kelurahan_id', 'core_kelurahan.kelurahan_name')
        ->join('core_kecamatan', 'core_kecamatan.kecamatan_id', '=', 'core_kelurahan.kecamatan_id')
        ->where('core_kelurahan.data_state', 0)
        ->get();

        return view('content/CoreKelurahan/ListCoreKelurahan',compact('corekelurahan'));
    }

    public function addCoreKelurahan(Request $request)
    {
        $kelurahan_token		= Session::get('sess_kelurahantoken');

        if (empty($kelurahan_token)){
            $kelurahan_token = md5(date("YmdHis"));
            Session::put('sess_kelurahantoken', $kelurahan_token);
        }

        $kelurahan_token	= Session::get('sess_kelurahantoken');
        $corekelurahan      = Session::get('data_corekelurahan');
        $corekecamatan      = CoreKecamatan::where('data_state', 0)
        ->pluck('kecamatan_name', 'kecamatan_id');

        return view('content/CoreKelurahan/FormAddCoreKelurahan', compact('kelurahan_token', 'corekelurahan', 'corekecamatan'));
    }

    public function addReset()
    {
        Session::forget('sess_kelurahantoken');
        Session::forget('data_corekelurahan');

        return redirect('/kelurahan/add');
    }

    public function processAddCoreKelurahan(Request $request)
    {
        $fields = $request->validate([
            'kelurahan_name'    => 'required',
            'kecamatan_id'      => 'required',
        ]);
        
        $data = array(
            'kelurahan_name'    => $fields['kelurahan_name'], 
            'kecamatan_id'      => $fields['kecamatan_id'], 
            'kelurahan_token'   => $request->kelurahan_token, 
            'created_id'        => Auth::id(),
            'data_state'        => 0
        );

        $kelurahan_token    = CoreKelurahan::select('kelurahan_token')
        ->where('kelurahan_token', '=', $data['kelurahan_token'])
        ->get();

        if(count($kelurahan_token) == 0){
            if(CoreKelurahan::create($data)){
                $this->set_log(Auth::id(), Auth::user()->name, '1089', 'Application.CoreKelurahan.processAddCoreKelurahan', Auth::user()->name, 'Add Core Service');

                $msg = 'Tambah Data Kelurahan Berhasil';

                Session::forget('sess_kelurahantoken');
                Session::forget('data_corekelurahan');
                return redirect('/kelurahan/add')->with('msg',$msg);
            } else {
                $msg = 'Tambah Data Kelurahan Gagal';
                return redirect('/kelurahan/add')->with('msg',$msg);
            }
        } else {
            $msg = 'Tambah Data Kelurahan Gagal - Data Kelurahan Sudah Ada';
            return redirect('/kelurahan/add')->with('msg',$msg);
        }
        
    }

    public function editCoreKelurahan($kelurahan_id)
    {
        $corekelurahan = CoreKelurahan::where('kelurahan_id',$kelurahan_id)->first();
        $corekecamatan = CoreKecamatan::where('data_state', 0)
        ->pluck('kecamatan_name', 'kecamatan_id');

        return view('content/CoreKelurahan/FormEditCoreKelurahan',compact('corekelurahan', 'corekecamatan'));
    }

    public function processEditCoreKelurahan(Request $request)
    {
        $fields = $request->validate([
            'kecamatan_id'   => 'required',
            'kelurahan_id'   => 'required',
            'kelurahan_name' => 'required',
        ]);

        $item                   = CoreKelurahan::findOrFail($fields['kelurahan_id']);
        $item->kelurahan_name   = $fields['kelurahan_name'];
        $item->kecamatan_id     = $fields['kecamatan_id'];
        $item->updated_id       = Auth::id();

        if($item->save()){
            $msg = 'Edit Kelurahan Berhasil';
            return redirect('/kelurahan')->with('msg',$msg);
        }else{
            $msg = 'Edit Kelurahan Gagal';
            return redirect('/kelurahan')->with('msg',$msg);
        }
    }

    public function deleteCoreKelurahan($kelurahan_id)
    {
        $item               = CoreKelurahan::findOrFail($kelurahan_id);
        $item->data_state   = 1;
        $item->deleted_id   = Auth::id();
        $item->deleted_at   = date("Y-m-d H:i:s");
        if($item->save())
        {
            $msg = 'Hapus Kelurahan Berhasil';
        }else{
            $msg = 'Hapus Kelurahan Gagal';
        }

        return redirect('/kelurahan')->with('msg',$msg);
    }
}
