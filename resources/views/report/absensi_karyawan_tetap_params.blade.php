@extends('layouts.backend')

@section('other-css')
    <link rel="stylesheet" href="{{ asset('vendor/formvalidation/formValidation.css') }}">
    <!-- bootstrap datepicker -->
    <link rel="stylesheet" href="{{ asset('bower_components/AdminLTE/plugins/datepicker/datepicker3.css') }}">
@endsection

@section('content')
    
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Laporan Absensi Karyawan Tetap
        <small>&nbsp;</small>
      </h1>
        
      <ol class="breadcrumb">
        <li><a href="{{ url('/') }}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active"> Laporan Absensi Karyawan Tetap/li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">

      <!-- Your Page Content Here -->
      
      <div class="row">
        
        <div class="col-xs-12">
          
          <div class="box box-primary">
            
            <div class="box-header with-border">
              <h3 class="box-title">Parameters</h3>
            </div>
            <!-- /.box-header -->
            
            <!-- form start -->
            <form class="form-horizontal" id="frmData" method="post" action="{{ url('/report/absensi-karyawan-tetap/preview') }}" autocomplete="off">
              
              {{ csrf_field() }}
              
              <div class="box-body">
                
                <div class="form-group">
                  <label for="tanggal_lahir" class="col-sm-2 control-label">Bulan *</label>
                  <div class="col-sm-10">
                    <div class="input-group date">
                      <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                      </div>
                      <select class="form-control" name="bulan" id="bulan">

                           <option value="">-- Pilih Bulan --</option>
                           <option value="1"> Januari</option>
                           <option value="2"> Februari</option>
                           <option value="3"> Maret</option>
                           <option value="4"> April</option>
                           <option value="5"> Mei</option>
                           <option value="6"> Juni</option>
                           <option value="7"> Juli</option>
                           <option value="8"> Agustus</option>
                           <option value="9"> September</option>
                           <option value="10"> Oktober</option>
                           <option value="11"> November</option>
                           <option value="12"> Desember</option>
                           
                                 
                      </select>
                    </div>
                  </div>
                </div>
                
              <!-- /.box-body -->
              
              <div class="box-footer">
                <div class="btn-group pull-right">
                  <button type="submit" class="btn btn-success" id="btnSubmit" style="margin-left: 5px;"><i class="fa fa-search fa-fw"></i> Preview</button>
                </div>
              </div>
              <!-- /.box-footer -->
            
            </form>
          
          </div>
          <!-- /.box -->
        
        </div>
        <!-- /.col -->
      
      </div>
      <!-- /.row -->

    </section>
    <!-- /.content -->
    <script type="text/javascript">
    $(document).ready(function(){
    reportAbsensiKaryawanTetapModule.init();
    });
    </script>

@endsection

@section('other-js')
    <script src="{{ asset('vendor/formvalidation/formValidation.min.js') }}"></script>
    <script src="{{ asset('vendor/formvalidation/framework/bootstrap.min.js') }}"></script>
    <!-- bootstrap datepicker -->
    <script src="{{ asset('bower_components/AdminLTE/plugins/datepicker/bootstrap-datepicker.js') }}"></script>
      
    <!-- page script -->
    <script type="text/javascript">
    $(document).ready(function() {
        advanceElements.init();
        validation.init();
    });
    </script>
@endsection