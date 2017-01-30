@extends('layouts.backend')

@section('other-css')
    
@endsection

@section('content')
    
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Role
        <small>Detail</small>
      </h1>
        
      <ol class="breadcrumb">
        <li><a href="{{ url('/') }}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="{{ url('/system/role') }}"> Role</a></li>
        <li class="Detail">Edit</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">

      <!-- Your Page Content Here -->
      
      <div class="row">
        
        <div class="col-xs-12">
          
          <div class="box box-info">
            
            <div class="box-header with-border">
              <h3 class="box-title">Role Data</h3>
            </div>
            <!-- /.box-header -->
            
            @include('flash::message')
            
            <!-- form start -->
            <form class="form-horizontal" id="frmData" method="post" action="{{ url('/system/role/update') }}/{{ $role->id }}" autocomplete="off">
              
              {{ csrf_field() }}
              
              <div class="box-body">
                
                <div class="form-group">
                  <label for="name" class="col-sm-2 control-label">Rolename </label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" name="name" id="name" style="background: #EDF7FA;" value="{{ old('name') !== null ? old('name') : $role->rolename }}" placeholder="Rolename" disabled>
                  </div>
                </div>
                <div class="form-group">
                  <label for="description" class="col-sm-2 control-label">Description </label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" name="description" id="description" style="background: #EDF7FA;" value="{{ old('description') !== null ? old('description') : $role->description }}" placeholder="Description" disabled>
                  </div>
                </div>
                
                <div class="form-group">
                  <label for="menus" class="col-sm-2 control-label">Menus </label>
                  <!-- first block -->
                  <div class="col-sm-5">
                      <table class="table table-bordered table-condensed">
                      
                      <tr>
                        <th style="width: 10px">#</th>
                        <th>Menu</th>
                      </tr>
                        
                      @for($i = 0; $i < $firstSectionCount; $i++)
                        
                        <tr>
                          <td>{{ $i+1 }}.</td>
                          <td>{{ $detail[$i]->menu->title }}</td>
                        </tr>
                          
                      @endfor
                      
                    </table>
                  </div>
                  <!-- second block -->
                  <div class="col-sm-5">
                      <table class="table table-bordered table-condensed">
                      
                      <tr>
                        <th style="width: 10px">#</th>
                        <th>Menu</th>
                      </tr>
                        
                      @for($i = $firstSectionCount; $i < $count; $i++)
                        
                        <tr>
                          <td>{{ $i+1 }}.</td>
                          <td>{{ $detail[$i]->menu->title }}</td>
                        </tr>
                          
                      @endfor
                      
                    </table>
                  </div>
                </div>
                  
              </div>
              <!-- /.box-body -->
              
              <div class="box-footer">
                <div class="pull-right">
                  <a href="{{ url('/system/role') }}" class="btn btn-primary"><i class="fa fa-chevron-left fa-fw"></i> Back</a>
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
    
@endsection
