@inject('TransServiceDisposition', 'App\Http\Controllers\TransServiceDispositionController')

@extends('adminlte::page')

@section('title', 'Siabas')
@section('js')
<script>

    $(document).ready(function(){
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
@section('css')
<style>
    .selected-table {
        background-color :rgba(72, 230, 41, 0.712);
    }
</style>
@stop
@section('content_header')
    
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
        <li class="breadcrumb-item"><a href="{{ url('mustahik-worksheet-result') }}">Daftar Penilaian Mustahik</a></li>
        <li class="breadcrumb-item active" aria-current="page">Scoring Penilaian Mustahik</li>
    </ol>
</nav>

@stop

@section('content')

<h3 class="page-title">
    Form Scoring Penilaian Mustahik
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
            Detail
        </h5>
        <div class="float-right">
            <button onclick="location.href='{{ url('mustahik-worksheet-result') }}'" name="Find" class="btn btn-sm btn-info" title="Back"><i class="fa fa-angle-left"></i>  Kembali</button>
        </div>
    </div>

    <form method="post" action="{{route('process-edit-service-disposition')}}" enctype="multipart/form-data">
        @csrf
        <div class="card-body">
            <div class="row form-group">
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Nomor Pengajuan</a>
                        <input class="form-control input-bb" type="text" name="service_requisition_no" id="service_requisition_no" value="{{$mustahikworksheetdetail['service_requisition_no']}}" autocomplete="off" readonly/>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Tanggal Pengajuan</a>
                        <input class="form-control input-bb" type="text" name="service_requisition_date" id="service_requisition_date" value="{{$mustahikworksheetdetail['created_at']}}" autocomplete="off" readonly/>
                    </div>
                </div>
            </div>
            <div class="row form-group">
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Nama Pemohon</a>
                        <input class="form-control input-bb" type="text" name="service_requisition_name" id="service_requisition_name" value="{{$mustahikworksheetdetail['service_requisition_name']}}" autocomplete="off" readonly/>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Nama Layanan</a>
                        <input class="form-control input-bb" type="text" name="service_name" id="service_name" value="{{$mustahikworksheetdetail['service_name']}}" autocomplete="off" readonly/>
                    </div>
                </div>
            </div>
            <div class="row form-group">
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Nama Surveyor</a>
                        <input class="form-control input-bb" type="text" name="surveyor_name" id="surveyor_name" value="{{$mustahikworksheetdetail['full_name']}}" autocomplete="off" readonly/>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Tanggal Penilaian</a>
                        <input class="form-control input-bb" type="text" name="worksheet_result_date" id="worksheet_result_date" value="{{$mustahikworksheetdetail['worksheet_result_date']}}" autocomplete="off" readonly/>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<div class="card border border-dark">
    <div class="card-header border-dark bg-dark">
        <h5 class="mb-0 float-left">
            Form Scoring
        </h5>
        <div class="float-right">
            <button onclick="location.href='{{ url('mustahik-worksheet-result/print-scoring/'.$mustahikworksheetdetail['worksheet_result_id']) }}'" name="Find" class="btn btn-sm btn-info" title="Back"><i class="fa fa-print"></i>  Print</button>
        </div>
    </div>
    <?php 
        $score = 0;
    ?>
    <form method="post" action="{{route('process-edit-service-disposition')}}" enctype="multipart/form-data">
        @csrf
        <div class="card-body">
            <div class="form-body form">
                <div class="table-responsive">
                    <table class="table table-bordered table-advance table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th style='text-align:center'>No</th>
                                <th style='text-align:center'>Index Rumah</th>
                                <th style='text-align:center'>Kriteria</th>
                                <th style='text-align:center'>Bobot Nilai</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style='text-align:center; vertical-align: middle;' rowspan="4">1. </td>
                                <td style='text-align:center; vertical-align: middle;' rowspan="4">Ukuran Rumah</td>
                                <?php if($data['worksheet_home_size_type'] >= 0 && $data['worksheet_home_size_type'] <= 40) {?>
                                    <td style='text-align:center;' class="selected-table">0 - 40 m2</td>
                                    <td style='text-align:center;' class="selected-table">0</td>
                                <?php }else{?>
                                    <td style='text-align:center;'>0 - 40 m2</td>
                                    <td style='text-align:center'>0</td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <?php if($data['worksheet_home_size_type'] >= 41 && $data['worksheet_home_size_type'] <= 60) {
                                        $score +=1;
                                ?>
                                    <td style='text-align:center' class="selected-table">41 - 60 m2</td>
                                    <td style='text-align:center' class="selected-table">1</td>
                                <?php }else{?>
                                    <td style='text-align:center'>41 - 60 m2</td>
                                    <td style='text-align:center'>1</td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <?php if($data['worksheet_home_size_type'] >= 61 && $data['worksheet_home_size_type'] <= 100) {
                                    $score +=2;
                                ?>
                                    <td style='text-align:center' class="selected-table">61 - 100 m2</td>
                                    <td style='text-align:center' class="selected-table">2</td>
                                <?php }else{?>
                                    <td style='text-align:center'>61 - 100 m2</td>
                                    <td style='text-align:center'>2</td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <?php if($data['worksheet_home_size_type'] > 100) {
                                    $score +=4;
                                ?>
                                    <td style='text-align:center' class="selected-table">Diatas 100 m2</td>
                                    <td style='text-align:center' class="selected-table">4</td>
                                <?php }else{?>
                                    <td style='text-align:center'>Diatas 100 m2</td>
                                    <td style='text-align:center'>4</td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <td style='text-align:center; vertical-align: middle;' rowspan="4">2. </td>
                                <td style='text-align:center; vertical-align: middle;' rowspan="4">Dinding Rumah</td>
                                <?php if($data['worksheet_wall']['wall_bamboo']) {?>
                                    <td style='text-align:left' class="selected-table">Bilik Bambu</td>
                                    <td style='text-align:center' class="selected-table">0</td>
                                <?php }else{?>
                                    <td style='text-align:left'>Bilik Bambu</td>
                                    <td style='text-align:center'>0</td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <?php if($data['worksheet_wall']['wall_wood']) {
                                    $score +=3;
                                ?>
                                    <td style='text-align:left' class="selected-table">Kayu Rotan</td>
                                    <td style='text-align:center' class="selected-table">3</td>
                                <?php }else{?>
                                    <td style='text-align:left'>Kayu Rotan</td>
                                    <td style='text-align:center'>3</td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <?php if($data['worksheet_wall']['wall_mix']) {
                                    $score +=4;
                                ?>
                                    <td style='text-align:left' class="selected-table">Campuran Tembok Kayu</td>
                                    <td style='text-align:center' class="selected-table">4</td>
                                <?php }else{?>
                                    <td style='text-align:left'>Campuran Tembok Kayu</td>
                                    <td style='text-align:center'>4</td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <?php if($data['worksheet_wall']['wall_plaster']) {
                                    $score +=5;
                                ?>
                                    <td style='text-align:left' class="selected-table">Tembok Plester</td>
                                    <td style='text-align:center' class="selected-table">5</td>
                                <?php }else{?>
                                    <td style='text-align:left'>Tembok Plester</td>
                                    <td style='text-align:center'>5</td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <td style='text-align:center; vertical-align: middle;' rowspan="4">3. </td>
                                <td style='text-align:center; vertical-align: middle;' rowspan="4">Lantai</td>
                                <?php if($data['worksheet_floor']['floor_sand']) {?>
                                    <td style='text-align:left' class="selected-table">Tanah</td>
                                    <td style='text-align:center' class="selected-table">0</td>
                                <?php }else{?>
                                    <td style='text-align:left'>Tanah</td>
                                    <td style='text-align:center'>0</td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <?php if($data['worksheet_floor']['floor_wood']) {
                                    $score +=2;
                                ?>
                                    <td style='text-align:left' class="selected-table">Kayu</td>
                                    <td style='text-align:center' class="selected-table">2</td>
                                <?php }else{?>
                                    <td style='text-align:left'>Kayu</td>
                                    <td style='text-align:center'>2</td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <?php if($data['worksheet_floor']['floor_cement']) {
                                    $score +=4;
                                ?>
                                    <td style='text-align:left' class="selected-table">Semen</td>
                                    <td style='text-align:center' class="selected-table">4</td>
                                <?php }else{?>
                                    <td style='text-align:left'>Semen</td>
                                    <td style='text-align:center'>4</td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <?php if($data['worksheet_floor']['floor_ceramic']) {
                                    $score +=5;
                                ?>
                                    <td style='text-align:left' class="selected-table">Keramik</td>
                                    <td style='text-align:center' class="selected-table">5</td>
                                <?php }else{?>
                                    <td style='text-align:left'>Keramik</td>
                                    <td style='text-align:center'>5</td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <td style='text-align:center; vertical-align: middle;' rowspan="3">4. </td>
                                <td style='text-align:center; vertical-align: middle;' rowspan="3">Atap</td>
                                <?php if($data['worksheet_roof']['roof_asbes']) {
                                    $score +=1;
                                ?>
                                    <td style='text-align:left' class="selected-table">Asbes</td>
                                    <td style='text-align:center' class="selected-table">1</td>
                                <?php }else{?>
                                    <td style='text-align:left'>Asbes</td>
                                    <td style='text-align:center'>1</td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <?php if($data['worksheet_roof']['roof_metal']) {
                                    $score +=3;
                                ?>
                                    <td style='text-align:left' class="selected-table">Seng Metal</td>
                                    <td style='text-align:center' class="selected-table">3</td>
                                <?php }else{?>
                                    <td style='text-align:left'>Seng Metal</td>
                                    <td style='text-align:center'>3</td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <?php if($data['worksheet_roof']['roof_tile']) {
                                    $score +=5;
                                ?>
                                    <td style='text-align:left' class="selected-table">Genteng</td>
                                    <td style='text-align:center' class="selected-table">5</td>
                                <?php }else{?>
                                    <td style='text-align:left'>Genteng</td>
                                    <td style='text-align:center'>5</td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <td style='text-align:center; vertical-align: middle;' rowspan="3">5. </td>
                                <td style='text-align:center; vertical-align: middle;' rowspan="3">Sanitasi</td>
                                <?php if($data['worksheet_sanitation']['sanitation_bath_room']) {
                                    $score +=3;
                                ?>
                                    <td style='text-align:left' class="selected-table">Kamar Mandi</td>
                                    <td style='text-align:center' class="selected-table">3</td>
                                <?php }else{?>
                                    <td style='text-align:left'>Kamar Mandi</td>
                                    <td style='text-align:center'>3</td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <?php if($data['worksheet_sanitation']['sanitation_mck']) {
                                    $score +=3;
                                ?>
                                    <td style='text-align:left' class="selected-table">MCK</td>
                                    <td style='text-align:center' class="selected-table">3</td>
                                <?php }else{?>
                                    <td style='text-align:left'>MCK</td>
                                    <td style='text-align:center'>3</td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <?php if($data['worksheet_sanitation']['sanitation_well']) {
                                    $score +=3;
                                ?>
                                    <td style='text-align:left' class="selected-table">Sumur / Sumber Air</td>
                                    <td style='text-align:center' class="selected-table">3</td>
                                <?php }else{?>
                                    <td style='text-align:left'>Sumur / Sumber Air</td>
                                    <td style='text-align:center'>3</td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <td style='text-align:center; vertical-align: middle;' rowspan="2">6. </td>
                                <td style='text-align:center; vertical-align: middle;' rowspan="2">Listrik</td>
                                <?php if($data['worksheet_electricity']['electricity_private']) {
                                    $score +=2;
                                ?>
                                    <td style='text-align:left' class="selected-table">KWH Pribadi</td>
                                    <td style='text-align:center' class="selected-table">2</td>
                                <?php }else{?>
                                    <td style='text-align:left'>KWH Pribadi</td>
                                    <td style='text-align:center'>2</td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <?php if($data['worksheet_electricity']['electricity_connect']) {
                                    $score +=5;
                                ?>
                                    <td style='text-align:left' class="selected-table">Menyambung</td>
                                    <td style='text-align:center' class="selected-table">5</td>
                                <?php }else{?>
                                    <td style='text-align:left'>Menyambung</td>
                                    <td style='text-align:center'>5</td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <td style='text-align:center; vertical-align: middle;' rowspan="3">7. </td>
                                <td style='text-align:center; vertical-align: middle;' rowspan="3">Kepemilikan Rumah</td>
                                <?php if($data['worksheet_ownership']['ownership_rent']) {
                                    $score +=5;
                                ?>
                                    <td style='text-align:left' class="selected-table">Sewa</td>
                                    <td style='text-align:center' class="selected-table">5</td>
                                <?php }else{?>
                                    <td style='text-align:left'>Sewa</td>
                                    <td style='text-align:center'>5</td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <?php if($data['worksheet_ownership']['ownership_family']) {
                                    $score +=3;
                                ?>
                                    <td style='text-align:left' class="selected-table">Keluarga</td>
                                    <td style='text-align:center' class="selected-table">3</td>
                                <?php }else{?>
                                    <td style='text-align:left'>Keluarga</td>
                                    <td style='text-align:center'>3</td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <?php if($data['worksheet_ownership']['ownership_self']) {?>
                                    <td style='text-align:left' class="selected-table">Milik Sendiri</td>
                                    <td style='text-align:center' class="selected-table">0</td>
                                <?php }else{?>
                                    <td style='text-align:left'>Milik Sendiri</td>
                                    <td style='text-align:center'>0</td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <td style='text-align:center; vertical-align: middle;' rowspan="4">8. </td>
                                <td style='text-align:center; vertical-align: middle;' rowspan="4">Total penghasilan / Tahun</td>
                                <?php 
                                $penghasilan = 0; 
                                if($data['worksheet_husband_business_yearly']){
                                    $penghasilan += $data['worksheet_husband_business_yearly'];
                                }
                                if($data['worksheet_wife_business_yearly']){
                                    $penghasilan += $data['worksheet_wife_business_yearly'];
                                }
                                if($data['worksheet_parents_yearly']){
                                    $penghasilan += $data['worksheet_parents_yearly'];
                                }
                                if($data['worksheet_childs_yearly']){
                                    $penghasilan += $data['worksheet_childs_yearly'];
                                }
                                if($data['worksheet_other_yearly']){
                                    $penghasilan += $data['worksheet_other_yearly'];
                                }

                                if($penghasilan >= 0 && $penghasilan <= 5000000) {?>
                                    <td style='text-align:center' class="selected-table">0 - 5.000.000</td>
                                    <td style='text-align:center' class="selected-table">0</td>
                                <?php }else{?>
                                    <td style='text-align:center'>0 - 5.000.000</td>
                                    <td style='text-align:center'>0</td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <?php if($penghasilan >= 5000001 && $penghasilan <= 10000000) {
                                    $score +=2;
                                ?>
                                    <td style='text-align:center' class="selected-table">5.000.001 - 10.000.000</td>
                                    <td style='text-align:center' class="selected-table">2</td>
                                <?php }else{?>
                                    <td style='text-align:center'>5.000.001 - 10.000.000</td>
                                    <td style='text-align:center'>2</td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <?php if($penghasilan >= 10000001 && $penghasilan <= 15000000) {
                                    $score +=4;
                                ?>
                                    <td style='text-align:center' class="selected-table">10.000.001 - 15.000.000</td>
                                    <td style='text-align:center' class="selected-table">4</td>
                                <?php }else{?>
                                    <td style='text-align:center'>10.000.001 - 15.000.000</td>
                                    <td style='text-align:center'>4</td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <?php if($penghasilan >= 15000001) {
                                    $score +=5;
                                ?>
                                    <td style='text-align:center' class="selected-table">Diatas 15 Juta</td>
                                    <td style='text-align:center' class="selected-table">5</td>
                                <?php }else{?>
                                    <td style='text-align:center'>Diatas 15 Juta</td>
                                    <td style='text-align:center'>5</td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <th style='text-align:center' colspan="3">Total</th>
                                <th style='text-align:center'>{{$score}}</th>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <br>
        <br>
        
        <div class="card-body">
            <div class="form-body form">
                <div class="row form-group">
                    <div class="col-md-4">
                        <div class="table-responsive">
                            <table class="table table-bordered table-advance table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th style='text-align:center'>Nilai Scoring</th>
                                        <th style='text-align:center'>Kategori</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td style='text-align:center'><= 20</td>
                                        <td style='text-align:center'>Approve</td>
                                    </tr>
                                    <tr>
                                        <td style='text-align:center'>21 - 39</td>
                                        <td style='text-align:center'>Komite</td>
                                    </tr>
                                    <tr>
                                        <td style='text-align:center'>>= 40</td>
                                        <td style='text-align:center'>Reject</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-md-4">
                    </div>
                    <div class="col-md-4">
                        <div class="table-responsive">
                            <table class="table table-bordered table-advance table-hover">
                                <thead class="thead-light">
                                    <tr>
                                        <th style='text-align:center'>Hasil Scoring</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <?php if($score <= 20){
                                            $score_result = "Aprrove";
                                        }else if($score >= 21 && $score <= 39){
                                            $score_result = "Komite";
                                        }else if($score >= 40){
                                            $score_result = "Reject";
                                        } 
                                        ?>
                                        <td style='text-align:center'>{{$score_result}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
@stop

@section('footer')
    
@stop

@section('css')
    
@stop