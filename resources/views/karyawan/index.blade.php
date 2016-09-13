@extends('layouts.backend')
@section('title', 'Master Karyawan')
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
  Item
  <small>List</small>
  </h1>
  <ol class="breadcrumb">
    <li><a href="{{ url('/') }}"><i class="fa fa-tachometer"></i> Dashboard</a></li>
    <li><a href="#">Master</a></li>
    <li><a href="{{ url('/item') }}">Item</a></li>
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
          
          <br><br>
          <div class="table-responsive">
          <!-- 
            Tambahkan style : table-layout fixed untuk bisa atur width column
             -->
            <table id="datatable" style="table-layout: fixed;" width="150%" class="table table-bordered table-striped table-condensed">
              <thead>
                <tr>
                  <th width="40%">Status Karyawan</th>
                  <th width="10%">NIK</th>
                  <th width="10%">Nama</th>
                  <th width="10%">Alamat</th>
                  <th width="10%">Phone</th>
                  <th width="10%">Lulusan</th>
                  <th width="10%">Tanggal Masuk</th>
                  <th width="10%">Nilai Upah</th>
                  <th width="10%">Uang Makan</th>
                  <th width="10%">Uang Lembur</th>
                  <th width="10%">Norek</th>
            
                  <th width="10%">Actions</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
              <tfoot>
              <tr>
                <th></th>
                <th>Status Karyawan</th>
                <th>NIK</th>
                <th>Nama</th>
                <th>Alamat</th>
                <th>Phone</th>
                <th>Lulusan</th>
                <th>Tanggal Masuk</th>
                <th>Nilai Upah</th>
                <th>Uang Makan</th>
                <th>Uang Lembur</th>
                <th>Norek</th>
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
  <div class="btn-group">
            <a href="{{ url('item/create') }}" class="btn btn-primary">
            <i class="fa fa-plus fa-fw"></i> Add
            </a>
          </div>
</section>
<!-- /.content -->
@include('karyawan.partials.add_modal')
<!-- page script -->
<script type="text/javascript">
$(document).ready(function(){
karyawanModule.init();
});
</script>
@endsection