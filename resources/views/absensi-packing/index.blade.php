@extends('layouts.backend')
@section('title', 'Master Absensi Packing')
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
  Absensi Packing
  <small>List</small>
  </h1>
  <ol class="breadcrumb">
    <li><a href="{{ url('/') }}"><i class="fa fa-tachometer"></i> Dashboard</a></li>
    <li><a href="#">Master</a></li>
    <li><a href="{{ url('/karyawan-tetap') }}">Absensi Packing</a></li>
    <li class="active">List</li>
  </ol>
</section>
<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-xs-12">
      <div class="box">
        <!-- /.box-header -->
        <div class="box-body">
          <div class="btn-group">
                <a href="{{ url('/absensi-packing/create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> Add</a>
              </div>
              <br><br>
          
          <div class="table-responsive">
          <!-- 
            Tambahkan style : table-layout fixed untuk bisa atur width column
             -->
            <table id="datatable" style="" width="100%" class="table table-bordered table-striped table-condensed">
              <thead>
                <tr>
                  <th>Tanggal</th>
                  <th>Bagian</th>
                  <th>Jenis</th>
                  <th>Jumlah</th>
                  <th width="10%">Actions</th>                 
                </tr>
              </thead>
              <tbody>
              </tbody>
              <tfoot>
              <tr>
                <th>Tanggal</th>
                <th>Bagian</th>
                <th>Jenis</th>
                <th>Jumlah</th>
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
<!-- /.content -->

<!-- page script -->
<style>
.datepicker{z-index:1151 !important;}
</style>
<script type="text/javascript">
$(document).ready(function(){
absensiPackingModule.init();
});
</script>
@endsection