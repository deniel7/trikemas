@extends('layouts.backend')

@section('other-css')
    <link rel="stylesheet" href="{{ asset('vendor/formvalidation/formValidation.css') }}">
    <!-- iCheck for checkboxes and radio inputs -->
    <link rel="stylesheet" href="{{ asset('bower_components/AdminLTE/plugins/iCheck/all.css') }}">
    <!-- bootstrap select -->
    <link rel="stylesheet" href="{{ asset('bower_components/bootstrap-select/dist/css/bootstrap-select.min.css') }}">
    <!-- dropify -->
    <link rel="stylesheet" href="{{ asset('bower_components/dropify/dist/css/dropify.min.css') }}">
@endsection

@section('content')
    
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        User
        <small>Edit</small>
      </h1>
        
      <ol class="breadcrumb">
        <li><a href="{{ url('/') }}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="{{ url('/system/user') }}"> User</a></li>
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
              <h3 class="box-title">Data User</h3>
            </div>
            <!-- /.box-header -->
            
            @include('flash::message')
            
            <!-- form start -->
            <form class="form-horizontal" id="frmData" method="post" action="{{ url('/system/user/update') }}/{{ $user->id }}" autocomplete="off" enctype="multipart/form-data">
              
              {{ csrf_field() }}
              
              <div class="box-body">
                
                <div class="form-group">
                  <label for="username" class="col-sm-2 control-label">Username *</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" name="username" id="username" value="{{ old('username') !== null ? old('username') : $user->username }}" placeholder="Username">
                  </div>
                </div>
                <div class="form-group">
                  <label for="name" class="col-sm-2 control-label">Name *</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" name="name" id="name" placeholder="Name" value="{{ old('name') !== null ? old('name') : $user->name }}">
                  </div>
                </div>
                <div class="form-group">
                  <label for="changepwd" class="col-sm-2 control-label">Change Password </label>
                  <div class="col-sm-10">
                    <input type="checkbox" name="changepwd" id="changepwd" class="flat-red">
                  </div>
                </div>
                <div class="form-group">
                  <label for="password" class="col-sm-2 control-label">Password </label>
                  <div class="col-sm-10">
                    <input type="password" class="form-control password" name="password" id="password" placeholder="Password" disabled>
                  </div>
                </div>
                <div class="form-group">
                  <label for="confirm_password" class="col-sm-2 control-label">Confirm Password </label>
                  <div class="col-sm-10">
                    <input type="password" class="form-control password" name="confirm_password" id="confirm_password" placeholder="Confirm Password" disabled>
                  </div>
                </div>
                <div class="form-group">
                  <label for="email" class="col-sm-2 control-label">Email </label>
                  <div class="col-sm-10">
                    <input type="email" class="form-control" name="email" id="email" placeholder="Email" value="{{ old('email') !== null ? old('email') : $user->email }}">
                  </div>
                </div>
                <div class="form-group">
                  <label for="rolename" class="col-sm-2 control-label">Rolename *</label>
                  <div class="col-sm-10">
                    <select name="rolename" id="rolename" class="form-control selectpicker" title="-- Pilih role --">
                      @foreach($role as $item)
                        <option value="{{ $item->rolename }}" {{ $item->rolename == (old('rolename') !== null ? old('rolename') : $user->rolename) ? 'selected' : '' }} >{{ $item->rolename }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
                <div class="form-group">
                  <label for="active" class="col-sm-2 control-label">Active *</label>
                  <div class="col-sm-10">
                    <input type="radio" name="active" value="1" class="flat-red" {{ (old('active') !== null ? old('active') : $user->active) == '1' ? 'checked' : '' }}  > Active &nbsp;&nbsp;&nbsp;
                    <input type="radio" name="active" value="0" class="flat-red" {{ (old('active') !== null ? old('active') : $user->active) == '0' ? 'checked' : '' }}> Inactive
                  </div>
                </div>
                <div class="form-group">
                  <label for="foto" class="col-sm-2 control-label">Foto</label>
                  <div class="col-sm-2">
                    <input type="file" name="foto" id="foto" class="dropify dropify-photo"
                          data-allowed-file-extensions="jpg png"
                          data-height="170"
                          data-max-file-size="1M"
                          data-max-file-size-preview="1M"
                          data-default-file="{{ $foto && File::exists(config('constants.user.photo') . '/' . $foto) ? asset(config('constants.user.url_photo') . '/' . $foto) : '' }}" >
                  </div>
                </div>
                <input type="hidden" name="foto_asal" id="foto_asal" value="{{ $foto }}">
                
              </div>
              <!-- /.box-body -->
              
              <div class="box-footer">
                <div class="pull-right">
                  <a href="{{ url('/system/user') }}" class="btn btn-primary"><i class="fa fa-chevron-left fa-fw"></i> Back</a>
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
    <!-- bootstrap select -->
    <script src="{{ asset('bower_components/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
    <!-- dropify -->
    <script src="{{ asset('bower_components/dropify/dist/js/dropify.min.js') }}"></script>
    <script src="{{ asset('js/system.js') }}"></script>
      
    <!-- page script -->
    <script type="text/javascript">
    $(document).ready(function(){
        validationUser.init();
        advanceElementsUser.init();
    });
    </script>
@endsection
