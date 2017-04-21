@extends('layouts.backend')

@section('other-css')
    <link rel="stylesheet" href="{{ asset('bower_components/sweetalert/dist/sweetalert.css') }}">
    <!-- datatables button -->
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.3.1/css/buttons.dataTables.min.css">
@endsection

@section('content')
    
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Biaya Angkutan
        <small>List</small>
      </h1>
        
      <ol class="breadcrumb">
        <li><a href="{{ url('/') }}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li class="active">Biaya Angkutan</li>
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
                @if (in_array(151, session()->get('allowed_menus')))
                  <a href="{{ url('/angkutan-tujuan/create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> Add</a>
                @endif
              </div>
              <br><br>
              
              <div class="table-responsive">
                    
                <table id="list" class="table table-bordered table-striped table-condensed">
                  <thead>
                    <tr>
                      <th class="text-center">Angkutan</th>
                      <th class="text-center">Tujuan</th>
                      <th class="text-center">Biaya</th>
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
    <!-- datatables button -->
    <script src="https://cdn.datatables.net/buttons/1.3.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.3.1/js/buttons.flash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.27/build/pdfmake.min.js"></script>
    <script src="https://cdn.rawgit.com/bpampuch/pdfmake/0.1.27/build/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.3.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.3.1/js/buttons.print.min.js"></script>
    <script src="{{ asset('js/angkutan_tujuan.js') }}"></script>
      
    <!-- page script -->
    <script type="text/javascript">
    $(document).ready(function() {
        datatables.init();
    });
    </script>
@endsection
