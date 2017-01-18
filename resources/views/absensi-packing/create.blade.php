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
                <br><select class="form-control" name="bagian">
                   
                      <option value="A">A</option>
                      <option value="B">B</option>
                      <option value="C">C</option>
                    
                </select>
              </div>
            </div>
            <div class="row">
              <div class="form-group required col-md-4">
                <label class="control-label">Jenis</label>
                <br><select class="form-control" name="jenis">
                   
                      <option value="01">01</option>
                      <option value="03p">03p</option>
                      <option value="03s">03s</option>
                      <option value="HB">HB</option>
                      <option value="M2">M2</option>
                      <option value="P6">P6</option>
                      <option value="P7.5">P7.5</option>
                      <option value="P9">P9</option>
                      <option value="MTG">MTG</option>
                      <option value="TR1">TR1</option>
                      <option value="TR2">TR2</option>
                      <option value="TR6">TR6</option>
                      <option value="TR7">TR7</option>
                      <option value="TR11">TR11</option>
                </select>
              </div>
            </div>
            <div class="row">
              <div class="form-group required col-md-4">
                <label class="control-label">Jumlah</label>
                <input type="text" required name="quantity" class="form-control" placeholder="" value="{{ old('quantity') }}">
              </div>
            </div>
            
            <div class="row">
              <div class="col-md-6">
                <a role="button" href="{{ URL::previous() }}" class="btn btn-default"><i class="fa fa-chevron-left fa-fw"></i> Back</a>
              </div>
              <div class="col-md-6 text-right">
                <div class="btn-group">
                  <button type="button" class="btn btn-success" onclick="absensiPackingModule.createAbsensi();"><i class="fa fa-send fa-fw"></i> Save</button>
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