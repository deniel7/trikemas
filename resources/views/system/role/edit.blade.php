@extends('layouts.backend')

@section('other-css')
    <link rel="stylesheet" href="{{ asset('vendor/formvalidation/formValidation.css') }}">
    <!-- iCheck for checkboxes and radio inputs -->
    <link rel="stylesheet" href="{{ asset('bower_components/AdminLTE/plugins/iCheck/all.css') }}">
@endsection

@section('content')
    
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Role
        <small>Edit</small>
      </h1>
        
      <ol class="breadcrumb">
        <li><a href="{{ url('/') }}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="{{ url('/system/role') }}"> Role</a></li>
        <li class="active">Edit</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">

      <!-- Your Page Content Here -->
      
      <div class="row">
        
        <div class="col-xs-12">
          
          <div class="box box-warning">
            
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
                  <label for="name" class="col-sm-2 control-label">Rolename *</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" name="name" id="name" value="{{ old('name') !== null ? old('name') : $role->rolename }}" placeholder="Rolename">
                  </div>
                </div>
                <div class="form-group">
                  <label for="description" class="col-sm-2 control-label">Description </label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" name="description" id="description" value="{{ old('description') !== null ? old('description') : $role->description }}" placeholder="Description">
                  </div>
                </div>
                <div class="form-group">
                  
                  <label class="col-sm-2 control-label">Menus </label>
                  <div class="col-sm-10">
                    <input type="checkbox" id="checkAll">
                    <label>Select all</label>
                    <div id="alertDayMessage"></div>
                  </div>
                   
                  <!-- The container to place the error of checkboxes -->
                  
                  <label class="col-sm-2 control-label lbl-menu">&nbsp; </label>
                  <!-- first block -->
                  <div class="col-sm-5">
                      <table class="table table-bordered table-condensed">
                      
                      @for($i = 0; $i < $firstSectionCount; $i++)
                        
                        <tr>
                          <td style="width: 10px">
                              <label>
                                <input type="checkbox" name="menu[]" {{ in_array($menus[$i]->id_menu, (is_array(old('menu')) ? old('menu') : array())) ? 'checked' : (in_array($menus[$i]->id_menu, $role_dtl) ? 'checked' : '') }} value="{{ $menus[$i]->id_menu }}" class="flat-red cb-menu">
                              </label>
                            
                          </td>
                          <td>{{ $menus[$i]->title }}</td>
                        </tr>
                          
                      @endfor
                      
                    </table>
                  </div>
                  <!-- second block -->
                  <div class="col-sm-5">
                      <table class="table table-bordered table-condensed">
                        
                      @for($i = $firstSectionCount; $i < $count; $i++)
                        
                        <tr>
                          <td style="width: 10px">
                              <label>
                                <input type="checkbox" name="menu[]" {{ in_array($menus[$i]->id_menu, (is_array(old('menu')) ? old('menu') : array())) ? 'checked' : (in_array($menus[$i]->id_menu, $role_dtl) ? 'checked' : '') }} value="{{ $menus[$i]->id_menu }}" class="flat-red cb-menu">
                              </label>
                            
                          </td>
                          <td>{{ $menus[$i]->title }}</td>
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
                  <button type="submit" class="btn btn-warning" id="btnSubmit"><i class="fa fa-refresh fa-fw"></i> Update</button>
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
    <!-- iCheck 1.0.1 -->
    <script src="{{ asset('bower_components/AdminLTE/plugins/iCheck/icheck.min.js') }}"></script>
    <script src="{{ asset('js/system.js') }}"></script>
    
    <!-- page script -->
    <script type="text/javascript">
    $(document).ready(function(){
        validationRole.init();
        advanceElementsRole.init();
    });
    </script>
@endsection
