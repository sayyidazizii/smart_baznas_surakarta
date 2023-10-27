@inject('CustomerSatisfactionReport', 'App\Http\Controllers\CustomerSatisfactionReportController')

@extends('adminlte::page')

@section('title', 'Siabas')

@section('js')

@stop

@section('content_header')
    
<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
        <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
        <li class="breadcrumb-item active" aria-current="page">Kepuasan Pelanggan</li>
    </ol>
</nav>

@stop

@section('content')

<h3 class="page-title">
    <b>Laporan Kepuasan Pelanggan</b>
</h3>
<br/>
<div id="accordion">
    <form  method="post" action="{{route('filter-customer-satisfaction-report')}}" enctype="multipart/form-data">
    @csrf
        <div class="card border border-dark">
        <div class="card-header bg-dark" id="headingOne" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
            <h5 class="mb-0">
                Filter
            </h5>
        </div>
    
        <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
            <div class="card-body">
                <div class = "row">
                    <div class = "col-md-6">
                        <div class="form-group form-md-line-input">
                            <section class="control-label">Tanggal Mulai
                                <span class="required text-danger">
                                    *
                                </span>
                            </section>
                            <input type ="date" class="form-control form-control-inline input-medium date-picker input-date" data-date-format="dd-mm-yyyy" type="text" name="start_date" id="start_date" onChange="function_elements_add(this.name, this.value);" value="{{$start_date}}" style="width: 15rem;"/>
                        </div>
                    </div>

                    <div class = "col-md-6">
                        <div class="form-group form-md-line-input">
                            <section class="control-label">Tanggal Akhir
                                <span class="required text-danger">
                                    *
                                </span>
                            </section>
                            <input type ="date" class="form-control form-control-inline input-medium date-picker input-date" data-date-format="dd-mm-yyyy" type="text" name="end_date" id="end_date" onChange="function_elements_add(this.name, this.value);" value="{{$end_date}}" style="width: 15rem;"/>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer text-muted">
                <div class="form-actions float-right">
                    <button type="reset" name="Reset" class="btn btn-danger" onClick="window.location.reload();"><i class="fa fa-times"></i> Batal</button>
                    <button type="submit" name="Find" class="btn btn-primary" title="Search Data"><i class="fa fa-search"></i> Cari</button>
                </div>
            </div>
        </div>
        </div>
    </form>
</div>
<br/>
@if(session('msg'))
<div class="alert alert-info" role="alert">
    {{session('msg')}}
</div>
@endif 
<div class="card border border-dark">
    <div class="card-header bg-dark clearfix">
        <h5 class="mb-0 float-left">
            List
        </h5>
        <div class="form-actions float-right">
            <button onclick="location.href='{{ url('customer-satisfaction-report/export') }}'" name="Export" class="btn btn-sm btn-info" title="Export Data"><i class="fa fa-print"></i> Export Data</button>
        </div>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table id="example" style="width:100%" class="table table-striped table-bordered table-hover table-full-width">
                <thead>
                    <tr>
                        <th width="5%" style='text-align:center'>No</th>
                        <?php 
                        $temp_date  = $start_date;
                        $last_date  = date('Y-m-d', strtotime("+1 day", strtotime($end_date)));

                        while($temp_date != $last_date){
                            $day        = date('d', strtotime($temp_date));
                        ?>
                            <th width="10%" style='text-align:center'>{{$day}}_N</th>
                            <th width="10%" style='text-align:center'>{{$day}}_Y</th>
                        <?php 
                            $temp_date = date('Y-m-d', strtotime("+1 day", strtotime($temp_date)));
                        } ?>
                        <th width="20%" style='text-align:center'>Total Tidak Puas</th>
                        <th width="20%" style='text-align:center'>Total Puas</th>
                        <th width="20%" style='text-align:center'>Total Kunjungan</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td width="5%" style='text-align:center'>1</td>
                        <?php 
                        $temp_date  = $start_date;
                        $last_date  = date('Y-m-d', strtotime("+1 day", strtotime($end_date)));
                        $n_total    = 0;
                        $y_total    = 0;

                        while($temp_date != $last_date){
                            $n_amount   = $CustomerSatisfactionReport->getNoAmount($temp_date);
                            $y_amount   = $CustomerSatisfactionReport->getYesAmount($temp_date);
                            $n_total   += $n_amount;
                            $y_total   += $y_amount;
                        ?>
                            <td width="10%" style='text-align:center'>{{$n_amount}}</td>
                            <td width="10%" style='text-align:center'>{{$y_amount}}</td>
                        <?php 
                            $temp_date = date('Y-m-d', strtotime("+1 day", strtotime($temp_date)));
                        } ?>
                        <td width="20%" style='text-align:center'>{{$n_total}}</td>
                        <td width="20%" style='text-align:center'>{{$y_total}}</td>
                        <td width="20%" style='text-align:center'>{{$n_total + $y_total}}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>

@stop

@section('footer')
    
@stop

@section('css')
    
@stop