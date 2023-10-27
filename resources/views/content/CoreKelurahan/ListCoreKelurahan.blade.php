@inject('CoreSection', 'App\Http\Controllers\CoreSectionController')

@extends('adminlte::page')

@section('title', 'Siabas')

@section('content_header')
    
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
        <li class="breadcrumb-item active" aria-current="page">Daftar Kelurahan</li>
    </ol>
</nav>

@stop

@section('content')

<h3 class="page-title">
    <b>Daftar Kelurahan</b> <small>Mengelola Kelurahan</small>
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
            <button onclick="location.href='{{ url('kelurahan/add') }}'" name="Find" class="btn btn-sm btn-info" title="Add Data"><i class="fa fa-plus"></i> Tambah Kelurahan Baru</button>
        </div>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table id="example" style="width:100%" class="table table-striped table-bordered table-hover table-full-width">
                <thead>
                    <tr>
                        <th width="5%" style='text-align:center'>No</th>
                        <th width="40%" style='text-align:center'>Nama Kecamatan</th>
                        <th width="45%" style='text-align:center'>Nama Kelurahan</th>
                        <th width="10%" style='text-align:center'>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; ?>
                    @foreach($corekelurahan as $kelurahan)
                    <tr>
                        <td style='text-align:center'>{{$no}}</td>
                        <td>{{$kelurahan['kecamatan_name']}}</td>
                        <td>{{$kelurahan['kelurahan_name']}}</td>
                        <td class="">
                            <a type="button" class="btn btn-outline-warning btn-sm" href="{{ url('/kelurahan/edit/'.$kelurahan['kelurahan_id']) }}">Edit</a>
                            <a type="button" class="btn btn-outline-danger btn-sm" href="{{ url('/kelurahan/delete-kelurahan/'.$kelurahan['kelurahan_id']) }}">Hapus</a>
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