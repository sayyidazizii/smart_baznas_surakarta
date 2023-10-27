@inject('TransServiceDisposition', 'App\Http\Controllers\TransServiceDispositionController')

@extends('adminlte::page')

@section('title', 'Siabas')
@section('js')
<script>

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
        <li class="breadcrumb-item active" aria-current="page">Edit Formulir Bantuan</li>
    </ol>
</nav>

@stop

@section('content')

<h3 class="page-title">
    Form Edit Formulir Bantuan
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
                <button onclick="location.href='{{ url('trans-service-disposition-approval/search') }}'" name="Find" class="btn btn-sm btn-info" title="Back"><i class="fa fa-angle-left"></i>  Kembali</button>
            </div>
        </div>

        <form method="post" action="{{route('process-edit-service-disposition-approval')}}" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                <div class="row form-group">
                    <div class="col-md-3">
                        <div class="form-group">
                            <a class="text-dark">Nama Layanan<a class='red'> *</a></a>
                            <input class="form-control input-bb" type="text" name="service_name" id="service_name" value="{{$TransServiceDisposition->getServiceName($servicedisposition['service_id'])}}" onChange="function_elements_add(this.name, this.value);" autocomplete="off" readonly/>

                            <input class="form-control input-bb" type="hidden" name="service_id" id="service_id" value="{{$servicedisposition['service_id']}}" onChange="function_elements_add(this.name, this.value);" autocomplete="off" readonly/>

                            <input class="form-control input-bb" type="hidden" name="service_disposition_token_edit" id="service_disposition_token_edit" value="{{$service_disposition_token_edit}}"/>

                            <input class="form-control input-bb" type="hidden" name="service_requisition_no" id="service_requisition_no" value="{{$servicedisposition['service_requisition_no']}}"/>

                            <input class="form-control input-bb" type="hidden" name="service_disposition_id" id="service_disposition_id" value="{{$service_disposition_id}}"/>
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
                    <div class="col-md-12">
                        <div class="form-group">
                            <a class="text-dark">Alamat Pemohon</a>
                            <textarea class="form-control input-bb" type="text" name="service_requisition_address" id="service_requisition_address" autocomplete="off" readonly>{{$servicedisposition['service_requisition_address']}}</textarea>
                        </div>
                    </div>
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
                                <textarea class="form-control input-bb" type="text" name="parameter_{{$parameter['service_parameter_id']}}" id="parameter_{{$parameter['service_parameter_id']}}"autocomplete="off">{{$parameter['service_disposition_parameter_value']}}</textarea>
                            </div>
                        </div>
                    </div>
                <?php }?>
            </div>
        
            <div class="card-footer text-muted">
                <div class="form-actions float-right">
                    <button type="reset" name="Reset" class="btn btn-danger" onClick="reset_add();"><i class="fa fa-times"></i> Batal</button>
                    <button type="submit" name="Save" class="btn btn-primary" title="Save"><i class="fa fa-check"></i> Simpan</button>
                </div>
            </div>
        </div>
    </div>
    </div>
    <br>
    <br>
    <br>                            
</form>

@stop

@section('footer')
    
@stop

@section('css')
    
@stop