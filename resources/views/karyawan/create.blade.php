@extends('layouts.backend')
@section('title', 'Tambah Karyawan')
@section('other-css')
    <link rel="stylesheet" href="{{ asset('vendor/formvalidation/formValidation.css') }}">
<!-- bootstrap select -->
    <link rel="stylesheet" href="{{ asset('bower_components/bootstrap-select/dist/css/bootstrap-select.min.css') }}">
@endsection

@section('content')
    
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Karyawan
        <small>Add</small>
      </h1>
        
      <ol class="breadcrumb">
        <li><a href="{{ url('/') }}"><i class="fa fa-dashboard"></i> Home</a></li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">

      <!-- Your Page Content Here -->
      
      <div class="row">
        
        <div class="col-xs-12">
          
          <div class="box box-primary">
            
            <div class="box-header with-border">
              <h3 class="box-title">Data Karyawan</h3>
            </div>
            <!-- /.box-header -->
            
            <!-- form start -->
            <form class="form-horizontal" id="frmData" method="post" action="{{ url('/karyawan-tetap') }}" autocomplete="off">
              
              {{ csrf_field() }}
              
              <div class="box-body">
                <div class="form-group">
                  <label for="nama" class="col-sm-2 control-label">Status Karyawan *</label>
                  <div class="col-sm-10">
                    <select name="status_karyawan_id" id="status_karyawan_id" class="form-control selectpicker" title="-- Pilih Status Karyawan --">
                      @foreach($status_karyawans as $item)
                        <option value="{{ $item->id }}" {{ $item->id == old('status_karyawan_id') ? 'selected' : '' }} >{{ $item->keterangan }}</option>
                      @endforeach
                    </select>
                  </div>
                    
                  </div>
                </div>
                <div class="form-group">
                  <label for="nik" class="col-sm-2 control-label">NIK *</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" name="nik" id="nik" placeholder="NIK" value="{{ old('nik') }}">
                  </div>
                </div>
                <div class="form-group">
                  <label for="nama" class="col-sm-2 control-label">Nama *</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" name="nama" id="nama" placeholder="Nama" value="{{ old('nama') }}">
                  </div>
                </div>
                <div class="form-group">
                  <label for="alamat" class="col-sm-2 control-label">Alamat </label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" name="alamat" id="alamat" placeholder="alamat" value="{{ old('alamat') }}">
                  </div>
                </div>
                <div class="form-group">
                  <label for="phone" class="col-sm-2 control-label">Phone *</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" name="phone" id="phone" placeholder="Phone" value="{{ old('phone') }}">
                  </div>
                </div>
                <div class="form-group">
                  <label for="lulusan" class="col-sm-2 control-label">Lulusan *</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" name="lulusan" id="lulusan" placeholder="Lulusan" value="{{ old('lulusan') }}">
                  </div>
                </div>
                <div class="form-group">
                  <label for="tgl_masuk" class="col-sm-2 control-label">Tanggal Masuk *</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control pull-right datepicker" name="tgl_masuk" id="tgl_masuk" placeholder="Tanggal Masuk" value="{{ old('tgl_masuk') }}">
                    
                  </div>
                </div>
                <div class="form-group">
                  <label for="nilai_upah" class="col-sm-2 control-label">Nilai Upah *</label>
                  <div class="col-sm-10">
                    <div class="input-group">
                      <span class="input-group-addon">Rp</span>
                      <input type="text" class="form-control number" id="nilai_upah" name="nilai_upah" placeholder="Nilai Upah" data-input="nilai_upah" value="{{ str_replace(',', '', old('nilai_upah')) }}">
                      <!--<input type="hidden" name="nilai_upah">-->
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <label for="uang_makan" class="col-sm-2 control-label">Uang Makan *</label>
                  <div class="col-sm-10">
                    <div class="input-group">
                      <span class="input-group-addon">Rp</span>
                      <input type="text" id="uang_makan" name="uang_makan" class="form-control number" placeholder="Uang Makan" data-input="uang_makan" value="{{ str_replace(',', '', old('uang_makan')) }}">
                      <!--<input type="hidden" name="uang_makan">-->
                    </div>
                  </div>
                </div>

                <div class="form-group">
                  <label for="uang_lembur" class="col-sm-2 control-label">Uang Lembur *</label>
                  <div class="col-sm-10">
                    <div class="input-group">
                      <span class="input-group-addon">Rp</span>
                      <input type="text" class="form-control number" name="uang_lembur"  id="uang_lembur" placeholder="Uang Lembur" data-input="uang_lembur" value="{{ str_replace(',', '', old('uang_lembur')) }}">
                      <!--<input type="hidden" name="uang_lembur">-->
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <label for="norek" class="col-sm-2 control-label">Nomor Rekening *</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" name="norek" id="norek" placeholder="Nomor Rekening" value="{{ old('norek') }}">
                  </div>
                </div>
                
              </div>
              <!-- /.box-body -->
              
              <div class="box-footer">
                <div class="btn-group pull-right">
                  <a href="{{ url('/karyawan-tetap') }}" class="btn btn-warning"><i class="fa fa-chevron-left"></i> Back</a>
                  <button type="submit" class="btn btn-primary" id="btnSubmit" style="margin-left: 5px;"><i class="fa fa-check"></i> Save</button>
                </div>
              </div>
              <!-- /.box-footer -->
            
            </form>
          
          </div>
          <!-- /.box -->
        </div>
        </div>
        <!-- /.col -->
      
      </div>
      <!-- /.row -->

    </section>
    <!-- /.content -->
<!-- page script -->
    <script type="text/javascript">
    $(document).ready(function(){
    karyawanValidation.init();
    });
    </script>
@endsection

@section('other-js')
    <script src="{{ asset('vendor/formvalidation/formValidation.min.js') }}"></script>
    <script src="{{ asset('vendor/formvalidation/framework/bootstrap.min.js') }}"></script>
    
    <script src="{{ asset('bower_components/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>


@endsection
