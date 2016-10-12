@extends('layouts.backend')

@section('other-css')
    <!-- bootstrap datepicker -->
    <link rel="stylesheet" href="{{ asset('bower_components/AdminLTE/plugins/datepicker/datepicker3.css') }}">
    <!-- bootstrap select -->
    <link rel="stylesheet" href="{{ asset('bower_components/bootstrap-select/dist/css/bootstrap-select.min.css') }}">
    <link rel="stylesheet" href="{{ asset('bower_components/sweetalert/dist/sweetalert.css') }}">
@endsection

@section('content')
    
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Invoice Penjualan
        <small>List</small>
      </h1>
        
      <ol class="breadcrumb">
        <li><a href="{{ url('/') }}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Invoice Penjualan</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">

      <!-- Your Page Content Here -->
      
      <div class="row">
        
        <div class="col-xs-12">
          
          <!-- search container -->
          <div class="box box-info collapsed-box">
            
            <div class="box-header">
              <h3 class="box-title">Pencarian</h3>
              <!-- tools box -->
              <div class="pull-right box-tools">
                <button type="button" class="btn btn-info btn-sm" data-widget="collapse" data-toggle="tooltip" title="Collapse">
                <i class="fa fa-plus"></i></button>
              </div>
              <!-- /. tools -->
            </div>
            <!-- /.box-header -->
            
            <!-- form start -->
            <form class="form-horizontal" method="post" id="search-form" autocomplete="off">
              <div class="box-body">
                <div class="form-group">
                  <label for="no_invoice" class="col-sm-2 control-label">No. Invoice</label>
                  <div class="col-sm-3">
                    <input type="text" class="form-control" name="no_invoice" id="no_invoice" placeholder="No. Invoice">
                  </div>
                  <label for="no_po" class="col-sm-2 control-label">No. PO</label>
                  <div class="col-sm-3">
                    <input type="text" class="form-control" name="no_po" id="no_po" placeholder="No. PO">
                  </div>
                </div>
                <div class="form-group">
                  <label for="no_surat_jalan" class="col-sm-2 control-label">No. Surat Jalan</label>
                  <div class="col-sm-3">
                    <input type="text" class="form-control" name="no_surat_jalan" id="no_surat_jalan" placeholder="No. Surat Jalan">
                  </div>
                  <label for="no_po" class="col-sm-2 control-label">Tgl. Jatuh Tempo</label>
                  <div class="col-sm-3">
                    <div class="input-group date">
                      <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                      </div>
                      <input type="text" class="form-control" name="tanggal_jatuh_tempo" id="tanggal_jatuh_tempo" placeholder="Tanggal Jatuh Tempo">
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <label for="konsumen_id" class="col-sm-2 control-label">Konsumen</label>
                  <div class="col-sm-3">
                    <select name="konsumen_id" id="konsumen_id" class="form-control selectpicker" title="-- Pilih konsumen --">
                      @foreach($konsumen as $item)
                        <option value="{{ $item->id }}">{{ $item->nama }}</option>
                      @endforeach
                    </select>
                  </div>
                  <label for="tujuan_id" class="col-sm-2 control-label">Tujuan</label>
                  <div class="col-sm-3">
                    <select name="tujuan_id" id="tujuan_id" class="form-control selectpicker" title="-- Pilih tujuan --">
                      @foreach($tujuan as $item)
                        <option value="{{ $item->id }}">{{ $item->nama }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
                <div class="form-group">
                  <label for="angkutan_id" class="col-sm-2 control-label">Angkutan</label>
                  <div class="col-sm-3">
                    <select name="angkutan_id" id="angkutan_id" class="form-control selectpicker" title="-- Pilih angkutan --">
                      @foreach($angkutan as $item)
                        <option value="{{ $item->id }}">{{ $item->nama }}</option>
                      @endforeach
                    </select>
                  </div>
                  <label for="no_mobil" class="col-sm-2 control-label">No. Mobil</label>
                  <div class="col-sm-3">
                    <input type="text" class="form-control" name="no_mobil" id="no_mobil" placeholder="No. Mobil">
                  </div>
                </div>
                
              </div>
              <!-- /.box-body -->
              <div class="box-footer">
                <div class="btn-group pull-right">
                  <button id="btnReset" type="button" class="btn bg-maroon" style="margin-right: 5px;">Reset</button>
                  <button type="submit" class="btn bg-purple">Search</button>
                </div>
              </div>
              <!-- /.box-footer -->
            </form>
            <!-- /form -->
          
          </div>
          <!-- /.box -->
          
          <!-- datatables container -->
          <div class="box box-primary">
            
            <div class="box-body">
              
              <div class="btn-group">
                <a href="{{ url('/invoice/create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> Add</a>
              </div>
              <br><br>
              
              <div class="table-responsive">
                    
                <table id="list" class="table table-bordered table-striped table-condensed"  style="table-layout: fixed; width:200%">
                  <thead>
                    <tr>
                      <th class="text-center">Tanggal</th>
                      <th class="text-center">No. Invoice</th>
                      <th class="text-center">Total</th>
                      <th class="text-center">Jth. Tempo</th>
                      <th class="text-center">Konsumen</th>
                      <th class="text-center">Tujuan</th>
                      <th class="text-center">Angkutan</th>
                      <th class="text-center">No. PO</th>
                      <th class="text-center">No. Srt. Jln.</th>
                      <th class="text-center">No. Mobil</th>
                      <th class="text-center">Status Bayar</th>
                      <th class="text-center">Tgl. Bayar</th>
                      <th class="text-center">Bank Tujuan</th>
                      <th class="text-center">Keterangan</th>
                      <th class="text-center" width="7%">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                  </tbody>
                </table>
              
              </div>
              <!-- end table-responsive -->
            
            </div>
            <!-- /.box-body -->
          
          </div>
          <!-- /.box -->
        
        </div>
        <!-- /.col -->
      
      </div>
      <!-- /.row -->

    </section>
    <!-- /.content -->
    
@endsection

@section('other-js')
    <!-- bootstrap datepicker -->
    <script src="{{ asset('bower_components/AdminLTE/plugins/datepicker/bootstrap-datepicker.js') }}"></script>
    <!-- bootstrap select -->
    <script src="{{ asset('bower_components/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('bower_components/sweetalert/dist/sweetalert.min.js') }}"></script>
    <script src="{{ asset('js/invoice.js') }}"></script>
      
    <!-- page script -->
    <script type="text/javascript">
    $(document).ready(function() {
        searchbox.init();
        datatables.init();
    });
    </script>
@endsection
