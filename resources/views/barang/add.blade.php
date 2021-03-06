@extends('layouts.backend')

@section('other-css')
    <link rel="stylesheet" href="{{ asset('vendor/formvalidation/formValidation.css') }}">
@endsection

@section('content')
    
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Jenis Barang
        <small>Add</small>
      </h1>
        
      <ol class="breadcrumb">
        <li><a href="{{ url('/') }}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="{{ url('/barang') }}"> Jenis Barang</a></li>
        <li class="active">Add</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">

      <!-- Your Page Content Here -->
      
      <div class="row">
        
        <div class="col-xs-12">
          
          <div class="box box-primary">
            
            <div class="box-header with-border">
              <h3 class="box-title">Data Jenis Barang</h3>
            </div>
            <!-- /.box-header -->
            
            <!-- form start -->
            <form class="form-horizontal" id="frmData" method="post" action="{{ url('/barang') }}" autocomplete="off">
              
              {{ csrf_field() }}
              
              <div class="box-body">
                
                <div class="form-group">
                  <label for="nama" class="col-sm-3 control-label">Nama *</label>
                  <div class="col-sm-9">
                    <input type="text" class="form-control" name="nama" id="nama" placeholder="Nama" value="{{ old('nama') }}">
                  </div>
                </div>
                <div class="form-group">
                  <label for="jenis" class="col-sm-3 control-label">Jenis </label>
                  <div class="col-sm-9">
                    <input type="text" class="form-control" name="jenis" id="jenis" placeholder="Jenis" value="{{ old('jenis') }}">
                  </div>
                </div>
                <div class="form-group">
                  <label for="pcs" class="col-sm-3 control-label">Jumlah Pcs dalam Ball *</label>
                  <div class="col-sm-9">
                    <input type="text" class="form-control" name="pcs" id="pcs" placeholder="Pcs" value="{{ str_replace(',', '', old('pcs')) }}">
                  </div>
                </div>
                <div class="form-group">
                  <label for="berat" class="col-sm-3 control-label">Berat (Kg) *</label>
                  <div class="col-sm-9">
                    <input type="text" class="form-control" name="berat" id="berat" placeholder="Berat" value="{{ str_replace(',', '', old('berat')) }}">
                  </div>
                </div>
                
              </div>
              <!-- /.box-body -->
              
              <div class="box-footer">
                <div class="btn-group pull-right">
                  <a href="{{ url('/barang') }}" class="btn btn-warning"><i class="fa fa-chevron-left"></i> Back</a>
                  <button type="submit" class="btn btn-primary" id="btnSubmit" style="margin-left: 5px;"><i class="fa fa-check"></i> Save</button>
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
    <script src="{{ asset('js/barang.js') }}"></script>
      
    <!-- page script -->
    <script type="text/javascript">
    $(document).ready(function() {
        validation.init();
    });
    </script>
@endsection