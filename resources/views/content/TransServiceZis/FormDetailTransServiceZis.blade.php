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
                url: "{{ route('add-service-elements') }}",
                data: {
                    'name': name,
                    'value': value,
                    '_token': '{{ csrf_token() }}'
                },
                success: function(msg) {}
            });
        }
    </script>
@stop
@section('content_header')

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
            <li class="breadcrumb-item"><a href="{{ url('trans-service-zis') }}">Daftar Zakat Infaq Sedekah</a></li>
            <li class="breadcrumb-item active" aria-current="page">Detail Zakat Infaq Sedekah</li>
        </ol>
    </nav>

@stop

@section('content')

    <h3 class="page-title">
        Form Detail Zakat Infaq Sedekah
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
                Form Detail
            </h5>
            <div class="float-right">
                <button onclick="location.href='{{ url('trans-service-zis') }}'" name="Find" class="btn btn-sm btn-info"
                    title="Back"><i class="fa fa-angle-left"></i> Kembali</button>
            </div>
        </div>

        @php
            $type = 0;
            if ($transservicezis->service_zis_type == 1) {
                $type = 'Instansi';
            } elseif ($transservicezis->service_zis_type == 2) {
                $type = 'Perorangan';
            }
        @endphp

        @php
            $category = 0;
            if ($transservicezis->service_zis_category == 1) {
                $category = 'Zakat';
            } elseif ($transservicezis->service_zis_category == 2) {
                $category = 'Infaq';
            }elseif ($transservicezis->service_zis_category == 3) {
                $category = 'Sedekah';
            }
        @endphp
        {{-- <form method="post" action="{{route('process-add-service-requisition')}}" enctype="multipart/form-data"> --}}
        @csrf
        <div class="card-body">
            <div class="row form-group">
                <div class="col-md-3">
                    <div class="form-group">
                        <a class="text-dark">Tanggal</a>
                        <input class="form-control input-bb" type="text" name="service_requisition_nik"
                            id="service_requisition_nik" value="{{ $transservicezis->service_zis_date }}"
                            onChange="function_elements_add(this.name, this.value);" autocomplete="off" readonly />
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <a class="text-dark">Jenis</a>
                        <input class="form-control input-bb" type="text" name="service_requisition_type"
                            id="service_requisition_type" value="{{ $type }}"
                            onChange="function_elements_add(this.name, this.value);" autocomplete="off" readonly />
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <a class="text-dark">Nama</a>
                        <input class="form-control input-bb" type="text" name="service_requisition_name"
                            id="service_requisition_name" value="{{ $transservicezis->service_zis_name }}"
                            onChange="function_elements_add(this.name, this.value);" autocomplete="off" readonly />
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <a class="text-dark">No Hp</a>
                        <input class="form-control input-bb" type="text" name="service_requisition_phone"
                            id="service_requisition_phone" value="{{ $transservicezis->service_zis_phone }}"
                            onChange="function_elements_add(this.name, this.value);" autocomplete="off" readonly />
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <a class="text-dark">Kategori </a>
                        <input class="form-control input-bb" type="text" name="service_requisition_phone"
                            id="service_requisition_phone" value="{{ $category }}"
                            onChange="function_elements_add(this.name, this.value);" autocomplete="off" readonly />
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <a class="text-dark">kelurahan </a>
                        <input class="form-control input-bb" type="text" name="service_requisition_phone"
                            id="service_requisition_phone"
                            value="{{ $TransServiceZis->getKelurahanName($transservicezis->kelurahan_id) }}"
                            onChange="function_elements_add(this.name, this.value);" autocomplete="off" readonly />
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <a class="text-dark">Kecamatan </a>
                        <input class="form-control input-bb" type="text" name="service_requisition_phone"
                            id="service_requisition_phone"
                            value="{{ $TransServiceZis->getKecamatanName($transservicezis->kecamatan_id) }}"
                            onChange="function_elements_add(this.name, this.value);" autocomplete="off" readonly />
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <a class="text-dark">Alamat </a>
                        <input class="form-control input-bb" type="text" name="service_requisition_phone"
                            id="service_requisition_phone" value="{{ $transservicezis->service_zis_address }}"
                            onChange="function_elements_add(this.name, this.value);" autocomplete="off" readonly />
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="form-group">
                        <a class="text-dark">Keterangan</a>
                        <input class="form-control input-bb" type="text" name="service_requisition_phone"
                            id="service_requisition_phone" value="{{ $transservicezis->service_zis_remark }}"
                            onChange="function_elements_add(this.name, this.value);" autocomplete="off" readonly />
                    </div>
                </div>
            </div>

            <br />
            <div class="row">
                <h5 class="form-section"><b>Detail Zakat infaq Sedekah</b></h5>
            </div>
            <hr style="margin:0;">
            <br />
            <div class="table-responsive">
                <table class="table table-bordered table-advance table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th style='text-align:center' width='5%'>No</th>
                            <th style='text-align:center' width='10%'>Jenis</th>
                            <th style='text-align:center' width='20%'>Nominal</th>
                            <th style='text-align:center' width='20%'>Keterangan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $no = 1;
                        @endphp
                        @foreach ($transservicezisItem as $row)
                            <tr>
                                <td>{{ $no }}</td>

                                <?php if( $row->service_zis_item_type == 1){ ?>
                                <td>Uang</td>
                                <?php }?>
                                <?php if( $row->service_zis_item_type == 2){ ?>
                                <td>Barang</td>
                                <?php }?>
                                <td style='text-align:right'>{{ number_format($row->service_zis_item_amount) }}</td>
                                <td>{{ $row->service_zis_item_remark }}</td>
                            </tr>
                            @php
                                $no++;
                            @endphp
                        @endforeach
                    </tbody>
                </table>
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
