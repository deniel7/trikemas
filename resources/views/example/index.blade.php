@extends('layouts.backend')

@section('title', 'Example')

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    Example
    <small>List</small>
  </h1>
  <ol class="breadcrumb">
    <li><a href="{{ url('/') }}"><i class="fa fa-tachometer"></i> Dashboard</a></li>
    <li><a href="#">Master</a></li>
    <li><a href="{{ url('/example') }}">Example</a></li>
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
        <div class="table-responsive">
          <table id="datatable" class="table table-bordered table-striped">
            <thead>
              <tr>
                <th>Name</th>
                <th>Description</th>
                <th>Created Date</th>
                <th>Updated Data</th>
                <th>Actions</th>
              </tr>
            </thead>
            <tbody>
            </tbody>
            <tfoot>
            <tr>
                <th>Name</th>
                <th>Description</th>
                <th>Created Date</th>
                <th>Updated Data</th>
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
<script type="text/javascript">
$(document).ready(function(){
  exampleModule.init();
});
</script>
@endsection