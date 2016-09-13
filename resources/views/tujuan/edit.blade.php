@extends('layouts.backend')

@section('other-css')
    <link rel="stylesheet" href="{{ asset('vendor/formvalidation/formValidation.css') }}">
@endsection

@section('content')
    
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Tujuan Pengiriman
        <small>Edit</small>
      </h1>
        
      <ol class="breadcrumb">
        <li><a href="{{ url('/') }}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="{{ url('/tujuan') }}"> Tujuan Pengiriman</a></li>
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
              <h3 class="box-title">Data Tujuan Pengiriman</h3>
            </div>
            <!-- /.box-header -->
            
            <!-- form start -->
            <form class="form-horizontal" id="frmData" method="post" action="{{ url('/tujuan') }}/{{ $tujuan->id }}" autocomplete="off">
              
              {{ csrf_field() }}
              {{ method_field('PUT') }}
              
              <div class="box-body">
                
                <div class="form-group">
                  <label for="kota" class="col-sm-2 control-label">Kota *</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" name="kota" id="kota" value="{{ old('kota') !== null ? old('kota') : $tujuan->kota }}" placeholder="Kota">
                  </div>
                </div>
                
              </div>
              <!-- /.box-body -->
              
              <div class="box-footer">
                <div class="pull-right">
                  <a href="{{ url('/tujuan') }}" class="btn btn-warning"><i class="fa fa-chevron-left"></i> Back</a>
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
    <script src="{{ asset('js/tujuan.js') }}"></script>
      
    <!-- page script -->
    <script type="text/javascript">
    $(document).ready(function() {
        validation.init();
    });
    </script>
@endsection
