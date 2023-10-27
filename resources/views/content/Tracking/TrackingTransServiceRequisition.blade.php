@inject('Tracking', 'App\Http\Controllers\TrackingController')

@extends('adminlte::page')

@section('title', 'Siabas')

@section('js')
<script>
	$(document).ready(function(){
        var service_requisition_no        = {!! json_encode($service_requisition_no) !!};
        
        document.getElementById("service_requisition_no").focus();
        setInterval(function() {
            searchTracking();
        }, 600000); //10 menit
    });
    $(document).ready(function(){
    $('#service_requisition_no').keypress(function(e){
      if(e.keyCode==13)
      $('#searchButton').click();
    });
});

    function searchTracking(){
        var service_requisition_no = document.getElementById("service_requisition_no").value;
        $.ajax({
                type: "POST",
                url : "{{route('search-tracking')}}",
                data : {
                    'service_requisition_no'      : service_requisition_no, 
                    '_token'                      : '{{csrf_token()}}'
                },
                success: function(msg){
                    location.reload();
            }
        });
    }

    function resetTracking(){
        $.ajax({
                type: "POST",
                url : "{{route('reset-tracking')}}",
                data : {
                    '_token'                      : '{{csrf_token()}}'
                },
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
        <li class="breadcrumb-item active" aria-current="page">Siabas</li>
    </ol>
</nav>

@stop

@section('content')

<h3 class="page-title">
    <b>Tracking</b> <small></small>
</h3>
<br/>
@if(session('msg'))
<div class="alert alert-info" role="alert">
    {{session('msg')}}
</div>
@endif 
<div class="card border border-dark">
    <div class="card-header bg-dark clearfix">
        <h5 class="mb-0 float-left">
            Cari
        </h5>
        <div class="form-actions float-right">
        </div>
    </div>

    <div class="card-body">
        <div class="row form-group">
            <div class="col-md-12">
                <div class="form-group">
                    <a class="text-dark">Nomor Pengajuan<a class='red'> *</a></a>
                    {{-- {!! Form::select('service_requisition_id',  $servicerequisitionno, $service_requisition_id, ['class' => 'selection-search-clear select-form', 'id' => 'service_requisition_id']) !!} --}}
                    <input class="form-control input-bb" type="text" name="service_requisition_no" id="service_requisition_no" value="" autocomplete="off" placeholder="klik disini sebelum scan barcode" onClick="this.value='';"/>

                </div>
            </div>
        </div>
    </div>
        
    <div class="card-footer text-muted">
        <div class="form-actions float-right">
            <a class="btn btn-danger" onClick="resetTracking()">Reset</a>
            <button type="submit" name="Save" id="searchButton" class="btn btn-primary" onClick="searchTracking()">Cari</button>
        </div>
    </div>
</div>
<div class="card border border-dark">
    <div class="card-header bg-dark clearfix">
        <h5 class="mb-0 float-left">
            History
        </h5>
    </div>

    <div class="card-body">
        <div class="row form-group">
            <div class="col-md-6">
                <div class="form-group">
                    <a class="text-dark">Nama Layanan<a class='red'> *</a></a>
                    <input class="form-control input-bb" type="text" name="service_name" id="service_name" value="{{$Tracking->getServiceName($transservicerequisition['service_id'])}}" onChange="function_elements_add(this.name, this.value);" autocomplete="off" readonly/>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <a class="text-dark">Nama Pemohon<a class='red'> *</a></a>
                    <input class="form-control input-bb" type="text" name="service_requisition_name" id="service_requisition_name" value="{{$transservicerequisition['service_requisition_name']}}" onChange="function_elements_add(this.name, this.value);" autocomplete="off" readonly/>
                </div>
            </div>
        </div>
        <div class="row form-group">
            <div class="table-responsive">
                <table class="table table-bordered table-advance table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th style='text-align:center'>No</th>
                            <th style='text-align:center'>Tanggal</th>
                            <th style='text-align:center'>Posisi</th>
                            <th style='text-align:center'>Status</th>
                            <th style='text-align:center'>User</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            if(!isset($transservicerequisition)){
                                echo "<tr><th colspan='5' style='text-align  : center !important;'>Data Kosong</th></tr>";
                            } else {
                                $no = 1;
                                foreach($servicelog as $val){
                                    if($val['service_status'] == 1 || $val['service_status'] == 5 ||$val['service_status'] == 8){
                                    echo"
                                        <tr>
                                            <td style='text-align  : center !important;'>".$no."</td>
                                            <td style='text-align  : left !important;'>".$val['created_at']."</td>
                                            <td style='text-align  : left !important;'>".$Tracking->getSectionName1(1)."</td>
                                            <td style='text-align  : left !important;'>".$Tracking->getServiceStatus($val['service_status'])."</td>
                                            <td style='text-align  : left !important;'>".$Tracking->getUserName($val['created_id'])."</td>
                                        </tr>
                                    ";
                                    }
                                    else {
                                        $transservicedisposition = $Tracking->getServiceDisposition($transservicerequisition['service_requisition_id']);
                                        echo"
                                            <tr>
                                                <td style='text-align  : center !important;'>".$no."</td>
                                                <td style='text-align  : left !important;'>".$val['created_at']."</td>
                                                <td style='text-align  : left !important;'>".$Tracking->getSectionName1($val['section_id'])."</td>
                                                <td style='text-align  : left !important;'>".$Tracking->getServiceStatus($val['service_status'])."</td>
                                                <td style='text-align  : left !important;'>".$Tracking->getUserName($val['created_id'])."</td>
                                            </tr>
                                        ";
                                    }
                                    $no++;
                                }
                            }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
</div>

@stop

@section('footer')
    
@stop

@section('css')
    
@stop