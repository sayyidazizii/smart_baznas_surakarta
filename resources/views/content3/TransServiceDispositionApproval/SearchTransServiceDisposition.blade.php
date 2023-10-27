@inject('TransServiceDispositionApproval', 'App\Http\Controllers\TransServiceDispositionApprovalController')

@extends('adminlte::page')

@section('title', 'PTSP Baznas Surakarta')

@section('content_header')
    
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
        <li class="breadcrumb-item"><a href="{{ url('trans-service-disposition-approval') }}">Daftar Persetujuan Disposisi Bantuan</a></li>
        <li class="breadcrumb-item active" aria-current="page">Daftar Disposisi Bantuan</li>
    </ol>
</nav>

@stop

@section('content')

<h3 class="page-title">
    <b>Daftar Disposisi Bantuan</b> <small>Mengelola Disposisi Bantuan</small>
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
            <button onclick="location.href='{{ url('trans-service-disposition-approval') }}'" name="Find" class="btn btn-sm btn-info" title="Add Data"><i class="fa fa-angle-left"></i>  Kembali</button>
        </div>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table id="example" style="width:100%" class="table table-striped table-bordered table-hover table-full-width">
                <thead>
                    <tr>
                        <th width="5%" style='text-align:center'>No</th>
                        <th width="10%" style='text-align:center'>Nomor Pengajuan</th>
                        <th width="15%" style='text-align:center'>Tanggal Pengajuan</th>
                        <th width="15%" style='text-align:center'>Nama Pemohon</th>
                        <th width="45%" style='text-align:center'>Nama Layanan</th>
                        <th width="10%" style='text-align:center'>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; ?>
                    @foreach($transservicedisposition as $disposition)
                    <tr>
                        <td style='text-align:center'>{{$no}}</td>
                        <td>{{$disposition['service_requisition_no']}}</td>
                        <td>{{$disposition['created_at']}}</td>
                        <td>{{$disposition['service_requisition_name']}}</td>
                        <td>{{$disposition['service_name']}}</td>
                        <td style='text-align:center'>
                            <a type="button" class="btn btn-outline-primary btn-sm" href="{{ url('/trans-service-disposition-approval/add/'.$disposition['service_disposition_id']) }}"><i class="fa fa-plus"></i></a>
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