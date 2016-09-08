@extends('layouts.backend')

@section('title', 'My Account')

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
    My Account
    <small></small>
  </h1>
  <ol class="breadcrumb">
    <li><a href="{{ url('/') }}"><i class="fa fa-tachometer"></i> Dashboard</a></li>
    <li class="active">My Account</li>
  </ol>
</section>

<!-- Main content -->
<section class="content">
  <div class="row">
    <div class="col-xs-12">

      <div class="box">
        <!-- /.box-header -->
        <div class="box-body">

          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li class="active"><a aria-expanded="true" href="#profile" data-toggle="tab">Profile</a></li>
              <li class=""><a aria-expanded="false" href="#password" data-toggle="tab">Password</a></li>
            </ul>
            <div class="tab-content">
              <div class="tab-pane active" id="profile">

              <form action="{{ url('user/update-profile') }}" method="post">

              <?php echo csrf_field(); ?>
                
                <div class="form-group">
                  <label>Full Name</label>
                  <input class="form-control" name="full_name" placeholder="Enter full name" type="text" value="{{ Auth::user()->full_name }}">
                </div>
                <div class="form-group">
                  <label>Username</label>
                  <div class="form-control-static">
                  {{ Auth::user()->username }}
                  </div>
                </div>
                <div class="form-group">
                  <label>Email</label>
                  <input class="form-control" name="email" placeholder="Enter email" type="email" value="{{ Auth::user()->email }}">
                </div>
                <div class="form-group">
                  <input type="submit" value="Save" class="btn btn-primary">
                </div>
                

              </form>
                
              </div>
              <!-- /.tab-pane -->
              <div class="tab-pane" id="password">

              <form action="{{ url('user/update-password') }}" method="post">

              <?php echo csrf_field(); ?>
                
                <div class="form-group">
                  <label>Current Password</label>
                  <input class="form-control" name="current_password" placeholder="Enter current password" type="password">
                </div>
                <div class="form-group">
                  <label>New Password</label>
                  <input class="form-control" name="new_password" placeholder="Enter new password" type="password">
                </div>
                <div class="form-group">
                  <label>Confirm New Password</label>
                  <input class="form-control" name="new_password_confirmation" placeholder="Confirm new password" type="password">
                </div>
                <div class="form-group">
                  <input type="submit" value="Save" class="btn btn-primary">
                </div>
                

              </form>
              
              </div>
              <!-- /.tab-pane -->
            </div>
            <!-- /.tab-content -->
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
@endsection