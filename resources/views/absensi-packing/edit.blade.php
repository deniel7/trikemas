@extends('layouts.backend')
@section('title', 'Tambah Absensi Packing')
@section('other-css')
    <link rel="stylesheet" href="{{ asset('vendor/formvalidation/formValidation.css') }}">
<!-- bootstrap select -->
    <link rel="stylesheet" href="{{ asset('bower_components/bootstrap-select/dist/css/bootstrap-select.min.css') }}">
@endsection

@section('content')
    
<section class="content">
    <form id="main_form">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    <input type="hidden" name="packing_id" value="{{ $absensi_packing->id }}">
    
    <div class="row">
      <div class="col-xs-12">

        <div class="box">
          <!-- /.box-header -->
          <div class="box-header">
            <h3 class="box-title">Absensi Packing</h3>
          </div>
          <div class="box-body">
            
            <div class="row">
              <div class="form-group required col-md-4">
                <label class="control-label">Tanggal</label>
                <input type="text" class="form-control pull-right datepicker" name="tgl" placeholder="Tanggal" value="{{ date('Y-m-d') }}">
                    
              </div>
            </div>
            <div class="row">
              <div class="form-group required col-md-4">
                <label class="control-label">Bagian</label>
                <br>
                <select name="bagian" class="form-control selectpicker" title="-- Pilih Bagian --">
                      @foreach($bagians as $bagian)
                        <option value="{{ $bagian->bagian }}" {{ $bagian->bagian == (old('bagian') !== null ? old('bagian') : $absensi_packing->bagian) ? 'selected' : '' }} >{{ $bagian->bagian }}</option>
                      @endforeach
                    </select>
              </div>
            </div>
            <div class="row">
              <div class="form-group required col-md-4">
                <label class="control-label">Jenis</label>
                <br>
                <select name="jenis" id="jenis" class="form-control selectpicker" title="-- Pilih Jenis --">
                      @foreach($upah_jenises as $upah_jenis)
                        <option value="{{ $upah_jenis->id }}" {{ $upah_jenis->id == (old('jenis') !== null ? old('jenis') : $absensi_packing->jenis) ? 'selected' : '' }} >{{ $upah_jenis->nama }}</option>
                      @endforeach
                    </select>

              </div>
            </div>
            <div class="row">
              <div class="form-group required col-md-4">
                <label class="control-label">Jumlah</label>

                <input type="text" class="form-control" name="quantity" id="quantity" value="{{ old('quantity') !== null ? old('quantity') : $absensi_packing->jumlah }}" placeholder="Jumlah">

                
              </div>
            </div>
            
            <div class="row">
              <div class="col-md-6">
                <a role="button" href="{{ URL::previous() }}" class="btn btn-default"><i class="fa fa-chevron-left fa-fw"></i> Back</a>
              </div>
              <div class="col-md-6 text-right">
                <div class="btn-group">
                  <button type="button" class="btn btn-success" onclick="absensiPackingModule.updateAbsensiPacking();"><i class="fa fa-send fa-fw"></i> Update</button>
                </div>
              </div>
            </div>
          </div>
          <div class="overlay" id="overlayForm">
            <i class="fa fa-refresh fa-spin"></i>
          </div>
          <!-- /.box-body -->
        </div>
        <!-- /.box -->
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
  </form>
</section>
    <!-- /.content -->
<!-- page script -->
    <script type="text/javascript">
    $(document).ready(function(){
    absensiPackingModule.init();
    });
    </script>


@section('other-js')
    <script src="{{ asset('vendor/formvalidation/formValidation.min.js') }}"></script>
    <script src="{{ asset('vendor/formvalidation/framework/bootstrap.min.js') }}"></script>
    <script src="{{ asset('bower_components/autoNumeric/autoNumeric.js') }}"></script>
    <script src="{{ asset('bower_components/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('js/fv-karyawan.js') }}"></script>
@endsection
@endsection