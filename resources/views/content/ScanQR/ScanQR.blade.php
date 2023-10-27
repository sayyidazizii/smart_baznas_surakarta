@inject('ScanQr', 'App\Http\Controllers\ScanQrController')

@extends('adminlte::page')

@section('title', 'Siabas')

@section('js')
<script>
    
	$(document).ready(function(){
        
        var img = {!! json_encode($img) !!};

        if(!img || img==''){
            img = '';
        }

        code = JSON.parse(img);
        console.log(code);
        
        if(code.qrcode != null){
            document.getElementById("qr_image").src = code.qrcode;
            document.getElementById("success").style.display = "none";
        }else{
            // document.getElementById("reload").style.display = "none";
            document.getElementById("reloadqr").style.display = "none";
        }
    });

    function reloadQR(){
        location.reload();
    }

    function reloadAPI(){
        
        $.ajax({
            type: "POST",
            url : "{{route('reload-scan-qr')}}",
            dataType: "html",
            data: {
                '_token' : '{{csrf_token()}}',
            },
            success: function(return_data){ 
                location.reload();
            },
            error: function(data)
            {
                console.log(data);

            }
        });
    }
</script>
@stop

@section('content_header')
    
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
        <li class="breadcrumb-item active" aria-current="page">Scan Kode QR</li>
    </ol>
</nav>

@stop

@section('content')

<h3 class="page-title">
    <b>Scan Kode QR</b> <small></small>
</h3>
<br/>
@if(session('msg'))
<div class="alert alert-info" role="alert">
    {{session('msg')}}
</div>
@endif 
<div class="card border border-dark">
    <div class="card-header bg-dark clearfix">
        <h5 class="mb-0 float-left">
            Kode
        </h5>
        <div class="form-actions float-right">
        </div>
    </div>

    <div class="card-body">
        <center><img src="{{asset('resources/img/success.png')}}" id="qr_image" style="width: 200px; height: 200px;">
        <br>
        <br>
        <button type="button" class="btn btn-primary" id="reloadqr" onclick="reloadQR()">Minta Ulang Code</button>
        <button type="button" class="btn btn-success" id="success">Connect WA Success</button>
        {{-- <button type="button" class="btn btn-info" id="reload" onclick="reloadAPI()">Reload</button> --}}
        </center>
    </div>
  </div>
</div>

@stop

@section('footer')
    
@stop

@section('css')
    
@stop