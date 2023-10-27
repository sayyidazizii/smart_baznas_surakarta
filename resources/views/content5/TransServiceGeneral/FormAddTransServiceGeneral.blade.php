@extends('adminlte::page')

@section('title', 'SMArT Baznas Surakarta')
@section('js')
<script>

    $(document).ready(function(){
        $("#service_general_priority").select2("val", "0");
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

	function reset_add(){
		$.ajax({
				type: "GET",
				url : "/trans-service-general/reset-add",
				success: function(msg){
                    location.reload();
			}

		});
	}
</script>
@stop
@section('content_header')
    
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
        <li class="breadcrumb-item"><a href="{{ url('trans-service-general') }}">Daftar Pengajuan Surat Umum</a></li>
        <li class="breadcrumb-item"><a href="{{ url('trans-service-general/search') }}">Daftar Layanan</a></li>
        <li class="breadcrumb-item active" aria-current="page">Tambah Pengajuan Surat Umum</li>
    </ol>
</nav>    

@stop

@section('content')

<h3 class="page-title">
    Form Tambah Pengajuan Surat Umum
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
                Form Tambah
            </h5>
            <div class="float-right">
                <button onclick="location.href='{{ url('trans-service-general') }}'" name="Find" class="btn btn-sm btn-info" title="Back"><i class="fa fa-angle-left"></i>  Kembali</button>
            </div>
        </div>

        <form method="post" action="{{route('process-add-service-general')}}" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                <div class="row form-group">
                    <div class="col-md-4">
                        <div class="form-group">
                            <a class="text-dark">Nama Instansi<a class='red'> *</a></a>
                            <input class="form-control input-bb" type="text" name="service_general_agency" id="service_general_agency" value="" onChange="function_elements_add(this.name, this.value);" autocomplete="off"/>
                            <input class="form-control input-bb" type="hidden" name="service_general_token" id="service_general_token" onChange="function_elements_add(this.name, this.value);" autocomplete="off" value="{{$service_general_token}}"/>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <a class="text-dark">No Whatsapp<a class='red'> *</a></a>
                            <input class="form-control input-bb" type="text" name="service_general_phone" id="service_general_phone" value="" onChange="function_elements_add(this.name, this.value);" autocomplete="off"/>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <a class="text-dark">Prioritas<a class='red'> *</a></a>
                            {!! Form::select('service_general_priority',  $coreservicegeneralpriority, 0, ['class' => 'selection-search-clear select-form', 'id' => 'service_general_priority']) !!}
                        </div>
                    </div>
                </div>
                <div class="row form-group" id="service-image">
                    <div class="col-md-6">
                        <a class="text-dark">Upload Surat Umum<a class='red'> *</a></a>
                        <br>
                        <br>
                        <input type="file" name="service_general_file" id="service_general_file" value="" accept="image/*"/>
                    </div>
                </div> 

                <br/>
                <div class="row">
                    <h5 class="form-section"><b>Formulir</b></h5>
                </div>
                <hr style="margin:0;">
                <br/>
                <?php foreach($coreservicegeneralparameter as $key => $parameter){?>
                    <div class="row form-group">
                        <div class="col-md-12">
                            <div class="form-group">
                                <a class="text-dark">{{$parameter['service_general_parameter_no'].'. '.$parameter['service_general_parameter_name']}}</a>
                                <textarea class="form-control input-bb" type="text" name="parameter_{{$parameter['service_general_parameter_id']}}" id="parameter_{{$parameter['service_general_parameter_id']}}" autocomplete="off"></textarea>
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