@inject('TransServiceDispositionFunds', 'App\Http\Controllers\TransServiceDispositionFundsController')
@inject('TransServiceDisposition', 'App\Http\Controllers\TransServiceDispositionController')

@extends('adminlte::page')

@section('title', 'Siabas')
@section('js')
<script>

    $(document).ready(function(){
        $("#section_id").select2("val", "0");
    });

    function function_elements_add(name, value){
        console.log("name " + name);
        console.log("value " + value);
		$.ajax({
				type: "POST",
				url : "{{route('add-service-elements')}}",
				data : {
                    'name'      : name, 
                    'value'     : value,
                    '_token'    : '{{csrf_token()}}'
                },
				success: function(msg){
			}
		});
	}
</script>
@stop
@section('content_header')
    
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
        <li class="breadcrumb-item"><a href="{{ url('trans-service-disposition-funds') }}">Daftar Pencairan Disposisi Bantuan</a></li>
        <li class="breadcrumb-item active" aria-current="page">Detail Pencairan Disposisi Bantuan</li>
    </ol>
</nav>

@stop

@section('content')

<h3 class="page-title">
    Form Detail Pencairan Disposisi Bantuan
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
                Form Detail
            </h5>
            <div class="float-right">
                <button onclick="location.href='{{ url('trans-service-disposition-funds') }}'" name="Find" class="btn btn-sm btn-info" title="Back"><i class="fa fa-angle-left"></i>  Kembali</button>
            </div>
        </div>

        <form method="post" action="{{route('process-add-service-disposition')}}" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                <br/>
                <div class="row">
                    <h5 class="form-section"><b>Form Disposisi</b></h5>
                </div>
                <hr style="margin:0;">
                <br/>
                <div class="row form-group">
                    <div class="col-md-3">
                        <div class="form-group">
                            <a class="text-dark">Nama Layanan<a class='red'> *</a></a>
                            <input class="form-control input-bb" type="text" name="service_name" id="service_name" value="{{$TransServiceDispositionFunds->getServiceName($servicedisposition['service_id'])}}" onChange="function_elements_add(this.name, this.value);" autocomplete="off" readonly/>
                            <input class="form-control input-bb" type="hidden" name="service_id" id="service_id" value="{{$servicedisposition['service_id']}}" onChange="function_elements_add(this.name, this.value);"/>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <a class="text-dark">NIK Pemohon</a>
                            <input class="form-control input-bb" type="text" name="service_requisition_nik" id="service_requisition_nik" value="{{$servicedisposition['service_requisition_nik']}}" onChange="function_elements_add(this.name, this.value);" autocomplete="off" readonly/>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <a class="text-dark">Nama Pemohon<a class='red'> *</a></a>
                            <input class="form-control input-bb" type="text" name="service_requisition_name" id="service_requisition_name" value="{{$servicedisposition['service_requisition_name']}}" onChange="function_elements_add(this.name, this.value);" autocomplete="off" readonly/>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <a class="text-dark">No Whatsapp Pemohon<a class='red'> *</a></a>
                            <input class="form-control input-bb" type="text" name="service_requisition_phone" id="service_requisition_phone" value="{{$servicedisposition['service_requisition_phone']}}" onChange="function_elements_add(this.name, this.value);" autocomplete="off" readonly/>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <a class="text-dark">Kecamatan<a class='red'> *</a></a>
                            <input class="form-control input-bb" type="text" name="kecamatan_name" id="kecamatan_name" value="{{$TransServiceDisposition->getKecamatanName($servicedisposition['kecamatan_id'])}}" onChange="function_elements_add(this.name, this.value);" autocomplete="off" readonly/>
                            <input class="form-control input-bb" type="hidden" name="kecamatan_id" id="kecamatan_id" value="{{$servicedisposition['kecamatan_id']}}" onChange="function_elements_add(this.name, this.value);" autocomplete="off" readonly/>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <a class="text-dark">Kelurahan<a class='red'> *</a></a>
                            <input class="form-control input-bb" type="text" name="kelurahan_name" id="kelurahan_name" value="{{$TransServiceDisposition->getKelurahanName($servicedisposition['kelurahan_id'])}}" onChange="function_elements_add(this.name, this.value);" autocomplete="off" readonly/>
                            <input class="form-control input-bb" type="hidden" name="kelurahan_id" id="kelurahan_id" value="{{$servicedisposition['kelurahan_id']}}" onChange="function_elements_add(this.name, this.value);" autocomplete="off" readonly/>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <a class="text-dark">Alamat Pemohon</a>
                            <textarea class="form-control input-bb" type="text" name="service_requisition_address" id="service_requisition_address" autocomplete="off" readonly>{{$servicedisposition['service_requisition_address']}}</textarea>
                        </div>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-md-6">	
                        <div class="form-group">	
                            <a class="text-dark">Bagian<a class='red'> *</a></a>
                            <input class="form-control input-bb" type="text" name="section_id" id="section_id" value="{{$TransServiceDispositionFunds->getSectionName($servicedisposition['section_id'])}}" onChange="function_elements_add(this.name, this.value);" readonly/>
                        </div>
                    </div>
                    <div class="col-md-6">	
                        <div class="form-group">	
                            <a class="text-dark">Jumlah<a class='red'> *</a></a>
                            <input class="form-control input-bb" type="text" name="section_id" id="section_id" value="{{number_format($servicedisposition['service_disposition_amount'], 2)}}" onChange="function_elements_add(this.name, this.value);" readonly/>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">	
                            <a class="text-dark">Surat SK<a class='red'> *</a></a>
                            <br>
                            <a href='{{url('/trans-service-disposition-review/download-sk/'.$servicedisposition['service_disposition_id'])}}' class='btn btn-primary' target="_blank"> Download</a>  
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">	
                            <a class="text-dark">Surat Permohonan Pencairan<a class='red'> *</a></a>
                            <br>
                            <a href='{{url('/trans-service-disposition-review/download-fa/'.$servicedisposition['service_disposition_id'])}}' class='btn btn-primary' target="_blank"> Download</a>  
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">	
                            <a class="text-dark">Surat Perintah Pencairan<a class='red'> *</a></a>
                            <br>
                            <a href='{{url('/trans-service-disposition-review/download-fo/'.$servicedisposition['service_disposition_id'])}}' class='btn btn-primary' target="_blank"> Download</a>  
                        </div>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-md-12">
                        <div class="form-group">	
                            <a class="text-dark">Keterangan</a>
                            <textarea class="form-control input-bb" name="service_disposition_remark" id="service_disposition_remark" readonly>Keterangan Bagian SDM, Umum, Administrasi : {{$servicedisposition['service_disposition_remark']}}&#13;&#10;Keterangan {{$TransServiceDispositionFunds->getSectionName($servicedisposition['section_id'])}} : {{$servicedisposition['approved_remark']}}</textarea>
                        </div>
                    </div>
                </div>

                <br/>
                <div class="row">
                    <h5 class="form-section"><b>Syarat Dan Ketentuan</b></h5>
                </div>
                <hr style="margin:0;">
                <br/>
                <div class="table-responsive">
                    <table class="table table-bordered table-advance table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th style='text-align:center' width='5%'>No</th>
                                <th style='text-align:center' width='80%'>Deskripsi</th>
                                <th style='text-align:center' width='5%'>Status</th>
                                <th style='text-align:center' width='10%'>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $no =1;
                                foreach ($servicedispositionterm AS $key => $val){
                                    echo"
                                        <tr>
                                            <td style='text-align  : center !important;'>".$val['service_term_no']."</td>
                                            <td style='text-align  : left !important;'>".$val['service_term_description']."</td>";?>
                                            
                                            <td style='text-align  : center'>
                                                <?php if($val['service_disposition_term_status']==1){ ?>
                                                    <i class="fa fa-check"></i>
                                                <?php } ?>
                                            </td>

                                            <td style='text-align  : center'>
                                                <?php if($val['service_disposition_term_value']!=''){ ?>
                                                    <a href='#showimage_{{$val['service_disposition_term_id']}}' data-toggle='modal' class='btn-sm btn-primary'> Open</a>   
                                                    <a href='{{url('/trans-service-disposition/download-term/'.$servicedisposition['service_id'].'/'.$val['service_disposition_term_id'])}}' class='btn-sm btn-info' target="_blank"> Download</a>  
                                                <?php }
                                        echo "
                                            </td>
                                        </tr>
                                    ";
                                    $no++;
                                }
                            ?>
                        </tbody>
                    </table>
                </div>

                <br/>
                <div class="row">
                    <h5 class="form-section"><b>Formulir</b></h5>
                </div>
                <hr style="margin:0;">
                <br/>
                <?php foreach($servicedispositionparameter as $key => $parameter){?>
                    <div class="row form-group">
                        <div class="col-md-12">
                            <div class="form-group">
                                <a class="text-dark">{{$parameter['service_parameter_no'].'. '.$parameter['service_parameter_description']}}</a>
                                <textarea class="form-control input-bb" type="text" name="parameter_{{$parameter['service_parameter_id']}}" id="parameter_{{$parameter['service_parameter_id']}}"autocomplete="off" readonly>{{$parameter['service_disposition_parameter_value']}}</textarea>
                            </div>
                        </div>
                    </div>
                <?php }?>
            </div>
        </div>
    </div>
    </div>
    <br>
    <br>
    <br>                            
</form>



<?php foreach($servicedispositionterm as $key => $val){
    if($val['service_disposition_term_value']!=''){
?>
    <div class="modal fade bs-modal-lg" id="showimage_{{$val['service_disposition_term_id']}}" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header"  style='text-align:left !important'>
                    <h4>Gambar</h4>
                </div>
                <div class="modal-body">
                    <div class="form-body form">
                        <div class="table-responsive" style="text-align : center;">
                            <img src="{{asset('storage/term/'.$servicedisposition['service_id'].'/'.$val['service_disposition_term_value'])}}" style="height:800px; width:700px">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php 
        }
    }
?>



@stop

@section('footer')
    
@stop

@section('css')
    
@stop