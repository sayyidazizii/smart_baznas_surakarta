@extends('adminlte::page')

@section('title', 'PTSP Baznas Surakarta')
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

	function reset_add(){
		$.ajax({
				type: "GET",
				url : "/trans-service-requisition/reset-add",
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
        <li class="breadcrumb-item"><a href="{{ url('trans-service-requisition') }}">Daftar Pengajuan Bantuan</a></li>
        <li class="breadcrumb-item"><a href="{{ url('trans-service-requisition/search') }}">Daftar Layanan</a></li>
        <li class="breadcrumb-item active" aria-current="page">Tambah Pengajuan Bantuan</li>
    </ol>
</nav>

@stop

@section('content')

<h3 class="page-title">
    Form Tambah Pengajuan Bantuan
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
                <button onclick="location.href='{{ url('trans-service-requisition/search') }}'" name="Find" class="btn btn-sm btn-info" title="Back"><i class="fa fa-angle-left"></i>  Kembali</button>
            </div>
        </div>

        <form method="post" action="{{route('process-add-service-requisition')}}" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                <div class="row form-group">
                    <div class="col-md-6">
                        <div class="form-group">
                            <a class="text-dark">Nama Layanan<a class='red'> *</a></a>
                            <input class="form-control input-bb" type="text" name="service_name" id="service_name" value="{{$coreservice['service_name']}}" onChange="function_elements_add(this.name, this.value);" autocomplete="off" readonly/>
                            <input class="form-control input-bb" type="hidden" name="service_id" id="service_id" value="{{$coreservice['service_id']}}" onChange="function_elements_add(this.name, this.value);" autocomplete="off" readonly/>

                            <input class="form-control input-bb" type="hidden" name="service_requisition_token" id="service_requisition_token" value="{{$service_requisition_token}}"/>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <a class="text-dark">Nama Pemohon<a class='red'> *</a></a>
                            <input class="form-control input-bb" type="text" name="service_requisition_name" id="service_requisition_name" value="" onChange="function_elements_add(this.name, this.value);" autocomplete="off"/>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <a class="text-dark">No Whatsapp Pemohon<a class='red'> *</a></a>
                            <input class="form-control input-bb" type="text" name="service_requisition_phone" id="service_requisition_phone" value="" onChange="function_elements_add(this.name, this.value);" autocomplete="off"/>
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
                                <th style='text-align:center' width='70%'>Deskripsi</th>
                                <th style='text-align:center' width='5%'>Status</th>
                                <th style='text-align:center' width='20%'>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                $no =1;
                                foreach ($coreserviceterm AS $key => $val){
                                    echo"
                                        <tr>
                                            <td style='text-align  : center !important;'>".$val['service_term_no']."</td>
                                            <td style='text-align  : left !important;'>".$val['service_term_description']."</td>";?>
                                            
                                            <td style='text-align  : center'>
                                                <input type='checkbox' class='checkboxes' name='checkbox_{{$val['service_term_id']}}' id='checkbox_{{$val['service_term_id']}}' value='1'  OnClick='checkboxSalesOrderChange({{$val['service_term_id']}})';/>
                                            </td>

                                            <td style='text-align  : center'>
                                                <div class="row form-group" id="service-image">
                                                    <div class="col-md-6">
                                                        <input type="file" name="file_{{$val['service_term_id']}}" id="file_{{$val['service_term_id']}}" value="" accept="image/*"/>
                                                    </div>
                                                </div>            
                                    <?php echo "
                                            </td>
                                        </tr>
                                    ";
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
                <?php foreach($coreserviceparameter as $key => $parameter){?>
                    <div class="row form-group">
                        <div class="col-md-12">
                            <div class="form-group">
                                <a class="text-dark">{{$parameter['service_parameter_no'].'. '.$parameter['service_parameter_description']}}</a>
                                <textarea class="form-control input-bb" type="text" name="parameter_{{$parameter['service_parameter_id']}}" id="parameter_{{$parameter['service_parameter_id']}}"autocomplete="off"></textarea>
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