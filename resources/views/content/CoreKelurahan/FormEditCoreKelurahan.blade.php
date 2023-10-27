@extends('adminlte::page')

@section('title', 'Siabas')    
@section('js')
@stop

@section('content_header')
    
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
        <li class="breadcrumb-item"><a href="{{ url('inv-item') }}">Daftar Kelurahan</a></li>
        <li class="breadcrumb-item active" aria-current="page">Edit Kelurahan</li>
    </ol>
</nav>

@stop

@section('content')

<h3 class="page-title">
    Form Edit Kelurahan
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
            <button onclick="location.href='{{ url('kelurahan') }}'" name="Find" class="btn btn-sm btn-info" title="Back"><i class="fa fa-angle-left"></i>  Kembali</button>
        </div>
    </div>

    <form method="post" action="{{route('process-edit-kelurahan')}}" enctype="multipart/form-data">
        @csrf
        <div class="card-body">
            <div class="row form-group">
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Nama Kelurahan<a class='red'> *</a></a>
                        <input class="form-control input-bb" type="text" name="kelurahan_name" id="kelurahan_name" value="{{$corekelurahan['kelurahan_name']}}" autocomplete="off"/>

                        <input class="form-control input-bb" type="hidden" name="kelurahan_id" id="kelurahan_id" value="{{$corekelurahan['kelurahan_id']}}"/>
                    </div>
                </div>
                <div class="col-md-6">	
                    <div class="form-group">	
                        <a class="text-dark">Kecamatan<a class='red'> *</a></a>
                        {!! Form::select('kecamatan_id',  $corekecamatan, $corekelurahan['kecamatan_id'], ['class' => 'selection-search-clear select-form', 'id' => 'kecamatan_id']) !!}
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