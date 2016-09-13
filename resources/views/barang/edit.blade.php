@extends('layouts.backend')

@section('other-css')
    <link rel="stylesheet" href="{{ asset('vendor/formvalidation/formValidation.css') }}">
@endsection

@section('content')
    
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Barang
        <small>Edit</small>
      </h1>
        
      <ol class="breadcrumb">
        <li><a href="{{ url('/') }}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="{{ url('/barang') }}"> Barang</a></li>
        <li class="active">Edit</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">

      <!-- Your Page Content Here -->
      
      <div class="row">
        
        <div class="col-xs-12">
          
          <div class="box box-primary">
            
            <div class="box-header with-border">
              <h3 class="box-title">Data Barang</h3>
            </div>
            <!-- /.box-header -->
            
            <!-- form start -->
            <form class="form-horizontal" id="frmData" method="post" action="{{ url('/barang') }}/{{ $barang->id }}" autocomplete="off">
              
              {{ csrf_field() }}
              {{ method_field('PUT') }}
              
              <div class="box-body">
                
                <div class="form-group">
                  <label for="nama" class="col-sm-2 control-label">Nama *</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" name="nama" id="nama" value="{{ old('nama') !== null ? old('nama') : $barang->nama }}" placeholder="Nama">
                  </div>
                </div>
                <div class="form-group">
                  <label for="jenis" class="col-sm-2 control-label">Jenis </label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" name="jenis" id="jenis" placeholder="Jenis" value="{{ old('jenis') !== null ? old('jenis') : $barang->jenis }}">
                  </div>
                </div>
                <div class="form-group">
                  <label for="pcs" class="col-sm-2 control-label">Pcs *</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" name="pcs" id="pcs" placeholder="Pcs" value="{{ old('pcs') !== null ? str_replace(',', '', old('pcs')) : $barang->pcs }}">
                  </div>
                </div>
                <div class="form-group">
                  <label for="berat" class="col-sm-2 control-label">Berat (Kg) *</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" name="berat" id="berat" placeholder="Berat" value="{{ old('berat') !== null ? str_replace(',', '', old('berat')) : $barang->berat }}">
                  </div>
                </div>
                
              </div>
              <!-- /.box-body -->
              
              <div class="box-footer">
                <div class="pull-right">
                  <a href="{{ url('/barang') }}" class="btn btn-warning"><i class="fa fa-chevron-left"></i> Back</a>
                  <button type="submit" class="btn btn-primary" id="btnSubmit"><i class="fa fa-refresh"></i> Update</button>
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
