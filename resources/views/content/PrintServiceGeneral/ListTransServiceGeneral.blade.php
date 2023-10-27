@inject('PrintService', 'App\Http\Controllers\PrintServiceGeneralController')

@extends('adminlte::page')

@section('title', 'Siabas')

@section('js')
<script>
	$(document).ready(function(){
        var section_id        = {!! json_encode($section_id) !!};
        var service_id        = {!! json_encode($service_id) !!};
        
        if(section_id == null){
            $("#section_id").select2("val", "0");
        }
        if(service_id == null){
            $("#service_id").select2("val", "0");
        }
    });
</script>
@stop

@section('content_header')
    
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
        <li class="breadcrumb-item active" aria-current="page">Daftar Print Surat Umum</li>
    </ol>
</nav>

@stop

@section('content')

<h3 class="page-title">
    <b>Daftar Print Surat Umum</b> <small>Mengelola Print Surat Umum</small>
</h3>
<br/>
<div id="accordion">
    <form  method="post" action="{{route('filter-print-service-general')}}" enctype="multipart/form-data">
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
            Daftar Disposisi Bantuan
        </h5>
        <div class="form-actions float-right">
            <button onclick="location.href='{{ url('print-service-general/export-recap') }}'" name="Find" class="btn btn-sm btn-info" title="Add Data"><i class="fa fa-download"></i> Export Rekap</button>
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
                        <th width="10%" style='text-align:center'>Nama Instansi</th>
                        <th width="10%" style='text-align:center'>No Whatsapp</th>
                        <th width="10%" style='text-align:center'>Prioritas</th>
                        <th width="10%" style='text-align:center'>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $no = 1; ?>
                    @foreach($transservicegeneral as $service)
                    <tr>
                        <td style='text-align:center'>{{$no}}</td>
                        <td>{{$service['service_general_no']}}</td>
                        <td>{{$service['created_at']}}</td>
                        <td>{{$service['service_general_agency']}}</td>
                        <td>{{$service['service_general_phone']}}</td>
                        <td>{{$PrintService->getPriorityName($service['service_general_priority'])}}</td>
                        <td style='text-align:center'>
                            <a type="button" class="btn btn-outline-info btn-sm" href="{{ url('/print-service-general/export/'.$service['service_general_id']) }}" target="_blank">Export</a>
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