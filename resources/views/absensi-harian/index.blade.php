@extends('layouts.backend')
@section('title', 'Master Karyawan Tetap')
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
  Absensi Harian
  
  </h1>
  <ol class="breadcrumb">
    <li><a href="{{ url('/') }}"><i class="fa fa-tachometer"></i> Dashboard</a></li>
    <li><a href="#">Absensi</a></li>
    <li><a href="{{ url('/absensi-harian') }}">Absensi Harian</a></li>
    
  </ol>
</section>
<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-xs-12">
      <div class="box">
        <!-- /.box-header -->
        <div class="box-body">
          <div class="table-responsive">
          <form class="form-horizontal" id="frmData" method="post" action="{{ url('absensi-harian/set-date') }}" autocomplete="off">
          {{ csrf_field() }}
            <div class="form-group">
                  <label for="tgl_masuk" class="col-sm-2 control-label">Tanggal Absensi</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control pull-right datepicker" name="tgl_absensi" placeholder="Tanggal Absensi" value="{{ old('tgl_absensi') }}">
                  </div>
            </div>
            <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">OK</button>
          </form>
          </div>
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
@include('karyawan.partials.add_modal')
<!-- page script -->
<script type="text/javascript">
$(document).ready(function(){
absensiHarianModule.init();
});
</script>
@endsection