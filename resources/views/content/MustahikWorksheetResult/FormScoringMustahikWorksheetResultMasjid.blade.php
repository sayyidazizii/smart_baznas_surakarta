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
                                <th style='text-align:center'>Index Penilaian</th>
                                <th style='text-align:center'>Kriteria</th>
                                <th style='text-align:center'>Bobot Nilai</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td style='text-align:center; vertical-align: middle;' rowspan="4">1. </td>
                                <td style='text-align:center; vertical-align: middle;' rowspan="4">Jumlah KK Miskin</td>
                                <?php if($data['mosque_poor_KK'] < 5) {
                                    $score += 4;
                                ?>
                                    <td style='text-align:left;' class="selected-table">Dibawah 5</td>
                                    <td style='text-align:center;' class="selected-table">4</td>
                                <?php }else{?>
                                    <td style='text-align:left;'>Dibawah 5</td>
                                    <td style='text-align:center'>4</td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <?php if($data['mosque_poor_KK'] >= 5 && $data['mosque_poor_KK'] <= 10) {
                                        $score += 3;
                                ?>
                                    <td style='text-align:left' class="selected-table">5 - 10</td>
                                    <td style='text-align:center' class="selected-table">3</td>
                                <?php }else{?>
                                    <td style='text-align:left'>5 - 10</td>
                                    <td style='text-align:center'>3</td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <?php if($data['mosque_poor_KK'] >= 11 && $data['mosque_poor_KK'] <= 15) {
                                    $score += 2;
                                ?>
                                    <td style='text-align:left' class="selected-table">11 -15</td>
                                    <td style='text-align:center' class="selected-table">2</td>
                                <?php }else{?>
                                    <td style='text-align:left'>11 -15</td>
                                    <td style='text-align:center'>2</td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <?php if($data['mosque_poor_KK'] > 15) {
                                    $score += 1;
                                ?>
                                    <td style='text-align:left' class="selected-table">Diatas 15</td>
                                    <td style='text-align:center' class="selected-table">1</td>
                                <?php }else{?>
                                    <td style='text-align:left'>Diatas 15</td>
                                    <td style='text-align:center'>1</td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <td style='text-align:center; vertical-align: middle;' rowspan="2">2. </td>
                                <td style='text-align:center; vertical-align: middle;' rowspan="2">Sertifikat Wakaf</td>
                                <?php if($data['mosque_wakaf_certificate']['no_certificate']) {
                                    $score += 4;
                                ?>
                                    <td style='text-align:left;' class="selected-table">Tidak Ada</td>
                                    <td style='text-align:center;' class="selected-table">4</td>
                                <?php }else{?>
                                    <td style='text-align:left;'>Tidak Ada</td>
                                    <td style='text-align:center'>4</td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <?php if($data['mosque_wakaf_certificate']['certificate']) {
                                        $score += 2;
                                ?>
                                    <td style='text-align:left' class="selected-table">Ada</td>
                                    <td style='text-align:center' class="selected-table">2</td>
                                <?php }else{?>
                                    <td style='text-align:left'>Ada</td>
                                    <td style='text-align:center'>2</td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <td style='text-align:center; vertical-align: middle;' rowspan="2">3. </td>
                                <td style='text-align:center; vertical-align: middle;' rowspan="2">Ijin Masjid</td>
                                <?php if($data['mosque_permission']['no_permission']) {
                                    $score += 4;
                                ?>
                                    <td style='text-align:left;' class="selected-table">Tidak Ada</td>
                                    <td style='text-align:center;' class="selected-table">4</td>
                                <?php }else{?>
                                    <td style='text-align:left;'>Tidak Ada</td>
                                    <td style='text-align:center'>4</td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <?php if($data['mosque_permission']['permission']) {
                                        $score += 2;
                                ?>
                                    <td style='text-align:left' class="selected-table">Ada</td>
                                    <td style='text-align:center' class="selected-table">2</td>
                                <?php }else{?>
                                    <td style='text-align:left'>Ada</td>
                                    <td style='text-align:center'>2</td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <td style='text-align:center; vertical-align: middle;' rowspan="4">4. </td>
                                <td style='text-align:center; vertical-align: middle;' rowspan="4">Jumlah Jamaah</td>
                                <?php if($data['mosque_jamaah'] < 50) {
                                    $score += 4;
                                ?>
                                    <td style='text-align:left;' class="selected-table">Dibawah 50</td>
                                    <td style='text-align:center;' class="selected-table">4</td>
                                <?php }else{?>
                                    <td style='text-align:left;'>Dibawah 50</td>
                                    <td style='text-align:center'>4</td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <?php if($data['mosque_jamaah'] >= 50 && $data['mosque_jamaah'] <= 100) {
                                    $score += 3;
                                ?>
                                    <td style='text-align:left' class="selected-table">50 - 100</td>
                                    <td style='text-align:center' class="selected-table">3</td>
                                <?php }else{?>
                                    <td style='text-align:left'>50 - 100</td>
                                    <td style='text-align:center'>3</td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <?php if($data['mosque_jamaah'] >= 101 && $data['mosque_jamaah'] <= 150) {
                                    $score += 2;
                                ?>
                                    <td style='text-align:left' class="selected-table">101 - 150</td>
                                    <td style='text-align:center' class="selected-table">2</td>
                                <?php }else{?>
                                    <td style='text-align:left'>101 - 150</td>
                                    <td style='text-align:center'>2</td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <?php if($data['mosque_jamaah'] > 150) {
                                    $score += 1;
                                ?>
                                    <td style='text-align:left' class="selected-table">Diatas 150</td>
                                    <td style='text-align:center' class="selected-table">1</td>
                                <?php }else{?>
                                    <td style='text-align:left'>Diatas 150</td>
                                    <td style='text-align:center'>1</td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <td style='text-align:center; vertical-align: middle;' rowspan="4">5. </td>
                                <td style='text-align:center; vertical-align: middle;' rowspan="4">Luas Bangunan</td>
                                <?php if($data['mosque_area'] < 50) {
                                    $score += 4;
                                ?>
                                    <td style='text-align:left;' class="selected-table">Dibawah 50 m2</td>
                                    <td style='text-align:center;' class="selected-table">4</td>
                                <?php }else{?>
                                    <td style='text-align:left;'>Dibawah 50 m2</td>
                                    <td style='text-align:center'>4</td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <?php if($data['mosque_area'] >= 50 && $data['mosque_area'] <= 100) {
                                    $score += 3;
                                ?>
                                    <td style='text-align:left' class="selected-table">50 - 100 m2</td>
                                    <td style='text-align:center' class="selected-table">3</td>
                                <?php }else{?>
                                    <td style='text-align:left'>50 - 100 m2</td>
                                    <td style='text-align:center'>3</td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <?php if($data['mosque_area'] >= 101 && $data['mosque_area'] <= 150) {
                                    $score += 2;
                                ?>
                                    <td style='text-align:left' class="selected-table">101 - 150 m2</td>
                                    <td style='text-align:center' class="selected-table">2</td>
                                <?php }else{?>
                                    <td style='text-align:left'>101 - 150 m2</td>
                                    <td style='text-align:center'>2</td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <?php if($data['mosque_area'] > 150) {
                                    $score += 1;
                                ?>
                                    <td style='text-align:left' class="selected-table">Diatas 150 m2</td>
                                    <td style='text-align:center' class="selected-table">1</td>
                                <?php }else{?>
                                    <td style='text-align:left'>Diatas 150</td>
                                    <td style='text-align:center'>1</td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <td style='text-align:center; vertical-align: middle;' rowspan="2">6. </td>
                                <td style='text-align:center; vertical-align: middle;' rowspan="2">Sumber Air</td>
                                <?php if($data['water_source']['water_source_machine']) {
                                    $score += 4;
                                ?>
                                    <td style='text-align:left;' class="selected-table">Mesin</td>
                                    <td style='text-align:center;' class="selected-table">4</td>
                                <?php }else{?>
                                    <td style='text-align:left;'>Mesin</td>
                                    <td style='text-align:center'>4</td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <?php if($data['water_source']['water_source_manual']) {
                                    $score += 2;
                                ?>
                                    <td style='text-align:left' class="selected-table">Alam / Manual</td>
                                    <td style='text-align:center' class="selected-table">2</td>
                                <?php }else{?>
                                    <td style='text-align:left'>Alam / Manual</td>
                                    <td style='text-align:center'>2</td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <td style='text-align:center; vertical-align: middle;' rowspan="3">7. </td>
                                <td style='text-align:center; vertical-align: middle;' rowspan="3">Lantai</td>
                                <?php if($data['floor']['floor_1']) {
                                    $score += 1;
                                ?>
                                    <td style='text-align:left;' class="selected-table">Ubin / Tegel</td>
                                    <td style='text-align:center;' class="selected-table">1</td>
                                <?php }else{?>
                                    <td style='text-align:left;'>Ubin / Tegel</td>
                                    <td style='text-align:center'>1</td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <?php if($data['floor']['floor_2']) {
                                    $score += 3;
                                ?>
                                    <td style='text-align:left' class="selected-table">Keramik</td>
                                    <td style='text-align:center' class="selected-table">3</td>
                                <?php }else{?>
                                    <td style='text-align:left'>Keramik</td>
                                    <td style='text-align:center'>3</td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <?php if($data['floor']['floor_3']) {
                                    $score += 5;
                                ?>
                                    <td style='text-align:left' class="selected-table">Marmer / Granit</td>
                                    <td style='text-align:center' class="selected-table">5</td>
                                <?php }else{?>
                                    <td style='text-align:left'>Marmer / Granit</td>
                                    <td style='text-align:center'>5</td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <td style='text-align:center; vertical-align: middle;' rowspan="2">8. </td>
                                <td style='text-align:center; vertical-align: middle;' rowspan="2">Dinding</td>
                                <?php if($data['wall']['wall_1']) {
                                    $score += 4;
                                ?>
                                    <td style='text-align:left;' class="selected-table">Bagus</td>
                                    <td style='text-align:center;' class="selected-table">4</td>
                                <?php }else{?>
                                    <td style='text-align:left;'>Bagus</td>
                                    <td style='text-align:center'>4</td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <?php if($data['wall']['wall_2']) {
                                    $score += 2;
                                ?>
                                    <td style='text-align:left' class="selected-table">Tidak Bagus</td>
                                    <td style='text-align:center' class="selected-table">2</td>
                                <?php }else{?>
                                    <td style='text-align:left'>Tidak Bagus</td>
                                    <td style='text-align:center'>2</td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <td style='text-align:center; vertical-align: middle;' rowspan="3">9. </td>
                                <td style='text-align:center; vertical-align: middle;' rowspan="3">Atap</td>
                                <?php if($data['roof']['roof_1']) {
                                    $score += 4;
                                ?>
                                    <td style='text-align:left;' class="selected-table">Genteng, Pvc, Spandek</td>
                                    <td style='text-align:center;' class="selected-table">4</td>
                                <?php }else{?>
                                    <td style='text-align:left;'>Genteng, Pvc, Spandek</td>
                                    <td style='text-align:center'>4</td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <?php if($data['roof']['roof_2']) {
                                    $score += 2;
                                ?>
                                    <td style='text-align:left' class="selected-table">Seng</td>
                                    <td style='text-align:center' class="selected-table">2</td>
                                <?php }else{?>
                                    <td style='text-align:left'>Seng</td>
                                    <td style='text-align:center'>2</td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <?php if($data['roof']['roof_3']) {
                                    $score += 1;
                                ?>
                                    <td style='text-align:left' class="selected-table">Lebih jelek dr diatas</td>
                                    <td style='text-align:center' class="selected-table">1</td>
                                <?php }else{?>
                                    <td style='text-align:left'>Lebih jelek dr diatas</td>
                                    <td style='text-align:center'>1</td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <td style='text-align:center; vertical-align: middle;' rowspan="2">10. </td>
                                <td style='text-align:center; vertical-align: middle;' rowspan="2">Pagar</td>
                                <?php if($data['fence']['fence']) {
                                    $score += 4;
                                ?>
                                    <td style='text-align:left;' class="selected-table">Ada</td>
                                    <td style='text-align:center;' class="selected-table">4</td>
                                <?php }else{?>
                                    <td style='text-align:left;'>Ada</td>
                                    <td style='text-align:center'>4</td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <?php if($data['fence']['no_fence']) {
                                    $score += 2;
                                ?>
                                    <td style='text-align:left' class="selected-table">Tidak Ada</td>
                                    <td style='text-align:center' class="selected-table">2</td>
                                <?php }else{?>
                                    <td style='text-align:left'>Tidak Ada</td>
                                    <td style='text-align:center'>2</td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <td style='text-align:center; vertical-align: middle;' rowspan="7">11. </td>
                                <td style='text-align:center; vertical-align: middle;' rowspan="7">Sarpras</td>
                                <?php if($data['sarpras']['mat']) {
                                    $score += 1;
                                ?>
                                    <td style='text-align:left;' class="selected-table">Tikar/Karpet</td>
                                    <td style='text-align:center;' class="selected-table">1</td>
                                <?php }else{?>
                                    <td style='text-align:left;'>Tikar/Karpet</td>
                                    <td style='text-align:center'>1</td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <?php if($data['sarpras']['ac']) {
                                    $score += 2;
                                ?>
                                    <td style='text-align:left' class="selected-table">AC</td>
                                    <td style='text-align:center' class="selected-table">2</td>
                                <?php }else{?>
                                    <td style='text-align:left'>AC</td>
                                    <td style='text-align:center'>2</td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <?php if($data['sarpras']['fan']) {
                                    $score += 1;
                                ?>
                                    <td style='text-align:left' class="selected-table">Kipas Angin</td>
                                    <td style='text-align:center' class="selected-table">1</td>
                                <?php }else{?>
                                    <td style='text-align:left'>Kipas Angin</td>
                                    <td style='text-align:center'>1</td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <?php if($data['sarpras']['sound']) {
                                    $score += 1;
                                ?>
                                    <td style='text-align:left' class="selected-table">Sound</td>
                                    <td style='text-align:center' class="selected-table">1</td>
                                <?php }else{?>
                                    <td style='text-align:left'>Sound</td>
                                    <td style='text-align:center'>1</td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <?php if($data['sarpras']['wudhu']) {
                                    $score += 1;
                                ?>
                                    <td style='text-align:left' class="selected-table">Tempat Wudhu</td>
                                    <td style='text-align:center' class="selected-table">1</td>
                                <?php }else{?>
                                    <td style='text-align:left'>Tempat Wudhu</td>
                                    <td style='text-align:center'>1</td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <?php if($data['sarpras']['parking']) {
                                    $score += 1;
                                ?>
                                    <td style='text-align:left' class="selected-table">Tempat Parkir</td>
                                    <td style='text-align:center' class="selected-table">1</td>
                                <?php }else{?>
                                    <td style='text-align:left'>Tempat Parkir</td>
                                    <td style='text-align:center'>1</td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <?php if($data['sarpras']['canopy']) {
                                    $score += 1;
                                ?>
                                    <td style='text-align:left' class="selected-table">Kanopi</td>
                                    <td style='text-align:center' class="selected-table">1</td>
                                <?php }else{?>
                                    <td style='text-align:left'>Kanopi</td>
                                    <td style='text-align:center'>1</td>
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
                                        <td style='text-align:center'>< 25</td>
                                        <td style='text-align:center'>Approve</td>
                                    </tr>
                                    <tr>
                                        <td style='text-align:center'>25 - 35</td>
                                        <td style='text-align:center'>Komite</td>
                                    </tr>
                                    <tr>
                                        <td style='text-align:center'>> 35</td>
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
                                        <?php if($score < 25){
                                            $score_result = "Aprrove";
                                        }else if($score >= 25 && $score <= 35){
                                            $score_result = "Komite";
                                        }else if($score > 35){
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