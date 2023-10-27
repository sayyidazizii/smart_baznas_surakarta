@extends('adminlte::page')

@section('title', 'PTSP Baznas Surakarta')
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
        <li class="breadcrumb-item"><a href="{{ url('service') }}">Daftar Layanan</a></li>
        <li class="breadcrumb-item active" aria-current="page">Tambah Layanan</li>
    </ol>
</nav>

@stop

@section('content')

<h3 class="page-title">
    Form Tambah Layanan
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
                <button onclick="location.href='{{ url('service') }}'" name="Find" class="btn btn-sm btn-info" title="Back"><i class="fa fa-angle-left"></i>  Kembali</button>
            </div>
        </div>

        <?php 
            if (empty($coreservice)){
                $coreservice['service_name'] = '';
            }
        ?>

        <form method="post" action="{{route('process-add-service')}}" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                <div class="row form-group">
                    <div class="col-md-6">
                        <div class="form-group">
                            <a class="text-dark">Nama Layanan<a class='red'> *</a></a>
                            <input class="form-control input-bb" type="text" name="service_name" id="service_name" value="{{$coreservice['service_name']}}" onChange="function_elements_add(this.name, this.value);" autocomplete="off"/>

                            <input class="form-control input-bb" type="hidden" name="service_token" id="service_token" value="{{$service_token}}"/>
                        </div>
                    </div>
                </div>

                <br/>
                <div class="row">
                    <h5 class="form-section"><b>Detail Syarat Dan Ketentuan</b></h5>
                </div>
                <hr style="margin:0;">
                <br/>
                <div class="row form-group">
                    <div class="col-md-3">
                        <div class="form-group">
                            <a class="text-dark">No</a>
                            <input class="form-control input-bb" type="text" name="service_term_no" id="service_term_no" value="" autocomplete="off"/>
                        </div>
                    </div>
                
                    <div class="col-md-9">
                        <div class="form-group">
                            <a class="text-dark">Deskripsi</a>
                            <input class="form-control input-bb" type="text" name="service_term_description" id="service_term_description" value="" autocomplete="off"/>
                        </div>
                    </div>
                </div>
                
                <div class="form-actions float-right">
                    <a name="Save" class="btn btn-primary" title="Save" onclick='processAddArrayCoreServiceTerm()'>Tambah</a>
                </div>

                <br/>
                <br/>
                <br/>
                <div class="row">
                    <h5 class="form-section"><b>Detail Isian Formulir</b></h5>
                </div>
                <hr style="margin:0;">
                <br/>
                <div class="row form-group">
                    <div class="col-md-3">
                        <div class="form-group">
                            <a class="text-dark">No</a>
                            <input class="form-control input-bb" type="text" name="service_parameter_no" id="service_parameter_no" value="" autocomplete="off"/>
                        </div>
                    </div>
                
                    <div class="col-md-9">
                        <div class="form-group">
                            <a class="text-dark">Deskripsi</a>
                            <input class="form-control input-bb" type="text" name="service_parameter_description" id="service_parameter_description" value="" autocomplete="off"/>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer text-muted">
                <div class="form-actions float-right">
                    <a name="Save" class="btn btn-primary" title="Save" onclick='processAddArrayCoreServiceParameter()'>Tambah</a>
                </div>
            </div>
        </div>
    </div>

    <br/>
    <div class="card border border-dark">
        <div class="card-header border-dark bg-dark">
            <h5 class="mb-0 float-left">
                Daftar
            </h5>
        </div>

        <div class="card-body">
            <div class="form-body form">
                <div class="row">
                    <h5 class="form-section"><b>Daftar Syarat Dan Ketentuan</b></h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-advance table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th style='text-align:center'>No</th>
                                <th style='text-align:center'>Deskripsi</th>
                                <th style='text-align:center'>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $receipt_amount_total = 0;
                                if(!is_array($coreserviceterm)){
                                    echo "<tr><th colspan='5' style='text-align  : center !important;'>Data Kosong</th></tr>";
                                } else {
                                    $no =1;
                                    foreach ($coreserviceterm AS $key => $val){
                                        echo"
                                            <tr>
                                                <td style='text-align  : left !important;'>".$val['service_term_no']."</td>
                                                <td style='text-align  : left !important;'>".$val['service_term_description']."</td>
                                                
                                                <td style='text-align  : center'>
                                                    <a href='/service/delete-add-term-array/".$key."' name='Reset' class='btn btn-danger btn-sm' onClick='javascript:return confirm(\"apakah yakin ingin dihapus ?\")'></i> Hapus</a>
                                                </td>";
                                                echo"
                                            </tr>
                                        ";
                                    }
                                }
                            ?>
                        </tbody>
                    </table>
                </div>
                <br/>
                <br/>
                <br/>
                <div class="row">
                    <h5 class="form-section"><b>Daftar Isian Formulir</b></h5>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-advance table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th style='text-align:center'>No</th>
                                <th style='text-align:center'>Deskripsi</th>
                                <th style='text-align:center'>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $receipt_amount_total = 0;
                                if(!is_array($coreserviceparameter)){
                                    echo "<tr><th colspan='5' style='text-align  : center !important;'>Data Kosong</th></tr>";
                                } else {
                                    $no =1;
                                    foreach ($coreserviceparameter AS $key => $val){
                                        echo"
                                            <tr>
                                                <td style='text-align  : left !important;'>".$val['service_parameter_no']."</td>
                                                <td style='text-align  : left !important;'>".$val['service_parameter_description']."</td>
                                                
                                                <td style='text-align  : center'>
                                                    <a href='/service/delete-add-parameter-array/".$key."' name='Reset' class='btn btn-danger btn-sm' onClick='javascript:return confirm(\"apakah yakin ingin dihapus ?\")'></i> Hapus</a>
                                                </td>";
                                                echo"
                                            </tr>
                                        ";
                                    }
                                }
                            ?>
                        </tbody>
                    </table>
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
    <br>
    <br>
    <br>                            
</form>

@stop

@section('footer')
    
@stop

@section('css')
    
@stop