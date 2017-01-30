@extends('layouts.backend')
@section('title', 'Master Karyawan Kontrak')
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
  Karyawan Kontrak
  <small>List</small>
  </h1>
  <ol class="breadcrumb">
    <li><a href="{{ url('/') }}"><i class="fa fa-tachometer"></i> Dashboard</a></li>
    <li><a href="#">Master</a></li>
    <li><a href="{{ url('/karyawan-tetap') }}">Karyawan Kontrak</a></li>
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
                @if (in_array(111, session()->get('allowed_menus')))
                <a href="{{ url('/karyawan-tetap/create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> Add</a>
                endif;
              </div>
              <br><br>
          <div class="table-responsive">
          <!-- 
            Tambahkan style : table-layout fixed untuk bisa atur width column
             -->
            <table id="datatable" style="table-layout: fixed;" width="150%" class="table table-bordered table-striped table-condensed">
              <thead>
                <tr>
                  <th>Status</th>
                  <th>NIK</th>
                  <th>Nama</th>
                  <th>Alamat</th>
                  <th>Phone</th>
                  <th>Lulusan</th>
                  <th>Tgl Masuk</th>
                  <th>Nilai Upah</th>
                  <th>Uang Makan</th>
                  <th>Tunjangan</th>
                  <th>Pot. Koperasi</th>
                  <th>Pot. BPJS</th>
                  <th width="10%">Norek</th>
            
                  <th width="10%">Actions</th>
                </tr>
              </thead>
              <tbody>
              </tbody>
              <tfoot>
              <tr>
                <th>Status</th>
                <th>NIK</th>
                <th>Nama</th>
                <th>Alamat</th>
                <th>Phone</th>
                <th>Lulusan</th>
                <th>Tgl Masuk</th>
                <th>Nilai Upah</th>
                <th>Uang Makan</th>
                <th>Tunjangan</th>
                <th>Pot. Koperasi</th>
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
  
</section>
<!-- /.content -->
@include('karyawan.partials.add_modal')
@include('karyawan.partials.print_modal')
<!-- page script -->
<script type="text/javascript">
$(document).ready(function(){
karyawanModule.init();
});
</script>
@endsection


@section('other-js')
    <script src="{{ asset('vendor/formvalidation/formValidation.min.js') }}"></script>
    <script src="{{ asset('vendor/formvalidation/framework/bootstrap.min.js') }}"></script>
    
    <script src="{{ asset('bower_components/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>


@endsection