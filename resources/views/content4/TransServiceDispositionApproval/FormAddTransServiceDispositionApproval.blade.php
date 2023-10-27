@inject('TransServiceDispositionApproval', 'App\Http\Controllers\TransServiceDispositionApprovalController')

@extends('adminlte::page')

@section('title', 'SMArT Baznas Surakarta')
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
        <li class="breadcrumb-item"><a href="{{ url('trans-service-disposition-approval') }}">Daftar Persetujuan Disposisi Bantuan</a></li>
        <li class="breadcrumb-item"><a href="{{ url('trans-service-disposition-approval/search') }}">Daftar Disposisi Bantuan</a></li>
        <li class="breadcrumb-item active" aria-current="page">Persetujuan Disposisi Bantuan</li>
    </ol>
</nav>

@stop

@section('content')

<h3 class="page-title">
    Form Persetujuan Disposisi Bantuan
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
                <button onclick="location.href='{{ url('trans-service-disposition-approval/search') }}'" name="Find" class="btn btn-sm btn-info" title="Back"><i class="fa fa-angle-left"></i>  Kembali</button>
            </div>
        </div>

        <form method="post" action="{{route('process-add-service-disposition-approval')}}" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                <br/>
                <div class="row">
                    <h5 class="form-section"><b>Form Persetujuan Disposisi</b></h5>
                </div>
                <hr style="margin:0;">
                <br/>
                <div class="row form-group">
                    <div class="col-md-6">
                        <div class="form-group">
                            <a class="text-dark">Nama Layanan<a class='red'> *</a></a>
                            <input class="form-control input-bb" type="text" name="service_name" id="service_name" value="{{$TransServiceDispositionApproval->getServiceName($servicedisposition['service_id'])}}" onChange="function_elements_add(this.name, this.value);" autocomplete="off" readonly/>
                            <input class="form-control input-bb" type="hidden" name="service_id" id="service_id" value="{{$servicedisposition['service_id']}}" onChange="function_elements_add(this.name, this.value);"/>
                            <input class="form-control input-bb" type="hidden" name="service_disposition_id" id="service_disposition_id" value="{{$service_disposition_id}}" onChange="function_elements_add(this.name, this.value);"/>
                            <input class="form-control input-bb" type="hidden" name="service_disposition_approval_token_edit" id="service_disposition_approval_token_edit" value="{{$service_disposition_approval_token_edit}}"/>
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
                </div>
                <div class="row form-group">
                    <div class="col-md-6">	
                        <div class="form-group">	
                            <a class="text-dark">Bagian<a class='red'> *</a></a>
                            <input class="form-control input-bb" type="text" name="section_id" id="section_id" value="{{$TransServiceDispositionApproval->getSectionName($servicedisposition['section_id'])}}" readonly/>
                        </div>
                    </div>
                </div>
                <div class="row form-group">
                    <div class="col-md-12">
                        <div class="form-group">	
                            <a class="text-dark">Keterangan</a>
                            <textarea class="form-control input-bb" name="service_disposition_remark" id="service_disposition_remark" readonly>{{$servicedisposition['service_disposition_remark']}}</textarea>
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
        
            <div class="card-footer text-muted">
                <div class="form-actions float-right">
                    <a class="btn btn-primary" href='#approve' data-toggle='modal'><i class="fa fa-check"></i> Approve</a>
                </div>
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

<form method="post" action="{{route('process-add-service-disposition-approval')}}" enctype="multipart/form-data">
    @csrf
    <div class="modal fade bs-modal-lg" id="approve" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header"  style='text-align:left !important'>
                    <h4>Approve</h4>
                </div>
                <div class="modal-body">
                    <div class="row form-group">
                        <div class="col-md-12">
                            <div class="form-group">
                                <a class="text-dark">Keterangan Approve <a class="red">*</a></a>
                                <textarea class="form-control input-bb" type="text" name="approved_remark" id="approved_remark" autocomplete="off"></textarea>
                                <input class="form-control input-bb" type="hidden" name="service_disposition_id" id="service_disposition_id" value="{{$service_disposition_id}}" onChange="function_elements_add(this.name, this.value);"/>
                                <input class="form-control input-bb" type="hidden" name="service_disposition_approval_token_edit" id="service_disposition_approval_token_edit" value="{{$service_disposition_approval_token_edit}}"/>
                            </div>
                        </div>
                    </div>
                </div>
            
                <div class="modal-footer text-muted">
                    <div class="form-actions float-right">
                        <button type="submit" name="Save" class="btn btn-primary" title="Save">Simpan</button>
                    </div>
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