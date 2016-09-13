@extends('layouts.backend')

@section('other-css')
    <link rel="stylesheet" href="{{ asset('vendor/formvalidation/formValidation.css') }}">
    <!-- bootstrap select -->
    <link rel="stylesheet" href="{{ asset('bower_components/bootstrap-select/dist/css/bootstrap-select.min.css') }}">
@endsection

@section('content')
    
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Biaya Angkutan
        <small>Edit</small>
      </h1>
        
      <ol class="breadcrumb">
        <li><a href="{{ url('/') }}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="{{ url('/angkutan-tujuan') }}"> Biaya Angkutan</a></li>
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
              <h3 class="box-title">Data Biaya Angkutan</h3>
            </div>
            <!-- /.box-header -->
            
            <!-- form start -->
            <form class="form-horizontal" id="frmData" method="post" action="{{ url('/angkutan-tujuan') }}/{{ $angkutan_tujuan->id }}" autocomplete="off">
              
              {{ csrf_field() }}
              {{ method_field('PUT') }}
              
              <div class="box-body">
                
                <div class="form-group">
                  <label for="angkutan_id" class="col-sm-2 control-label">Angkutan *</label>
                  <div class="col-sm-10">
                    <select name="angkutan_id" id="angkutan_id" class="form-control selectpicker" title="-- Pilih angkutan --">
                      @foreach($angkutan as $item)
                        <option value="{{ $item->id }}" {{ $item->id == (old('angkutan_id') !== null ? old('angkutan_id') : $angkutan_tujuan->angkutan_id) ? 'selected' : '' }} >{{ $item->nama }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
                <div class="form-group">
                  <label for="tujuan_id" class="col-sm-2 control-label">Tujuan *</label>
                  <div class="col-sm-10">
                    <select name="tujuan_id" id="tujuan_id" class="form-control selectpicker" title="-- Pilih tujuan --">
                      @foreach($tujuan as $item)
                        <option value="{{ $item->id }}" {{ $item->id == (old('tujuan_id') !== null ? old('tujuan_id') : $angkutan_tujuan->tujuan_id) ? 'selected' : '' }} >{{ $item->nama }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
                <div class="form-group">
                  <label for="harga" class="col-sm-2 control-label">Biaya *</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" name="harga" id="harga" placeholder="Biaya" value="{{ old('harga') !== null ? str_replace(',', '', old('harga')) : $angkutan_tujuan->harga }}">
                  </div>
                </div>
                
              </div>
              <!-- /.box-body -->
              
              <div class="box-footer">
                <div class="pull-right">
                  <a href="{{ url('/angkutan-tujuan') }}" class="btn btn-warning"><i class="fa fa-chevron-left"></i> Back</a>
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
    <!-- bootstrap select -->
    <script src="{{ asset('bower_components/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
    <script src="{{ asset('js/angkutan_tujuan.js') }}"></script>
      
    <!-- page script -->
    <script type="text/javascript">
    $(document).ready(function() {
        advanceElements.init();
        validation.init();
    });
    </script>
@endsection
