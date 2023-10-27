<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\PublicController;
use App\Providers\RouteServiceProvider;
use App\Models\CoreMessages;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CoreMessagesController extends PublicController
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
        Session::forget('data_coremessages');
        $coremessages = CoreMessages::where('data_state','=',0)->get();

        return view('content/CoreMessages/ListCoreMessages',compact('coremessages'));
    }

    public function addCoreMessages(Request $request)
    {
        $messages_token		= Session::get('sess_servicetoken');

        if (empty($messages_token)){
            $messages_token = md5(date("YmdHis"));
            Session::put('sess_servicetoken', $messages_token);
        }

        $messages_token		= Session::get('sess_servicetoken');
        $coremessages        = Session::get('data_coremessages');

        return view('content/CoreMessages/FormAddCoreMessages', compact('messages_token', 'coremessages'));
    }

    public function addElementsCoreMessages(Request $request)
    {
        $data_coremessages[$request->name] = $request->value;

        Session::put('data_coremessages', $data_coremessages);
        
        return redirect('/messages/add');
    }

    public function addReset()
    {
        Session::forget('sess_servicetoken');
        Session::forget('data_coremessages');

        return redirect('/messages/add');
    }

    public function processAddCoreMessages(Request $request)
    {
        $fields = $request->validate([
            'messages_text' => 'required',
        ]);
        
        $data = array(
            'messages_text'              => $fields['messages_text'], 
            'messages_token'             => $request->messages_token, 
            'created_id'                => Auth::id(),
            'data_state'                => 0
        );

        $messages_token 				    = CoreMessages::select('messages_token')
            ->where('messages_token', '=', $data['messages_token'])
            ->get();

        if(count($messages_token) == 0){
            if(CoreMessages::create($data)){
                $this->set_log(Auth::id(), Auth::user()->name, '1089', 'Application.CoreMessages.processAddCoreMessages', Auth::user()->name, 'Add Core Service');

                $msg = 'Tambah Data Pesan Notifikasi Berhasil';

                Session::forget('sess_servicetoken');
                Session::forget('data_coremessages');
                return redirect('/messages/add')->with('msg',$msg);
            } else {
                $msg = 'Tambah Data Pesan Notifikasi Gagal';
                return redirect('/messages/add')->with('msg',$msg);
            }
        } else {
            $msg = 'Tambah Data Pesan Notifikasi Gagal - Data Pesan Notifikasi Sudah Ada';
            return redirect('/messages/add')->with('msg',$msg);
        }
        
    }

    public function editCoreMessages($messages_id)
    {
        $coremessages = CoreMessages::where('messages_id',$messages_id)->first();

        return view('content/CoreMessages/FormEditCoreMessages',compact('coremessages'));
    }

    public function processEditCoreMessages(Request $request)
    {
        $fields = $request->validate([
            'messages_id'   => 'required',
            'messages_text' => 'required',
        ]);

        $item                   = CoreMessages::findOrFail($fields['messages_id']);
        $item->messages_text     = $fields['messages_text'];
        $item->updated_id       = Auth::id();


        if($item->save()){
            $msg = 'Edit Pesan Notifikasi Berhasil';
            return redirect('/messages')->with('msg',$msg);
        }else{
            $msg = 'Edit Pesan Notifikasi Gagal';
            return redirect('/messages')->with('msg',$msg);
        }
    }

    public function deleteCoreMessages($messages_id)
    {
        $item               = CoreMessages::findOrFail($messages_id);
        $item->data_state   = 1;
        $item->deleted_id   = Auth::id();
        $item->deleted_at   = date("Y-m-d H:i:s");
        if($item->save())
        {
            $msg = 'Hapus Pesan Notifikasi Berhasil';
        }else{
            $msg = 'Hapus Pesan Notifikasi Gagal';
        }

        return redirect('/messages')->with('msg',$msg);
    }

    public function activateCoreMessages($messages_id)
    {
        $messages                   = CoreMessages::findOrFail($messages_id);
        $messages->messages_status  = 1;
        if($messages->save())
        {
            $msg = 'Aktivasi Pesan Notifikasi Berhasil';
        }else{
            $msg = 'Aktivasi Pesan Notifikasi Gagal';
        }

        return redirect('/messages')->with('msg',$msg);
    }

    public function nonActivateCoreMessages($messages_id)
    {
        $messages                   = CoreMessages::findOrFail($messages_id);
        $messages->messages_status  = 0;
        if($messages->save())
        {
            $msg = 'Non Aktivasi Pesan Notifikasi Berhasil';
        }else{
            $msg = 'Non Aktivasi Pesan Notifikasi Gagal';
        }

        return redirect('/messages')->with('msg',$msg);
    }
}
