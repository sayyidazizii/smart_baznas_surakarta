@extends('adminlte::page')

@section('title', 'SMArT Baznas Surakarta')
@section('js')
<script>

	function processAddArrayCoreServiceTerm(){
		var service_term_no					= document.getElementById("service_term_no").value;
		var service_term_description		= document.getElementById("service_term_description").value;
		
        $.ajax({
            type: "POST",
            url : "{{route('add-service-term-array')}}",
            data: {
                'service_term_no'					: service_term_no,
                'service_term_description' 			: service_term_description, 
                '_token'                            : '{{csrf_token()}}'
            },
            success: function(msg){
                location.reload();
            }
        });
	}

    function processAddArrayCoreServiceParameter(){
        var service_parameter_no				= document.getElementById("service_parameter_no").value;
        var service_parameter_description		= document.getElementById("service_parameter_description").value;
        
        $.ajax({
            type: "POST",
            url : "{{route('add-service-parameter-array')}}",
            data: {
                'service_parameter_no'			    : service_parameter_no,
                'service_parameter_description'     : service_parameter_description, 
                '_token'                            : '{{csrf_token()}}'
            },
            success: function(msg){
                location.reload();
            }
        });
    }

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
				url : "{{route('add-reset-service')}}",
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
        <li class="breadcrumb-item"><a href="{{ url('service') }}">Daftar Surat Umum</a></li>
        <li class="breadcrumb-item active" aria-current="page">Tambah Surat Umum</li>
    </ol>
</nav>

@stop

@section('content')

<h3 class="page-title">
    Form Tambah Surat Umum
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
                <button onclick="location.href='{{ url('service-general-parameter') }}'" name="Find" class="btn btn-sm btn-info" title="Back"><i class="fa fa-angle-left"></i>  Kembali</button>
            </div>
        </div>

        <?php 
            if (empty($coreservice)){
                $coreservice['service_name'] = '';
            }
        ?>

        <form method="post" action="{{route('process-add-service-general-parameter')}}" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                <div class="row form-group">
                    <div class="col-md-6">
                        <div class="form-group">
                            <a class="text-dark">Nomor Isian Surat Umum<a class='red'> *</a></a>
                            <input class="form-control input-bb" type="text" name="service_general_parameter_no" id="service_general_parameter_no" value="" onChange="function_elements_add(this.name, this.value);" autocomplete="off"/>

                            <input class="form-control input-bb" type="hidden" name="service_general_parameter_token" id="service_general_parameter_token" value="{{$service_token}}"/>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <a class="text-dark">Nama Isian Surat Umum<a class='red'> *</a></a>
                            <input class="form-control input-bb" type="text" name="service_general_parameter_name" id="service_general_parameter_name" value="" onChange="function_elements_add(this.name, this.value);" autocomplete="off"/>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer text-muted">
                <div class="form-actions float-right">
                    <button type="reset" name="Reset" class="btn btn-danger" onClick="reset_add();"><i class="fa fa-times"></i> Batal</button>
                    <button type="submit" name="Save" class="btn btn-primary" title="Save"><i class="fa fa-check"></i> Simpan</button>
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