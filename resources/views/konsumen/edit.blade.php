@extends('layouts.backend')

@section('other-css')
    <link rel="stylesheet" href="{{ asset('vendor/formvalidation/formValidation.css') }}">
@endsection

@section('content')
    
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Konsumen
        <small>Edit</small>
      </h1>
        
      <ol class="breadcrumb">
        <li><a href="{{ url('/') }}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="{{ url('/konsumen') }}"> Konsumen</a></li>
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
              <h3 class="box-title">Data Konsumen</h3>
            </div>
            <!-- /.box-header -->
            
            <!-- form start -->
            <form class="form-horizontal" id="frmData" method="post" action="{{ url('/konsumen') }}/{{ $konsumen->id }}" autocomplete="off">
              
              {{ csrf_field() }}
              {{ method_field('PUT') }}
              
              <div class="box-body">
                
                <div class="form-group">
                  <label for="nama" class="col-sm-2 control-label">Nama *</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" name="nama" id="nama" value="{{ old('nama') !== null ? old('nama') : $konsumen->nama }}" placeholder="Nama">
                  </div>
                </div>
                <div class="form-group">
                  <label for="alamat" class="col-sm-2 control-label">Alamat </label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" name="alamat" id="alamat" placeholder="Alamat" value="{{ old('alamat') !== null ? old('alamat') : $konsumen->alamat }}">
                  </div>
                </div>
                <div class="form-group">
                  <label for="hp" class="col-sm-2 control-label">No. HP *</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" name="hp" id="hp" placeholder="No. HP" value="{{ old('hp') !== null ? old('hp') : $konsumen->hp }}">
                  </div>
                </div>
                
              </div>
              <!-- /.box-body -->
              
              <div class="box-footer">
                <div class="pull-right">
                  <a href="{{ url('/konsumen') }}" class="btn btn-warning"><i class="fa fa-chevron-left"></i> Back</a>
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
    <script src="{{ asset('js/konsumen.js') }}"></script>
      
    <!-- page script -->
    <script type="text/javascript">
    $(document).ready(function() {
        validation.init();
    });
    </script>
@endsection
