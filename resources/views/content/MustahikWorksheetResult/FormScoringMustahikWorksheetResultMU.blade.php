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
                                <td style='text-align:center; vertical-align: middle;' rowspan="2">1. </td>
                                <td style='text-align:center; vertical-align: middle;' rowspan="2">Lokasi Usaha</td>
                                <?php if($data['business_location']['location_not_strategic'] == true) {
                                    $score += 2;
                                ?>
                                    <td style='text-align:left;' class="selected-table">Tidak Strategis</td>
                                    <td style='text-align:center;' class="selected-table">2</td>
                                <?php }else{?>
                                    <td style='text-align:left;'>Tidak Strategis</td>
                                    <td style='text-align:center'>2</td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <?php if($data['business_location']['location_strategic'] == true) {
                                    $score += 4;
                                ?>
                                    <td style='text-align:left' class="selected-table">Strategis</td>
                                    <td style='text-align:center' class="selected-table">4</td>
                                <?php }else{?>
                                    <td style='text-align:left'>Strategis</td>
                                    <td style='text-align:center'>4</td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <td style='text-align:center; vertical-align: middle;' rowspan="2">2. </td>
                                <td style='text-align:center; vertical-align: middle;' rowspan="2">Tipe Usaha</td>
                                <?php if($data['business_community']['community_group']) {
                                    $score += 2;
                                ?>
                                    <td style='text-align:left' class="selected-table">Kelompok</td>
                                    <td style='text-align:center' class="selected-table">2</td>
                                <?php }else{?>
                                    <td style='text-align:left'>Kelompok</td>
                                    <td style='text-align:center'>2</td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <?php if($data['business_community']['community_individual']) {
                                    $score += 4;
                                ?>
                                    <td style='text-align:left' class="selected-table">Personal</td>
                                    <td style='text-align:center' class="selected-table">4</td>
                                <?php }else{?>
                                    <td style='text-align:left'>Personal</td>
                                    <td style='text-align:center'>4</td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <td style='text-align:center; vertical-align: middle;' rowspan="4">3. </td>
                                <td style='text-align:center; vertical-align: middle;' rowspan="4">Lama Usaha</td>
                                <?php if(!$data['business_age'] || $data['business_age'] < 6) {
                                    $score += 1;
                                ?>
                                    <td style='text-align:left' class="selected-table">Dibawah 6 Bulan</td>
                                    <td style='text-align:center' class="selected-table">1</td>
                                <?php }else{?>
                                    <td style='text-align:left'>Dibawah 6 Bulan</td>
                                    <td style='text-align:center'>1</td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <?php if($data['business_age'] >= 6 && $data['business_age'] <= 12) {
                                    $score += 2;
                                ?>
                                    <td style='text-align:left' class="selected-table">6 Bulan - 1 Tahun</td>
                                    <td style='text-align:center' class="selected-table">2</td>
                                <?php }else{?>
                                    <td style='text-align:left'>6 Bulan - 1 Tahun</td>
                                    <td style='text-align:center'>2</td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <?php if($data['business_age'] >= 13 && $data['business_age'] <= 24) {
                                    $score += 3;
                                ?>
                                    <td style='text-align:left' class="selected-table">1 Tahun - 2 Tahun</td>
                                    <td style='text-align:center' class="selected-table">3</td>
                                <?php }else{?>
                                    <td style='text-align:left'>1 Tahun - 2 Tahun</td>
                                    <td style='text-align:center'>3</td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <?php if($data['business_age'] > 24) {
                                    $score += 4;
                                ?>
                                    <td style='text-align:left' class="selected-table">Diatas 2 Tahun</td>
                                    <td style='text-align:center' class="selected-table">4</td>
                                <?php }else{?>
                                    <td style='text-align:left'>Diatas 2 Tahun</td>
                                    <td style='text-align:center'>4</td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <td style='text-align:center; vertical-align: middle;' rowspan="4">4. </td>
                                <td style='text-align:center; vertical-align: middle;' rowspan="4">Jumlah Karyawan / Anggota</td>
                                <?php if($data['business_employee'] <= 0) {
                                    $score += 1;
                                ?>
                                    <td style='text-align:left' class="selected-table">Sendiri</td>
                                    <td style='text-align:center' class="selected-table">1</td>
                                <?php }else{?>
                                    <td style='text-align:left'>Sendiri</td>
                                    <td style='text-align:center'>1</td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <?php if($data['business_employee'] >= 1 && $data['business_employee'] <= 3) {
                                    $score += 2;
                                ?>
                                    <td style='text-align:left' class="selected-table">1 - 3 Orang</td>
                                    <td style='text-align:center' class="selected-table">2</td>
                                <?php }else{?>
                                    <td style='text-align:left'>1 - 3 Orang</td>
                                    <td style='text-align:center'>2</td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <?php if($data['business_employee'] >= 4 && $data['business_employee'] <= 10) {
                                    $score += 3;
                                ?>
                                    <td style='text-align:left' class="selected-table">4 - 10 Orang</td>
                                    <td style='text-align:center' class="selected-table">3</td>
                                <?php }else{?>
                                    <td style='text-align:left'>4 - 10 Orang</td>
                                    <td style='text-align:center'>3</td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <?php if($data['business_employee'] > 10) {
                                    $score += 4;
                                ?>
                                    <td style='text-align:left' class="selected-table">Diatas 10 Orang</td>
                                    <td style='text-align:center' class="selected-table">4</td>
                                <?php }else{?>
                                    <td style='text-align:left'>Diatas 10 Orang</td>
                                    <td style='text-align:center'>4</td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <td style='text-align:center; vertical-align: middle;' rowspan="5">5. </td>
                                <td style='text-align:center; vertical-align: middle;' rowspan="5">Margin Keuntungan</td>
                                <?php if($data['business_interest'] < 8) {
                                    $score += 1;
                                ?>
                                    <td style='text-align:left' class="selected-table">Dibawah 8%</td>
                                    <td style='text-align:center' class="selected-table">1</td>
                                <?php }else{?>
                                    <td style='text-align:left'>Dibawah 8%</td>
                                    <td style='text-align:center'>1</td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <?php if($data['business_interest'] >= 8 && $data['business_interest'] <= 10) {
                                    $score += 2;
                                ?>
                                    <td style='text-align:left' class="selected-table">8% - 10%</td>
                                    <td style='text-align:center' class="selected-table">2</td>
                                <?php }else{?>
                                    <td style='text-align:left'>8% - 10%</td>
                                    <td style='text-align:center'>2</td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <?php if($data['business_interest'] >= 11 && $data['business_interest'] <= 15) {
                                    $score += 3;
                                ?>
                                    <td style='text-align:left' class="selected-table">11% - 15%</td>
                                    <td style='text-align:center' class="selected-table">3</td>
                                <?php }else{?>
                                    <td style='text-align:left'>11% - 15%</td>
                                    <td style='text-align:center'>3</td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <?php if($data['business_interest'] >= 16 && $data['business_interest'] <= 20) {
                                    $score += 4;
                                ?>
                                    <td style='text-align:left' class="selected-table">16% - 20%</td>
                                    <td style='text-align:center' class="selected-table">4</td>
                                <?php }else{?>
                                    <td style='text-align:left'>16% - 20%</td>
                                    <td style='text-align:center'>4</td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <?php if($data['business_interest'] > 20) {
                                    $score += 5;
                                ?>
                                    <td style='text-align:left' class="selected-table">Diatas 20%</td>
                                    <td style='text-align:center' class="selected-table">5</td>
                                <?php }else{?>
                                    <td style='text-align:left'>Diatas 20%</td>
                                    <td style='text-align:center'>5</td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <td style='text-align:center; vertical-align: middle;' rowspan="4">6. </td>
                                <td style='text-align:center; vertical-align: middle;' rowspan="4">Pernah Mendapat Pinjaman / Bantuan</td>
                                <?php if($data['business_assistance_loans']['assistance_loans'] == true) {
                                    $score += 1;
                                ?>
                                    <td style='text-align:left' class="selected-table">Bantuan dan Pinjaman</td>
                                    <td style='text-align:center' class="selected-table">1</td>
                                <?php }else{?>
                                    <td style='text-align:left'>Bantuan dan Pinjaman</td>
                                    <td style='text-align:center'>1</td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <?php if($data['business_assistance_loans']['loans'] == true) {
                                    $score += 2;
                                ?>
                                    <td style='text-align:left' class="selected-table">Pinjaman</td>
                                    <td style='text-align:center' class="selected-table">2</td>
                                <?php }else{?>
                                    <td style='text-align:left'>Pinjaman</td>
                                    <td style='text-align:center'>2</td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <?php if($data['business_assistance_loans']['assistance'] == true) {
                                    $score += 3;
                                ?>
                                    <td style='text-align:left' class="selected-table">Bantuan</td>
                                    <td style='text-align:center' class="selected-table">3</td>
                                <?php }else{?>
                                    <td style='text-align:left'>Bantuan</td>
                                    <td style='text-align:center'>3</td>
                                <?php } ?>
                            </tr>
                            <tr>
                                <?php if($data['business_assistance_loans']['no_assistance_loans'] == true) {
                                    $score += 4;
                                ?>
                                    <td style='text-align:left' class="selected-table">Belum Pernah</td>
                                    <td style='text-align:center' class="selected-table">4</td>
                                <?php }else{?>
                                    <td style='text-align:left'>Belum Pernah</td>
                                    <td style='text-align:center'>4</td>
                                <?php } ?>
                            </tr>
                        <?php if($data['category']['asnaf_poor'] == true){?>
                            <tr>
                                <td style='text-align:center; vertical-align: middle;' rowspan="2">7. </td>
                                <td style='text-align:center; vertical-align: middle;' rowspan="2">SKTM</td>
                                <?php if($data['sktm']['no_sktm'] == true) {
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
                                <?php if($data['sktm']['sktm'] == true) {
                                    $score += 4;
                                ?>
                                    <td style='text-align:left' class="selected-table">Ada</td>
                                    <td style='text-align:center' class="selected-table">4</td>
                                <?php }else{?>
                                    <td style='text-align:left'>Ada</td>
                                    <td style='text-align:center'>4</td>
                                <?php } ?>
                            </tr>
                        <?php }?>
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
                            <?php if($data['category']['asnaf_poor'] == true){?>
                                <tbody>
                                    <tr>
                                        <td style='text-align:center'>>= 22</td>
                                        <td style='text-align:center'>Approve</td>
                                    </tr>
                                    <tr>
                                        <td style='text-align:center'>9 - 21</td>
                                        <td style='text-align:center'>Komite</td>
                                    </tr>
                                    <tr>
                                        <td style='text-align:center'><= 8</td>
                                        <td style='text-align:center'>Reject</td>
                                    </tr>
                                </tbody>
                            <?php }else{ ?>
                                <tbody>
                                    <tr>
                                        <td style='text-align:center'>>= 20</td>
                                        <td style='text-align:center'>Approve</td>
                                    </tr>
                                    <tr>
                                        <td style='text-align:center'>9 - 19</td>
                                        <td style='text-align:center'>Komite</td>
                                    </tr>
                                    <tr>
                                        <td style='text-align:center'><= 8</td>
                                        <td style='text-align:center'>Reject</td>
                                    </tr>
                                </tbody>
                            <?php }?>
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
                                <?php if($data['category']['asnaf_poor'] == true){?>
                                    <tbody>
                                        <tr>
                                            <?php if($score >= 22){
                                                $score_result = "Aprrove";
                                            }else if($score >= 9 && $score <= 21){
                                                $score_result = "Komite";
                                            }else if($score <= 8){
                                                $score_result = "Reject";
                                            } 
                                            ?>
                                            <td style='text-align:center'>{{$score_result}}</td>
                                        </tr>
                                    </tbody>
                                <?php }else{ ?>
                                    <tbody>
                                        <tr>
                                            <?php if($score >= 20){
                                                $score_result = "Aprrove";
                                            }else if($score >= 9 && $score <= 19){
                                                $score_result = "Komite";
                                            }else if($score <= 8){
                                                $score_result = "Reject";
                                            } 
                                            ?>
                                            <td style='text-align:center'>{{$score_result}}</td>
                                        </tr>
                                    </tbody>
                                <?php }?>
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