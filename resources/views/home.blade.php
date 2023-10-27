@extends('adminlte::page')

@section('title', 'Siabas')

{{-- @section('content_header')
    
Dashboard

@stop --}}

@section('content')
    
<br>

<div class="card border border-dark">
    <div class="card-header border-dark bg-dark">
        <h5 class="mb-0 float-left">
            Menu Utama
        </h5>
    </div>

    <div class="card-body">
        <div class="row">
            <div class='col-md-6'>
                <div class="card" style="height: 280px;">
                    <div class="card-header bg-secondary">
                    Layanan
                    </div>
                    <div class="card-body">
                    <ul class="list-group">
                    <?php foreach($menus as $menu){
                            if($menu['id_menu']==221){
                    ?>
                        <li class="list-group-item main-menu-item" onClick="location.href='{{route('service-requisition')}}'"> <i class="fa fa-angle-right"></i> Pengajuan Bantuan</li>
                    <?php   }
                            if($menu['id_menu']==231){
                    ?> 
                        <li class="list-group-item main-menu-item" onClick="location.href='{{route('service-disposition')}}'"> <i class="fa fa-angle-right"></i> Disposisi Bantuan</li>
                    <?php   }
                            if($menu['id_menu']==232){
                    ?> 
                        <li class="list-group-item main-menu-item" onClick="location.href='{{route('service-disposition-approval')}}'"> <i class="fa fa-angle-right"></i> Persetujuan Disposisi Bantuan</li>
                    <?php   }
                            if($menu['id_menu']==233){
                    ?> 
                        <li class="list-group-item main-menu-item" onClick="location.href='{{route('service-disposition-review')}}'"> <i class="fa fa-angle-right"></i> Review Disposisi Bantuan</li>            
                    <?php 
                            }
                        } 
                    ?>               
                    </ul>
                </div>
                </div>
            </div>
            <div class='col-md-6'>
                <div class="card" style="height: 280px;">
                    <div class="card-header bg-info">
                    Dashboard & Cetak
                    </div>
                    <div class="card-body scrollable">
                        <ul class="list-group">
                        <?php foreach($menus as $menu){
                            if($menu['id_menu']==41){
                        ?>
                            <li class="list-group-item main-menu-item" onClick="location.href='{{route('print-service')}}'"> <i class="fa fa-angle-right"></i> Cetak Data Bantuan</li>          
                        <?php   }
                            if($menu['id_menu']==42){
                        ?> 
                            <li class="list-group-item main-menu-item" onClick="location.href='{{route('service-general-print')}}'"> <i class="fa fa-angle-right"></i> Cetak Data Surat Umum</li>     
                        <?php   }
                            if($menu['id_menu']==6){
                        ?> 
                            <li class="list-group-item main-menu-item" onClick="location.href='{{route('dashboard-review')}}'"> <i class="fa fa-angle-right"></i> Dashboard Review</li>         
                        <?php 
                            }
                        } 
                        ?>           
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class='col-md-6'>
                <div class="card" style="height: 280px;">
                    <div class="card-header bg-info">
                    Surat Umum
                    </div>
                    <div class="card-body">
                    <ul class="list-group">
                    <?php foreach($menus as $menu){
                        if($menu['id_menu']==32){
                    ?>
                        <li class="list-group-item main-menu-item" onClick="location.href='{{route('service-general')}}'"> <i class="fa fa-angle-right"></i> Pengajuan Surat Umum</li>         
                    <?php   }
                        if($menu['id_menu']==33){
                    ?> 
                        <li class="list-group-item main-menu-item" onClick="location.href='{{route('service-general-approval')}}'"> <i class="fa fa-angle-right"></i> Persetujuan Surat Umum</li>  
                    <?php 
                            }
                        } 
                    ?>                    
                    </ul>
                </div>
                </div>
            </div>
            <div class='col-md-6'>
                <div class="card" style="height: 280px;">
                    <div class="card-header bg-secondary">
                    Notifikasi
                    </div>
                    <div class="card-body">
                    <ul class="list-group">
                    <?php foreach($menus as $menu){
                            if($menu['id_menu']==51){
                    ?>
                        <li class="list-group-item main-menu-item" onClick="location.href='{{route('messages')}}'"> <i class="fa fa-angle-right"></i> Pesan Notifikasi</li>
                    <?php   }
                            if($menu['id_menu']==52){
                    ?> 
                        <li class="list-group-item main-menu-item" onClick="location.href='{{route('scan-qr')}}'"> <i class="fa fa-angle-right"></i> Scan QR</li>      
                    <?php   }
                            if($menu['id_menu']==53){
                    ?> 
                            <li class="list-group-item main-menu-item" onClick="location.href='{{route('reload-scan-qr')}}'"> <i class="fa fa-angle-right"></i> Reload Service</li>  
                    <?php 
                            }
                        } 
                    ?>                        
                    </ul>
                </div>
                </div>
            </div>
        </div>
    </div>
</form>


@stop

@section('css')
    
@stop

@section('js')
    
@stop