@extends('layouts.backend')

@section('other-css')
    <link rel="stylesheet" href="{{ asset('vendor/formvalidation/formValidation.css') }}">
    <!-- bootstrap select -->
    <link rel="stylesheet" href="{{ asset('bower_components/bootstrap-select/dist/css/bootstrap-select.min.css') }}">
    <link rel="stylesheet" href="{{ asset('bower_components/sweetalert/dist/sweetalert.css') }}">
@endsection

@section('content')
    
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Pembayaran Angkutan
        <small>List</small>
      </h1>
        
      <ol class="breadcrumb">
        <li><a href="{{ url('/') }}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Pembayaran Angkutan</li>
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
                  <label for="no_surat_jalan" class="col-sm-2 control-label">No. Surat Jalan</label>
                  <div class="col-sm-3">
                    <input type="text" class="form-control" name="no_surat_jalan" id="no_surat_jalan" placeholder="No. Surat Jalan">
                  </div>
                  <label for="no_mobil" class="col-sm-2 control-label">No. Mobil</label>
                  <div class="col-sm-3">
                    <input type="text" class="form-control" name="no_mobil" id="no_mobil" placeholder="No. Mobil">
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
                  <label for="konsumen_id" class="col-sm-2 control-label">Konsumen</label>
                  <div class="col-sm-3">
                    <select name="konsumen_id" id="konsumen_id" class="form-control selectpicker" title="-- Pilih konsumen --">
                      @foreach($konsumen as $item)
                        <option value="{{ $item->id }}">{{ $item->nama }}</option>
                      @endforeach
                    </select>
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
          
          <div class="box box-primary">
            
            <div class="box-body">
              
              <br>
              
              <div class="table-responsive">
                    
                <table id="list" class="table table-bordered table-striped table-condensed" style="table-layout: fixed; width:140%">
                  <thead>
                    <tr>
                      <th class="text-center">No. Surat Jalan</th>
                      <th class="text-center">Nama Angkutan</th>
                      <th class="text-center">No. Mobil</th>
                      <th class="text-center">Tujuan</th>
                      <th class="text-center">Biaya Angkutan</th>
                      <th class="text-center">Potongan</th>
                      <th class="text-center">Jumlah</th>
                      <th class="text-center">Status</th>
                      <th class="text-center">Tgl. Bayar</th>
                      <th class="text-center">Keterangan</th>
                      <th class="text-center" width="4%">Action</th>
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
    
    <div class="modal fade" id="confirmModal">
        <div class="modal-dialog">
          <div class="modal-content">
            
            <form id="frmModal" method="post" class="form-horizontal" action="{{ url('/pembayaran-angkutan') }}" autocomplete="off">
            
             {{ csrf_field() }}
             
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
              <h4 class="modal-title">Konfirmasi Pembayaran</h4>
            </div>
            <!-- /.modal-header -->
            <div class="modal-body">
              
              <div class="form-group">
                  <label class="col-xs-4 control-label">Tanggal Pembayaran *</label>
                  <div class="col-xs-6">
                      <input type="text" class="form-control" name="tanggal_bayar" id="tanggal_bayar" placeholder="Tanggal bayar" value="{{ $default_date }}" >
                  </div>
              </div>
              <div class="form-group">
                  <label class="col-xs-4 control-label">No. Surat Jalan</label>
                  <div class="col-xs-6">
                      <input type="text" style="background: #EDF7FA;" class="form-control" name="no_srt_jln" id="no_srt_jln" placeholder="No. Surat Jalan" readonly >
                  </div>
              </div>
              <div class="form-group">
                  <label class="col-xs-4 control-label">Biaya Angkutan</label>
                  <div class="col-xs-6">
                      <input type="text" style="background: #EDF7FA;" class="form-control text-right" name="biaya_angkutan" id="biaya_angkutan" placeholder="Biaya Angkutan" readonly >
                  </div>
              </div>
              <div class="form-group">
                  <label class="col-xs-4 control-label">Potongan</label>
                  <div class="col-xs-6">
                      <input type="text" class="form-control text-right" name="discount" id="discount" placeholder="Potongan" >
                  </div>
              </div>
              <div class="form-group">
                  <label class="col-xs-4 control-label">Jumlah Pembayaran</label>
                  <div class="col-xs-6">
                      <input type="text" style="background: #EDF7FA;" class="form-control text-right" name="jumlah_bayar" id="jumlah_bayar" placeholder="Jumlah Pembayaran" readonly >
                  </div>
              </div>
              <!--<div class="form-group">
                  <label class="col-xs-4 control-label">Bank Tujuan *</label>
                  <div class="col-xs-6">
                      <select name="bank_tujuan_bayar" id="bank_tujuan_bayar" class="form-control selectpicker" title="-- Pilih bank --">
                        <option value="BCA">BCA</option>
                        <option value="BRI">BRI</option>
                      </select>
                  </div>
              </div>-->
              <div class="form-group">
                  <label class="col-xs-4 control-label">Keterangan</label>
                  <div class="col-xs-6">
                      <textarea class="form-control" name="keterangan" id="keterangan" placeholder="Keterangan" maxlength="255"></textarea>
                  </div>
              </div>
            
            </div>
            <!-- /.modal-body -->
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
              <button type="submit" class="btn btn-primary" id="btnSubmit">Save</button>
            </div>
            <!-- /.modal-footer -->
            
            </form>
              
          </div>
          <!-- /.modal-content -->
        </div>
        <!-- /.modal-dialog -->
      </div>
      <!-- /.modal -->
      
      <input type="hidden" id="default_date" value="{{ $default_date }}">
      <input type="hidden" id="id" value="">
      
@endsection

@section('other-js')
    <script src="{{ asset('vendor/formvalidation/formValidation.min.js') }}"></script>
    <script src="{{ asset('vendor/formvalidation/framework/bootstrap.min.js') }}"></script>
    <script src="{{ asset('bower_components/autoNumeric/autoNumeric.js') }}"></script>
    <!-- bootstrap select -->
    <script src="{{ asset('bower_components/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('bower_components/sweetalert/dist/sweetalert.min.js') }}"></script>
    <script src="{{ asset('js/pembayaran_angkutan.js') }}"></script>
      
    <!-- page script -->
    <script type="text/javascript">
    $(document).ready(function() {
        searchbox.init();
        datatables.init();
        detail.init();
        validationDetail.init();
    });
    </script>
@endsection
