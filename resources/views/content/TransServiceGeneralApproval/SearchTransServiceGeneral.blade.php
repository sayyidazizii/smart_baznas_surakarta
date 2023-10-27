@inject('TransServiceGeneral', 'App\Http\Controllers\TransServiceGeneralController')

@extends('adminlte::page')

@section('title', 'Siabas')

@section('content_header')
    
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
        <li class="breadcrumb-item"><a href="{{ url('trans-service-disposition-review') }}">Daftar Persetujuan Surat Umum</a></li>
        <li class="breadcrumb-item active" aria-current="page">Daftar Pengajuan Surat Umum</li>
    </ol>
</nav>

@stop

@section('content')

<h3 class="page-title">
    <b>Daftar Pengajuan Surat Umum</b> <small>Mengelola Pengajuan Surat Umum</small>
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
            Daftar
        </h5>
        <div class="form-actions float-right">
            <button onclick="location.href='{{ url('trans-service-general-approval') }}'" name="Find" class="btn btn-sm btn-info" title="Add Data"><i class="fa fa-angle-left"></i>  Kembali</button>
        </div>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table id="example" style="width:100%" class="table table-striped table-bordered table-hover table-full-width">
                <thead>
                    <tr>
                        <th width="5%" style='text-align:center'>No</th>
                        <th width="20%" style='text-align:center'>Nomor Pengajuan</th>
                        <th width="20%" style='text-align:center'>Tanggal Pengajuan</th>
                        <th width="25%" style='text-align:center'>Nama Instansi</th>
                        <th width="10%" style='text-align:center'>Nomor Whatsapp</th>
                        <th width="10%" style='text-align:center'>Prioritas</th>
                        <th width="10%" style='text-align:center'>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; ?>
                    @foreach($transservicegeneral as $general)
                    <tr>
                        <td style='text-align:center'>{{$no}}</td>
                        <td>{{$general['service_general_no']}}</td>
                        <td>{{$general['created_at']}}</td>
                        <td>{{$general['service_general_agency']}}</td>
                        <td>{{$general['service_general_phone']}}</td>
                        <td>{{$TransServiceGeneral->getPriorityName($general['service_general_priority'])}}</td>
                        <td style='text-align:center'>
                            <a type="button" class="btn btn-outline-primary btn-sm" href="{{ url('/trans-service-general-approval/add/'.$general['service_general_id']) }}"><i class="fa fa-plus"></i></a>
                        </td>
                    </tr>
                    <?php $no++; ?>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>

@stop

@section('footer')
    
@stop

@section('css')
    
@stop

@section('js')
    
@stop