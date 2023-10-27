@inject('TransServiceDispositionFunds', 'App\Http\Controllers\TransServiceDispositionFundsController')

@extends('adminlte::page')

@section('title', 'Siabas')

@section('content_header')
    
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
        <li class="breadcrumb-item active" aria-current="page">Daftar Pencairan Disposisi Bantuan</li>
    </ol>
</nav>

@stop

@section('content')

<h3 class="page-title">
    <b>Daftar Pencairan Disposisi Bantuan</b> <small>Mengelola Pencairan Disposisi Bantuan</small>
</h3>
<br/>
<div id="accordion">
    <form  method="post" action="{{route('filter-service-disposition-funds')}}" enctype="multipart/form-data">
    @csrf
        <div class="card border border-dark">
        <div class="card-header bg-dark" id="headingOne" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
            <h5 class="mb-0">
                Filter
            </h5>
        </div>
    
        <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
            <div class="card-body">
                <div class = "row">
                    <div class = "col-md-6">
                        <div class="form-group form-md-line-input">
                            <section class="control-label">Tanggal Mulai
                                <span class="required text-danger">
                                    *
                                </span>
                            </section>
                            <input type ="date" class="form-control form-control-inline input-medium date-picker input-date" data-date-format="dd-mm-yyyy" type="text" name="start_date" id="start_date" onChange="function_elements_add(this.name, this.value);" value="{{$start_date}}" style="width: 15rem;"/>
                        </div>
                    </div>

                    <div class = "col-md-6">
                        <div class="form-group form-md-line-input">
                            <section class="control-label">Tanggal Akhir
                                <span class="required text-danger">
                                    *
                                </span>
                            </section>
                            <input type ="date" class="form-control form-control-inline input-medium date-picker input-date" data-date-format="dd-mm-yyyy" type="text" name="end_date" id="end_date" onChange="function_elements_add(this.name, this.value);" value="{{$end_date}}" style="width: 15rem;"/>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer text-muted">
                <div class="form-actions float-right">
                    <button type="reset" name="Reset" class="btn btn-danger" onClick="window.location.reload();"><i class="fa fa-times"></i> Batal</button>
                    <button type="submit" name="Find" class="btn btn-primary" title="Search Data"><i class="fa fa-search"></i> Cari</button>
                </div>
            </div>
        </div>
        </div>
    </form>
</div>
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
            <button onclick="location.href='{{ url('trans-service-disposition-funds/search') }}'" name="Find" class="btn btn-sm btn-info" title="Add Data"><i class="fa fa-plus"></i> Tambah Pencairan Disposisi Bantuan Baru</button>
        </div>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table id="example" style="width:100%" class="table table-striped table-bordered table-hover table-full-width">
                <thead>
                    <tr>
                        <th width="5%" style='text-align:center'>No</th>
                        <th width="10%" style='text-align:center'>Nomor Pengajuan</th>
                        <th width="5%" style='text-align:center'>Nomor Register</th>
                        <th width="10%" style='text-align:center'>Tanggal Pengajuan</th>
                        <th width="15%" style='text-align:center'>Nama Pemohon</th>
                        <th width="15%" style='text-align:center'>Nama Layanan</th>
                        <th width="15%" style='text-align:center'>Bagian</th>
                        <th width="10%" style='text-align:center'>Jumlah</th>
                        <th width="10%" style='text-align:center'>Status</th>
                        <th width="5%" style='text-align:center'>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; ?>
                    @foreach($transservicedisposition as $service)
                    <tr>
                        <td style='text-align:center'>{{$no}}</td>
                        <td>{{$service['service_requisition_no']}}</td>
                        <td>{{$service['service_register_no']}}</td>
                        <td>{{$service['created_at']}}</td>
                        <td>{{$service['service_requisition_name']}}</td>
                        <td>{{$TransServiceDispositionFunds->getServiceName($service['service_id'])}}</td>
                        <td>{{$TransServiceDispositionFunds->getSectionName($service['section_id'])}}</td>
                        <td style='text-align: right'>{{number_format($service['service_disposition_amount'], 2)}}</td>
                        <td class="">
                            <?php if($service['service_disposition_funds_status'] == 1){ ?>
                                <a>Dana Sudah Dicairkan</a>
                            <?php }else if($service['service_disposition_funds_status'] == 2){ ?>
                                <a>Dana Sudah Diberikan</a>
                            <?php } ?>
                        </td>
                        <td class="">
                            {{-- <a type="button" class="btn btn-outline-warning btn-sm" href="{{ url('/trans-service-disposition-funds/print-warrant/'.$service['service_disposition_id']) }}">Surat Perintah</a> --}}
                            <a type="button" class="btn btn-outline-info btn-sm" href="{{ url('/trans-service-disposition-funds/detail/'.$service['service_disposition_id']) }}">Detail</a>
                            <a type="button" class="btn btn-outline-success btn-sm" href="{{ url('/trans-service-disposition-funds/print-reg/'.$service['service_disposition_id']) }}">Print No Reg</a>
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