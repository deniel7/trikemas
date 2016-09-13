@extends('layouts.backend')

@section('other-css')
    <link rel="stylesheet" href="{{ asset('bower_components/sweetalert/dist/sweetalert.css') }}">
@endsection

@section('content')
    
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Barang
        <small>List</small>
      </h1>
        
      <ol class="breadcrumb">
        <li><a href="{{ url('/') }}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Barang</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">

      <!-- Your Page Content Here -->
      
      <div class="row">
        
        <div class="col-xs-12">
          
          <div class="box box-primary">
            
            <div class="box-body">
              
              <div class="btn-group">
                <a href="{{ url('/barang/create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> Add</a>
              </div>
              <br><br>
              
              <div class="table-responsive">
                    
                <table id="list" class="table table-bordered table-striped table-condensed">
                  <thead>
                    <tr>
                      <th class="text-center">Nama</th>
                      <th class="text-center">Jenis</th>
                      <th class="text-center">Pcs</th>
                      <th class="text-center">Berat</th>
                      <th class="text-center" width="10%">Action</th>
                    </tr>
                  </thead>
                  <tbody>
                  </tbody>
                </table>
              
              </div>
              <!-- end table-responsive -->
            
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
    
@endsection

@section('other-js')
    <script src="{{ asset('bower_components/sweetalert/dist/sweetalert.min.js') }}"></script>
    <script src="{{ asset('js/barang.js') }}"></script>
      
    <!-- page script -->
    <script type="text/javascript">
    $(document).ready(function() {
        datatables.init();
    });
    </script>
@endsection
