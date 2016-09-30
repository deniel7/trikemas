@extends('layouts.backend')
@section('title', 'Edit Karyawan')
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
        <small>Edit</small>
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
              <form class="form-horizontal" id="frmData" method="post" action="{{ url('karyawan-tetap').'/'.$karyawan->id }}" autocomplete="off">
              <input name="_method" type="hidden" value="put">
              <input type="hidden" name="_token" value="{{ csrf_token() }}">
              
              <div class="box-body">
                <div class="form-group">
                  <label for="nama" class="col-sm-2 control-label">Status Karyawan *</label>
                  <div class="col-sm-10">
                  <select name="status_karyawan_id" id="status_karyawan_id" class="form-control selectpicker" title="-- Pilih angkutan --">
                      @foreach($status_karyawans as $item)
                        <option value="{{ $item->id }}" {{ $item->id == (old('status_karyawan_id') !== null ? old('status_karyawan_id') : $karyawan->status_karyawan_id) ? 'selected' : '' }} >{{ $item->keterangan }}</option>
                      @endforeach
                    </select>

                  </div>
                </div>
                <div class="form-group">
                  <label for="nama" class="col-sm-2 control-label">NIK *</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" name="nik" id="nik" value="{{ $karyawan->nik }}">
                  </div>
                </div>
                <div class="form-group">
                  <label for="nama" class="col-sm-2 control-label">Nama *</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" name="nama" id="nama" placeholder="Nama" value="{{ old('nama') !==null ? : $karyawan->nama  }}">
                  </div>
                </div>
                <div class="form-group">
                  <label for="jenis" class="col-sm-2 control-label">Alamat </label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" name="alamat" id="alamat" placeholder="alamat" value="{{ old('alamat') !==null ? : $karyawan->alamat  }}">
                  </div>
                </div>
                <div class="form-group">
                  <label for="pcs" class="col-sm-2 control-label">Phone *</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" name="phone" id="phone" placeholder="Phone" value="{{ old('phone') !==null ? : $karyawan->phone  }}">
                  </div>
                </div>
                <div class="form-group">
                  <label for="berat" class="col-sm-2 control-label">Lulusan *</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" name="lulusan" id="lulusan" placeholder="Lulusan" value="{{ old('lulusan') !==null ? : $karyawan->lulusan  }}">
                  </div>
                </div>
                <div class="form-group">
                  <label for="berat" class="col-sm-2 control-label">Tanggal Masuk *</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" name="tgl_masuk" id="tgl_masuk" placeholder="Tanggal Masuk" value="{{ old('tgl_masuk') !==null ? : $karyawan->tgl_masuk  }}">
                  </div>
                </div>
                <div class="form-group">
                  <label for="berat" class="col-sm-2 control-label">Nilai Upah *</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" name="nilai_upah" id="nilai_upah" placeholder="Nilai Upah" value="{{ old('nilai_upah') !==null ? : $karyawan->nilai_upah  }}">
                  </div>
                </div>
                <div class="form-group">
                  <label for="berat" class="col-sm-2 control-label">Uang Makan *</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" name="uang_makan" id="uang_makan" placeholder="Uang Makan" value="{{ old('uang_makan') !==null ? : $karyawan->uang_makan  }}">
                  </div>
                </div>

                <div class="form-group">
                  <label for="berat" class="col-sm-2 control-label">Uang Lembur *</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" name="uang_lembur" id="uang_lembur" placeholder="Uang Lembur" value="{{ old('uang_lembur') !==null ? : $karyawan->uang_lembur  }}">
                  </div>
                </div>
                <div class="form-group">
                  <label for="berat" class="col-sm-2 control-label">Nomor Rekening *</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" name="norek" id="norek" placeholder="Nomor Rekening" value="{{ old('norek') !==null ? : $karyawan->norek  }}">
                  </div>
                </div>
                
              </div>
              <!-- /.box-body -->
              
              <div class="box-footer">
                <div class="btn-group pull-right">
                  <a href="{{ url('/karyawan-tetap') }}" class="btn btn-warning"><i class="fa fa-chevron-left"></i> Back</a>
                  <button type="submit" class="btn btn-primary" id="btnSubmit" style="margin-left: 5px;"><i class="fa fa-check"></i> Update</button>
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
    
@endsection

@section('other-js')
    <script src="{{ asset('vendor/formvalidation/formValidation.min.js') }}"></script>
    <script src="{{ asset('vendor/formvalidation/framework/bootstrap.min.js') }}"></script>
    <script src="{{ asset('bower_components/autoNumeric/autoNumeric.js') }}"></script>
        <script src="{{ asset('bower_components/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
      
    <!-- page script -->
    <script type="text/javascript">
    $(document).ready(function() {
        validation.init();
    });
    </script>
@endsection
