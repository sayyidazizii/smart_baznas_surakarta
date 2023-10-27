<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\PublicController;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use GuzzleHttp\Exception\BadResponseException;

class ScanQRController extends PublicController
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
        $client = new \GuzzleHttp\Client();
        $token ='xrOAoiED3BBrdu7aSscudCZ1zN3GC9TxX1WP3RXrnUlwQ77W1R';
        try{
            $response = $client->request('POST', 'https://app.ruangwa.id/api/qrcode', [
                'headers' => [
                    'Accept'        => 'application/json',
                    'Content-Type'  => 'application/x-www-form-urlencoded',
                ],
                'form_params' => [
                    'token'  => 'xrOAoiED3BBrdu7aSscudCZ1zN3GC9TxX1WP3RXrnUlwQ77W1R',
                ]
            ]);

            $response   = $response->getBody()->getContents();

            $img        = $response;
            return view('content/ScanQR/ScanQR',compact('img'));
        } catch (BadResponseException $exception) {
            $response = $exception->getResponse();
            $jsonBody = (string) $response->getBody();
            return redirect()->back()->with('alert','Terjadi Masalah Pada Server Whatssapp');
        }
    }

    public function reloadAPI()
    {
        $client = new \GuzzleHttp\Client();
        $token ='xrOAoiED3BBrdu7aSscudCZ1zN3GC9TxX1WP3RXrnUlwQ77W1R';
        $response = $client->request('POST', 'https://app.ruangwa.id/api/reload', [
            'headers' => [
                'Accept'        => 'application/json',
                'Content-Type'  => 'application/x-www-form-urlencoded',
            ],
            'form_params' => [
                'token'  => 'xrOAoiED3BBrdu7aSscudCZ1zN3GC9TxX1WP3RXrnUlwQ77W1R',
            ]
        ]);

        $response   = $response->getBody()->getContents();
        
        $response = $client->request('POST', 'https://app.ruangwa.id/api/qrcode', [
            'headers' => [
                'Accept'        => 'application/json',
                'Content-Type'  => 'application/x-www-form-urlencoded',
            ],
            'form_params' => [
                'token'  => 'xrOAoiED3BBrdu7aSscudCZ1zN3GC9TxX1WP3RXrnUlwQ77W1R',
            ]
        ]);

        $response   = $response->getBody()->getContents();

        $img        = $response;
        return view('content/ScanQR/ScanQR',compact('img'));
    }
}
