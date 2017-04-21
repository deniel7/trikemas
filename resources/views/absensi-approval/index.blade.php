@extends('layouts.backend')
@section('title', 'Absensi Approval')
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
  Absensi
  <small>List</small>
  </h1>
  <ol class="breadcrumb">
    <li><a href="{{ url('/') }}"><i class="fa fa-tachometer"></i> Dashboard</a></li>
    <li><a href="#">Master</a></li>
    <li><a href="{{ url('/karyawan-tetap') }}">Absensi</a></li>
    <li class="active">List</li>
  </ol>
</section>

<form action="{{ url('absensi-approval') }}" method="post" id="approve">
<input type="hidden" name="_token" value="{{ csrf_token() }}">
<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-xs-12">
      <div class="box">
        <!-- /.box-header -->
        <div class="box-body">
          <button type="button" onclick="absensiApprovalModule.confirmLembur();" class="btn btn-warning" name="confirm" value="Confirm"><i class="fa fa-check fa-fw"></i> Confirm</button>


          <div class="table-responsive">
          <!-- 
            Tambahkan style : table-layout fixed untuk bisa atur width column
             -->
            <table id="datatable" style="" width="100%" class="table table-bordered table-striped table-condensed">
              <thead>
                <tr>
                  <th><input name="select_all" value="1" id="example-select-all" type="checkbox" /></th>
                  <th>Tanggal</th>
                  <th>Nama</th>
                  <th>Shift</th>
                  <th>Masuk</th>
                  <th>Pulang</th>
                  <th>Scan Masuk</th>
                  <th>Scan Pulang</th>
                  <th>Terlambat</th>
                  <th>Pulang Cepat</th>
                  <th>Jenis Lembur</th>
                  <th>Lembur</th>
                  <th>Konfirmasi Lembur</th>
                  <th>Jumlah Kehadiran</th>
                  <th>Pot. Absensi</th>
                  <th>Status</th>
                  <th width="10%">Actions</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
              <tfoot>
              <tr>
                  <th></th>
                  <th>Tanggal</th>
                  <th>Nama</th>
                  <th>Shift</th>
                  <th>Masuk</th>
                  <th>Pulang</th>
                  <th>Scan Masuk</th>
                  <th>Scan Pulang</th>
                  <th>Terlambat</th>
                  <th>Pulang Cepat</th>
                  <th>Jenis Lembur</th>
                  <th>Lembur</th>
                  <th>Konfirmasi Lembur</th>
                  <th>Jumlah Kehadiran</th>
                  <th>Pot. Absensi</th>
                  <th>Status</th>
                <th></th>
              </tr>
              </tfoot>
            </table>
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
</form>
<!-- /.content -->
@include('absensi-approval.partials.show_detail_modal')
<!-- page script -->
<style>
.datepicker{z-index:1151 !important;}
</style>
<script type="text/javascript">
$(document).ready(function(){
absensiApprovalModule.init();
});
</script>
@endsection