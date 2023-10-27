@inject('CoreMessages', 'App\Http\Controllers\CoreMessagesController')

@extends('adminlte::page')

@section('title', 'Siabas')

@section('content_header')
    
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
        <li class="breadcrumb-item active" aria-current="page">Daftar Pesan Notifikasi</li>
    </ol>
</nav>

@stop

@section('content')

<h3 class="page-title">
    <b>Daftar Pesan Notifikasi</b> <small>Mengelola Pesan Notifikasi</small>
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
            {{-- <button onclick="location.href='{{ url('messages/add') }}'" name="Find" class="btn btn-sm btn-info" title="Add Data"><i class="fa fa-plus"></i> Tambah Pesan Notifikasi Baru</button> --}}
        </div>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table id="example" style="width:100%" class="table table-striped table-bordered table-hover table-full-width">
                <thead>
                    <tr>
                        <th width="5%" style='text-align:center'>No</th>
                        <th width="20%" style='text-align:center'>Waktu Notifikasi</th>
                        <th width="55%" style='text-align:center'>Text Pesan Notifikasi</th>
                        <th width="20%" style='text-align:center'>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; ?>
                    @foreach($coremessages as $messages)
                    <tr>
                        <td style='text-align:center'>{{$no}}</td>
                        <td>{{$messages['messages_name']}}</td>
                        <td>{{$messages['messages_text']}}</td>
                        <td style='text-align:center'>
                            <a type="button" class="btn btn-outline-warning btn-sm" href="{{ url('/messages/edit/'.$messages['messages_id']) }}">Edit</a>
                            <?php if($messages['messages_status'] == 1){?>
                                <a type="button" class="btn btn-outline-danger btn-sm" href="{{ url('/messages/non-activate/'.$messages['messages_id']) }}">Non Aktifkan</a>
                            <?php }else{?>
                                <a type="button" class="btn btn-outline-success btn-sm" href="{{ url('/messages/activate/'.$messages['messages_id']) }}">Aktifkan</a>
                            <?php }?>
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