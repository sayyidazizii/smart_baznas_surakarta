@extends('adminlte::page')

@section('title', 'PTSP Baznas Surakarta')    
@section('js')
@stop

@section('content_header')
    
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
        <li class="breadcrumb-item"><a href="{{ url('inv-item') }}">Daftar Pesan Notifikasi</a></li>
        <li class="breadcrumb-item active" aria-current="page">Edit Pesan Notifikasi</li>
    </ol>
</nav>

@stop

@section('content')

<h3 class="page-title">
    Form Edit Pesan Notifikasi
</h3>
<br/>
@if(session('msg'))
<div class="alert alert-info" role="alert">
    {{session('msg')}}
</div>
@endif
    <div class="card border border-dark">
    <div class="card-header border-dark bg-dark">
        <h5 class="mb-0 float-left">
            Form Edit
        </h5>
        <div class="float-right">
            <button onclick="location.href='{{ url('messages') }}'" name="Find" class="btn btn-sm btn-info" title="Back"><i class="fa fa-angle-left"></i>  Kembali</button>
        </div>
    </div>

    <form method="post" action="{{route('process-edit-messages')}}" enctype="multipart/form-data">
        @csrf
        <div class="card-body">
            <div class="row form-group">
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Waktu Notifikasi<a class='red'> *</a></a>
                        <input class="form-control input-bb" type="text" name="messages_name" id="messages_name" value="{{$coremessages['messages_name']}}" autocomplete="off" readonly/>

                        <input class="form-control input-bb" type="hidden" name="messages_id" id="messages_id" value="{{$coremessages['messages_id']}}"/>
                    </div>
                </div>
            </div>
            <div class="row form-group">
                <div class="col-md-12">
                    <div class="form-group">
                        <a class="text-dark">Text Pesan Notifikasi<a class='red'> *</a></a>
                        <textarea class="form-control input-bb" type="text" name="messages_text" id="messages_text" value="" autocomplete="off">{{$coremessages['messages_text']}}</textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer text-muted">
            <div class="form-actions float-right">
                <button type="reset" name="Reset" class="btn btn-danger" onClick="window.location.reload();"><i class="fa fa-times"></i> Batal</button>
                <button type="submit" name="Save" class="btn btn-primary" title="Save"><i class="fa fa-check"></i> Simpan</button>
            </div>
        </div>
    </div>
    </div>
</form>

@stop

@section('footer')
    
@stop

@section('css')
    
@stop