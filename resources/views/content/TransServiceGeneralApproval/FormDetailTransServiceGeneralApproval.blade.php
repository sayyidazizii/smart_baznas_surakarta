@inject('TransServiceGeneral', 'App\Http\Controllers\TransServiceGeneralController')

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
        <li class="breadcrumb-item"><a href="{{ url('trans-service-general-approval') }}">Daftar Pengajuan Surat Umum</a></li>
        <li class="breadcrumb-item"><a href="{{ url('trans-service-general-approval/search') }}">Daftar Layanan</a></li>
        <li class="breadcrumb-item active" aria-current="page">Detail Pengajuan Surat Umum</li>
    </ol>
</nav>

@stop

@section('content')

<h3 class="page-title">
    Form Detail Pengajuan Surat Umum
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
                <button onclick="location.href='{{ url('trans-service-general-approval') }}'" name="Find" class="btn btn-sm btn-info" title="Back"><i class="fa fa-angle-left"></i>  Kembali</button>
            </div>
        </div>

        <form method="post" action="{{route('process-edit-service-general')}}" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                <div class="row form-group">
                    <div class="col-md-4">
                        <div class="form-group">
                            <a class="text-dark">Nama Instansi<a class='red'> *</a></a>
                            <input class="form-control input-bb" type="text" name="service_general_agency" id="service_general_agency" value="{{$servicegeneral['service_general_agency']}}" onChange="function_elements_add(this.name, this.value);" autocomplete="off" readonly/>

                            <input class="form-control input-bb" type="hidden" name="service_general_id" id="service_general_id" value="{{$service_general_id}}"/>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <a class="text-dark">No Whatsapp<a class='red'> *</a></a>
                            <input class="form-control input-bb" type="text" name="service_general_phone" id="service_general_phone" value="{{$servicegeneral['service_general_phone']}}" onChange="function_elements_add(this.name, this.value);" autocomplete="off" readonly/>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <a class="text-dark">Prioritas<a class='red'> *</a></a>
                            <input class="form-control input-bb" type="text" name="service_general_priority" id="service_general_priority" value="{{$TransServiceGeneral->getPriorityName($servicegeneral['service_general_priority'])}}" onChange="function_elements_add(this.name, this.value);" autocomplete="off" readonly/>
                        </div>
                    </div>
                </div>
                <div class="row form-group" id="service-image">
                    <div class="col-md-4">
                        <a class="text-dark">Surat Umum<a class='red'> *</a></a>
                        <br>
                        <br>
                        <?php if($servicegeneral['service_general_file']!=''){ ?>  
                            <a href='{{url('/trans-service-general/download/'.$servicegeneral['service_general_id'])}}' class='btn-sm btn-info' target="_blank"> Download</a>  
                        <?php } ?>
                    </div>
                    <div class="col-md-4">
                        <a class="text-dark">Surat<a class='red'> *</a></a>
                        <br>
                        <br>
                        <?php if($servicegeneral['service_general_file']!=''){ ?>  
                            <a href='{{url('/trans-service-general-approval/download-sk/'.$servicegeneral['service_general_id'])}}' class='btn-sm btn-info' target="_blank"> Download</a>  
                        <?php } ?>
                    </div>
                </div> 
                <div class="row form-group">
                    <div class="col-md-12">
                        <div class="form-group">	
                            <a class="text-dark">Keterangan<a class='red'> *</a></a>
                            <textarea class="form-control input-bb" name="service_general_remark" id="service_general_remark" readonly>{{$servicegeneral['service_general_remark']}}</textarea>
                        </div>
                    </div>
                </div>
                <br/>
                <div class="row">
                    <h5 class="form-section"><b>Formulir</b></h5>
                </div>
                <hr style="margin:0;">
                <br/>
                <?php foreach($servicegeneralparameter as $key => $parameter){?>
                    <div class="row form-group">
                        <div class="col-md-12">
                            <div class="form-group">
                                <a class="text-dark">{{$parameter['service_general_parameter_no'].'. '.$parameter['service_general_parameter_name']}}</a>
                                <textarea class="form-control input-bb" type="text" name="parameter_{{$parameter['service_general_parameter_id']}}" id="parameter_{{$parameter['service_general_parameter_id']}}"autocomplete="off" readonly>{{$parameter['service_general_parameter_value']}}</textarea>
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


@stop

@section('footer')
    
@stop

@section('css')
    
@stop