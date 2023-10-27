@inject('TransServiceZis', 'App\Http\Controllers\TransServiceZisController')

@extends('adminlte::page')

@section('title', 'Siabas')

@section('content_header')

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url('home') }}">Beranda</a></li>
            <li class="breadcrumb-item active" aria-current="page">Daftar Zakat Infaq Sedekah</li>
        </ol>
    </nav>

@stop

@section('content')

    <h3 class="page-title">
        <b>Daftar Zakat Infaq Sedekah</b> <small>Mengelola Zakat Infaq Sedekah</small>
    </h3>
    <br />
    <div id="accordion">
        <form method="post" action="{{ route('filter-service-zis') }}" enctype="multipart/form-data">
            @csrf
            <div class="card border border-dark">
                <div class="card-header bg-dark" id="headingOne" data-toggle="collapse" data-target="#collapseOne"
                    aria-expanded="true" aria-controls="collapseOne">
                    <h5 class="mb-0">
                        Filter
                    </h5>
                </div>

                <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordion">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group form-md-line-input">
                                    <section class="control-label">Tanggal Mulai
                                        <span class="required text-danger">
                                            *
                                        </span>
                                    </section>
                                    <input type="date"
                                        class="form-control form-control-inline input-medium date-picker input-date"
                                        data-date-format="dd-mm-yyyy" type="text" name="start_date" id="start_date"
                                        onChange="function_elements_add(this.name, this.value);" value="{{ $start_date }}"
                                        style="width: 15rem;" />
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group form-md-line-input">
                                    <section class="control-label">Tanggal Akhir
                                        <span class="required text-danger">
                                            *
                                        </span>
                                    </section>
                                    <input type="date"
                                        class="form-control form-control-inline input-medium date-picker input-date"
                                        data-date-format="dd-mm-yyyy" type="text" name="end_date" id="end_date"
                                        onChange="function_elements_add(this.name, this.value);" value="{{ $end_date }}"
                                        style="width: 15rem;" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-muted">
                        <div class="form-actions float-right">
                            <button type="reset" name="Reset" class="btn btn-danger"
                                onClick="window.location.reload();"><i class="fa fa-times"></i> Batal</button>
                            <button type="submit" name="Find" class="btn btn-primary" title="Search Data"><i
                                    class="fa fa-search"></i> Cari</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <br />
    @if (session('msg'))
        <div class="alert alert-info" role="alert">
            {{ session('msg') }}
        </div>
    @endif
    <div class="card border border-dark">
        <div class="card-header bg-dark clearfix">
            <h5 class="mb-0 float-left">
                Daftar
            </h5>
            <div class="form-actions float-right">
                <button onclick="location.href='{{ url('/trans-service-zis/add') }}'" name="Find"
                    class="btn btn-sm btn-info" title="Add Data"><i class="fa fa-plus"></i> Tambah Zakat Infaq Baru</button>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table id="example" style="width:100%"
                    class="table table-striped table-bordered table-hover table-full-width">
                    <thead>
                        <tr>
                            <th width="5%" style='text-align:center'>No</th>
                            <th width="10%" style='text-align:center'>Jenis </th>
                            <th width="10%" style='text-align:center'>Kategori</th>
                            <th width="10%" style='text-align:center'>Tanggal </th>
                            <th width="10%" style='text-align:center'>Nama</th>
                            <th width="15%" style='text-align:center'>Kelurahan</th>
                            <th width="15%" style='text-align:center'>Kecamatan</th>
                            <th width="20%" style='text-align:center'>Alamat</th>
                            <th width="10%" style='text-align:center'>No HP</th>
                            <th width="20%" style='text-align:center'>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $no = 1;
                        @endphp
                        @foreach ($transservicezis as $row)
                            <tr>
                                <td>{{ $no++ }}</td>
                                {{-- jenis --}}
                                <?php if($row->service_zis_type == 1){ ?>
                                <td>Instansi</td>
                                <?php } ?>
                                <?php if($row->service_zis_type == 2){ ?>
                                <td>Perorangan</td>
                                <?php } ?>

                                {{-- kategori --}}
                                <?php if($row->service_zis_category == 1){ ?>
                                <td>Zakat</td>
                                <?php } ?>
                                <?php if($row->service_zis_category == 2){ ?>
                                <td>Infaq</td>
                                <?php } ?>
                                <?php if($row->service_zis_category == 3){ ?>
                                <td>Sedekah</td>
                                <?php } ?>



                                <td>{{ $row->service_zis_date }}</td>
                                <td>{{ $row->service_zis_name }}</td>
                                <td>{{ $TransServiceZis->getKelurahanName($row->kelurahan_id) }}</td>
                                <td>{{ $TransServiceZis->getKecamatanName($row->kecamatan_id) }}</td>
                                <td>{{ $row->service_zis_address }}</td>
                                <td>{{ $row->service_zis_phone }}</td>
                                <td>
                                    <a href="{{ url('/trans-service-zis/export-single/' . $row['service_zis_id']) }}"
                                    class="btn btn-outline-secondary    ">print</a>
                                    <a href="{{ url('/trans-service-zis/detail/' . $row['service_zis_id']) }}"
                                        class="btn btn-outline-info">detail</a>
                                    <a href="{{ url('/trans-service-zis/edit/' . $row['service_zis_id']) }}"
                                        class="btn btn-outline-warning">Edit</a>
                                    <a href="{{ url('/trans-service-zis/process-delete/' . $row['service_zis_id']) }}"
                                        class="btn btn-outline-danger"
                                        onclick="return confirm('yakin untuk menghapus data ini?');">Hapus</a>

                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
                <div class="card-footer text-muted">
                    <div class="form-actions float-right">
                        <a href="{{ url('trans-service-zis/export') }}"name="Find" class="btn btn-info"
                            title="Export"><i class="fa fa-print"></i>Export</a>
                    </div>
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

@section('js')

@stop
