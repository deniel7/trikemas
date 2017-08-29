@extends('layouts.backend')
@section('title', 'Tambah Absensi Lembur')
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
  Absensi Harian
  <small>Tambah Absensi Lembur</small>
  </h1>
  <ol class="breadcrumb">
    <li><a href="{{ url('/') }}"><i class="fa fa-tachometer"></i> Dashboard</a></li>
    <li><a href="#">Absensi</a></li>
    <li><a href="{{ url('/absensi-harian/tambah-absensi') }}">Absensi Lembur</a></li>
    <li class="active">Tambah</li>
  </ol>
</section>
<!-- Main content -->
<section class="content">

   <!-- form start -->
            <form class="form-horizontal" id="frmData" method="post" action="{{ url('/absensi-harian/lembur') }}" autocomplete="off">
              
              {{ csrf_field() }}
              
              <div class="box-body">
                <div class="form-group">
                  <label for="tgl_masuk" class="col-sm-2 control-label">Tanggal *</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control pull-right datepicker" name="tgl_lembur" id="tgl_lembur" placeholder="Tanggal Lembur" value="{{ old('tgl_lembur') }}">
                  </div>
                </div>
                
                <div class="form-group">
                  <label for="nama" class="col-sm-2 control-label">Nama *</label>
                  <div class="col-sm-10">
                    <select name="karyawan" class="form-control select2">
                      <option value="ANY">-- ketik nama disini --</option>
                      @foreach($karyawans as $karyawan)
                      <option value="{{ $karyawan->nik }}">{{ $karyawan->nama }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
                <div class="form-group">
                  <label for="jenis_lembur" class="col-sm-2 control-label">Jenis Lembur </label>
                  <div class="col-sm-10">
                    <select name='jenis_lembur' class='form-control selectpicker' title='-- Pilih Jenis Lembur --'><option value='1'>Rutin</option><option value='2'>Biasa</option><option value='3'>Off</option></select>
                  </div>
                </div>
                <div class="form-group">
                  <label for="phone" class="col-sm-2 control-label">Jam Lembur *</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" name="jam" id="jam" placeholder="Jam" value="{{ old('jam') }}">
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

</section>
<!-- /.content -->
<!-- page script -->
<script type="text/javascript">
$(document).ready(function(){
absensiHarianModule.init();
});
</script>
@endsection