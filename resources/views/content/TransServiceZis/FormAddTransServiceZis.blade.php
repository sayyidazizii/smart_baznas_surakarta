@inject('TransServiceZis', 'App\Http\Controllers\TransServiceZisController')

@extends('adminlte::page')

@section('title', 'Siabas')
@section('js')
    <script>
        function function_elements_add(name, value) {
            console.log("name " + name);
            console.log("value " + value);
            $.ajax({
                type: "POST",
                url: "{{ route('add-service-zis-item-elements') }}",
                data: {
                    'name': name,
                    'value': value,
                    '_token': '{{ csrf_token() }}'
                },
                success: function(msg) {}
            });
        }

        $(document).ready(function() {

            // $("#kecamatan_id").select2('val','0');
            // $("#kelurahan_id").select2('val','0');
            var service_zis_item_type = $("#service_zis_item_type").val();

            if (service_zis_item_type == '1') {
                $('#service_zis_item_remark').hide();
                $('#label-remark').hide();
            }

        });

        $("#service_zis_item_type").change(function() {
            var service_zis_item_type = $("#service_zis_item_type").val();

            if (service_zis_item_type == '1') {
                $('#service_zis_item_remark').hide();
                $('#label-remark').hide();
                $('#service_zis_item_amount').show();
                document.getElementById('label-nominal-text').innerHTML = 'Nominal';
                $('#label-nominal').show();
            } else if (service_zis_item_type == '2') {
                $('#service_zis_item_amount').show();
                document.getElementById('label-nominal-text').innerHTML = 'Taksiran';
                $('#label-nominal').show();
                $('#service_zis_item_remark').show();
                $('#label-remark').show();
            }

        });

        $("#kecamatan_id").change(function() {
            var kecamatan_id = $("#kecamatan_id").val();
            console.log(kecamatan_id);


        });

        $("#kecamatan_id").change(function() {
            var kecamatan_id = $("#kecamatan_id").val();
            $.ajax({
                type: "POST",
                url: "{{ route('service-zis-get-kelurahan') }}",
                dataType: "html",
                data: {
                    'kecamatan_id': kecamatan_id,
                    '_token': '{{ csrf_token() }}',
                },
                success: function(return_data) {
                    $('#kelurahan_id').html(return_data);
                    console.log(return_data);
                },
                error: function(data) {
                    console.log(data);

                }
            });
        });

        function addItem() {
            // var service_zis_item_name = $("#service_zis_item_name").val();
            // var service_zis_item_category = $("#service_zis_item_category").val();
            var service_zis_item_type = $("#service_zis_item_type").val();
            var service_zis_item_amount = $("#service_zis_item_amount").val();
            var service_zis_item_remark = $("#service_zis_item_remark").val();
                console.log(service_zis_item_type,
                    service_zis_item_amount, service_zis_item_remark);
            
                $.ajax({
                    type: "POST",
                    url: "{{ route('add-service_zis_item') }}",
                    dataType: "html",
                    data: {
                        'service_zis_item_type': service_zis_item_type,
                        'service_zis_item_amount': service_zis_item_amount,
                        'service_zis_item_remark': service_zis_item_remark,
                        '_token': '{{ csrf_token() }}',
                    },
                    success: function(return_data) {
                        $('#cancel_btn_bank').click();
                        location.reload();

                    },
                    error: function(data) {
                        console.log(data);
                    }
                });

        }
    </script>
@stop
@section('content_header')

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
            <li class="breadcrumb-item"><a href="{{ url('trans-service-zis') }}">Daftar Zakat Infaq Sedekah</a></li>
            <li class="breadcrumb-item active" aria-current="page">Tambah Zakat Infaq Sedekah</li>
        </ol>
    </nav>

@stop

@section('content')

    <h3 class="page-title">
        Form Tambah Zakat Infaq Sedekah
    </h3>
    <br />
    @if (session('msg'))
        <div class="alert alert-info" role="alert">
            {{ session('msg') }}
        </div>
    @endif
    <div class="card border border-dark">
        <div class="card-header border-dark bg-dark">
            <h5 class="mb-0 float-left">
                Form Tambah
            </h5>
            <div class="float-right">
                <button onclick="location.href='{{ url('trans-service-zis') }}'" name="Find" class="btn btn-sm btn-info"
                    title="Back"><i class="fa fa-angle-left"></i> Kembali</button>
            </div>
        </div>
        <form method="post" action="{{ route('process-add-service-zis') }}" enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                <div class="row form-group">
                    <div class="col-md-6">
                        <div class="form-group">
                            <a class="text-dark">Tanggal</a>
                            <input class="form-control input-bb" type="date" name="service_zis_date"
                                id="service_zis_date" onChange="function_elements_add(this.name, this.value);"
                                autocomplete="off" value="{{ $transservicezis['service_zis_date'] ?? '' }}" />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <a class="text-dark">Jenis</a>
                            {!! Form::select('service_zis_type', $service_zis_type, $transservicezis['service_zis_type'] ?? 1, [
                                'class' => 'selection-search-clear select-form',
                                'id' => 'service_zis_type',
                                'name' => 'service_zis_type',
                                'onChange' => 'function_elements_add(this.name, this.value);',
                            ]) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <a class="text-dark">Nama</a>
                            <input class="form-control input-bb" type="text" name="service_zis_name"
                                id="service_zis_name" onChange="function_elements_add(this.name, this.value);"
                                autocomplete="off" value="{{ $transservicezis['service_zis_name'] ?? '' }}" />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <a class="text-dark">No Hp</a>
                            <input class="form-control input-bb" type="text" name="service_zis_phone"
                                id="service_zis_phone" onChange="function_elements_add(this.name, this.value);"
                                autocomplete="off" value="{{ $transservicezis['service_zis_phone'] ?? '' }}" />
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <a class="text-dark">Kategori</a>
                            {!! Form::select('service_zis_category', $service_zis_category, $transservicezis['service_zis_category'] ?? 1, [
                                'class' => 'selection-search-clear select-form',
                                'id' => 'service_zis_category',
                                'name' => 'service_zis_category',
                                'onChange' => 'function_elements_add(this.name, this.value);',
                            ]) !!}
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <a class="text-dark">Alamat </a>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <a class="text-dark">kecamatan</a>
                            {!! Form::select('kecamatan_id', $kecamatan, $transservicezis['kecamatan_id'] ?? 0, [
                                'class' => 'selection-search-clear select-form',
                                'id' => 'kecamatan_id',
                                'name' => 'kecamatan_id',
                                'onChange' => 'function_elements_add(this.name, this.value);',
                            ]) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <a class="text-dark">Kelurahan</a>
                            {!! Form::select('kelurahan_id', $kelurahan, $transservicezis['kelurahan_id'] ?? 0, [
                                'class' => 'selection-search-clear select-form',
                                'id' => 'kelurahan_id',
                                'name' => 'kelurahan_id',
                                'onChange' => 'function_elements_add(this.name, this.value);',
                            ]) !!}
                        </div>
                        <input class="form-control input-bb" type="text" name="service_zis_address"
                        id="service_zis_address" onChange="function_elements_add(this.name, this.value);"
                        autocomplete="off" value="{{ $transservicezis['service_zis_address'] ?? '' }}"
                        placeholder="detail alamat" />
                    </div>

                    <div class="col-md-12">
                        <div class="form-group">
                            <a class="text-dark">Keterangan</a>
                            <input class="form-control input-bb" type="text" name="service_zis_remark"
                                id="service_zis_remark" onChange="function_elements_add(this.name, this.value);"
                                autocomplete="off" value="{{ $transservicezis['service_zis_remark'] ?? '' }}" />
                        </div>
                    </div>
                </div>
                <br />
                <div class="row">
                    <h5 class="form-section"><b>Detail Zakat infaq Sedekah</b></h5>
                </div>
                <hr style="margin:0;">
                <div class="table-responsive">
                    <div class="form-actions float-right">
                        <!-- Button trigger modal -->
                        <a href='#add' data-toggle='modal' name="Find" class="btn btn-success add-btn mb-3"
                            title="Add Data"><i
                            class="fa fa-plus"></i> Tambah Barang</a>
                    </div>
                    <table id="table" class="table table-bordered table-advance table-hover">
                        <thead class="thead-light">
                            <tr>
                                <th style='text-align:center' width='5%'>No</th>
                                <th style='text-align:center' width='10%'>Jenis</th>
                                <th style='text-align:center' width='20%'>Nominal</th>
                                <th style='text-align:center' width='20%'>Keterangan</th>
                                <th style='text-align:center' width='20%'>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            $jenis = '';
                            if(!is_array($transservicezisItem)){
                                echo "<tr><th colspan='9' style='text-align:center'>Data Kosong</th></tr>";
                            } else {
                                foreach ($transservicezisItem as $key=>$val){
                                    if($val['service_zis_item_type'] == 1){
                                        $jenis = 'uang';
                                    }else{
                                        $jenis = 'barang';
                                    }   
                                    echo"
                                        <tr>
                                            <td style='text-align  : left !important;'>".$no++."</td>
                                            <td style='text-align  : right !important;'>".$jenis."</td>
                                            <td style='text-align  : right !important;'>".$val['service_zis_item_amount']."</td>
                                            <td style='text-align  : right !important;'>".$val['service_zis_item_remark']."</td>
                                            ";
                                            
                                            ?>  
                            <td style='text-align  : center !important;'>
                                <a href="{{ route('delete-item-array-service-zis', ['record_id' => $key]) }}"
                                    name='Reset' class='btn btn-danger btn-sm'
                                    onClick='javascript:return confirm(\"apakah yakin ingin dihapus ?\")'></i> Hapus</a>
                            </td>
                            <?php
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
            <div class="card-footer text-muted">
                <div class="form-actions float-right">
                    <button type="reset" name="Reset" class="btn btn-danger" onClick="window.location.reload();"><i
                            class="fa fa-times"></i> Batal</button>
                    <button type="submit" name="Save" class="btn btn-primary" title="Save"><i
                            class="fa fa-check"></i> Simpan</button>
                </div>
            </div>
    </div>
    </div>
    </div>
    <br>
    <br>
    <br>
    </form>


    <div class="modal fade bs-modal-lg" id="add" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header" style='text-align:left !important'>
                    <h4>Form Tambah Barang</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <div class="form-group">
                                    <a class="text-dark">Jenis</a>
                                    {!! Form::select('service_zis_item_type', $service_zis_item_type, 1, [
                                        'class' => 'selection-search-clear select-form',
                                        'id' => 'service_zis_item_type',
                                        'name' => 'service_zis_item_type',
                                    ]) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div id="label-nominal" class="form-group">
                                <a id="label-nominal-text" class="text-dark">Nominal</a>
                                <input class="form-control input-bb" type="text" name="service_zis_item_amount"
                                    id="service_zis_item_amount" value="" />
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <a id="label-remark" class="text-dark">Keterangan</a>
                                <textarea class="form-control input-bb" type="text" name="service_zis_item_remark" id="service_zis_item_remark"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal"
                        id='cancel_btn_bank'>Batal</button>
                    <a class="btn btn-primary" onClick="addItem()">tambah</a>
                </div>
            </div>
        </div>
    </div>

@stop

@section('footer')

@stop

@section('css')

@stop
