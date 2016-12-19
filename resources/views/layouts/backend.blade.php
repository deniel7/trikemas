<!DOCTYPE html>
<!--
This is a starter template page. Use this page to start your new project from
scratch. This page gets rid of all links and provides the needed markup only.
-->
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>@yield('title')</title>
  <!-- Tell the browser to be responsive to screen width -->
  <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
  <!-- Bootstrap 3.3.5 -->
  <link rel="stylesheet" href="{{ asset('bower_components/AdminLTE/bootstrap/css/bootstrap.min.css') }}">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{ asset('bower_components/font-awesome/css/font-awesome.min.css') }}">
    <!-- Theme style -->
  <link rel="stylesheet" href="{{ asset('bower_components/AdminLTE/dist/css/AdminLTE.min.css') }}">
  <!-- DataTables -->
  <link rel="stylesheet" href="{{ asset('bower_components/AdminLTE/plugins/datatables/dataTables.bootstrap.css') }}">

  <!-- Datepicker -->
  <link rel="stylesheet" href="{{ asset('bower_components/AdminLTE/plugins/datepicker/datepicker3.css') }}">

  <!-- Sweet Alert -->
    <script src="{{ asset('bower_components/sweetalert/dist/sweetalert.min.js') }}"></script>
    <link rel="stylesheet" type="text/css" href="{{ asset('bower_components/sweetalert/dist/sweetalert.css') }}">
  
  <!-- Select2 -->
  <link rel="stylesheet" href="{{ asset('bower_components/AdminLTE/plugins/select2/select2.min.css') }}">

  @yield("other-css")
  
  <!-- CSRF Token -->
  <meta name="csrf-token" content="{{ csrf_token() }}">

  <!-- AdminLTE Skins. We have chosen the skin-blue for this starter
        page. However, you can choose any other skin. Make sure you
        apply the skin class to the body tag so the changes take effect.
      -->
      <link rel="stylesheet" href="{{ asset('bower_components/AdminLTE/dist/css/skins/skin-purple.min.css') }}">

      <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
      <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
  <!--[if lt IE 9]>
  <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
  <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
  <![endif]-->


  <!-- jQuery 2.1.4 -->
  <script src="{{ asset('bower_components/AdminLTE/plugins/jQuery/jQuery-2.1.4.min.js') }}"></script>

  <!-- CSS ALL -->
  <link rel="stylesheet" type="text/css" href="{{ asset('css/app.css') }}">


</head>

<body class="hold-transition skin-purple sidebar-mini">
  <div class="wrapper">

    <!-- Main Header -->
    <header class="main-header">

      <!-- Logo -->
      <a href="{{ url('/') }}" class="logo">
        <!-- mini logo for sidebar mini 50x50 pixels -->
        <span class="logo-mini"><b>S</b>A</span>
        <!-- logo for regular state and mobile devices -->
        <span class="logo-lg"><!-- <b>DN7Store</b>Apps --></span>
      </a>

      <!-- Header Navbar -->
      <nav class="navbar navbar-static-top" role="navigation">
        <!-- Sidebar toggle button-->
        <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
          <span class="sr-only">Toggle navigation</span>
        </a>
        <!-- Navbar Right Menu -->
        <div class="navbar-custom-menu">
          <ul class="nav navbar-nav">

            <!-- Notification -->
            <li class="dropdown notifications-menu">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                  <i class="fa fa-bell-o"></i>
                  <span class="label label-warning">10</span>
                </a>
                <ul class="dropdown-menu">
                  <li class="header">You have 10 notifications</li>
                  <li>
                    <!-- inner menu: contains the actual data -->
                    <ul class="menu">
                      <li>
                        <a href="#">
                          <i class="fa fa-users text-aqua"></i> 5 new members joined today
                        </a>
                      </li>
                    </ul>
                  </li>
                  <li class="footer"><a href="#">View all</a></li>
                </ul>
              </li>

            <!-- User Account Menu -->
            <li class="dropdown user user-menu">
              <!-- Menu Toggle Button -->
              <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                <!-- The user image in the navbar-->
                <img src="http://www.gravatar.com/avatar/{{ md5(Auth::user()->email) }}?d=identicon" class="user-image" alt="User Image">
                <!-- hidden-xs hides the username on small devices so only the image appears. -->
                <span class="hidden-xs">{{ Auth::user()->full_name }}</span>
              </a>
              <ul class="dropdown-menu">
                <!-- The user image in the menu -->
                <li class="user-header">
                  <img src="http://www.gravatar.com/avatar/{{ md5(Auth::user()->email) }}?d=identicon" class="img-circle" alt="User Image">

                  <p>
                    {{ Auth::user()->full_name }}
                    <small>Member since {{ Auth::user()->created_at->format('d F Y') }}</small>
                  </p>
                </li>
                <!-- Menu Body -->

                <!-- Menu Footer-->
                <li class="user-footer">
                  <div class="pull-left">
                    <a href="{{ url('user/profile') }}" class="btn btn-default btn-flat">My Account</a>
                  </div>
                  <div class="pull-right">
                    <a href="{{ url('/logout') }}" class="btn btn-default btn-flat">Log out</a>
                  </div>
                </li>
              </ul>
            </li>
          </ul>
        </div>
      </nav>
    </header>
    <!-- Left side column. contains the logo and sidebar -->
    <aside class="main-sidebar">

      <!-- sidebar: style can be found in sidebar.less -->
      <section class="sidebar">

        @include('layouts.sidebar.user-panel')

        @include('layouts.sidebar.menu')

      </section>
      <!-- /.sidebar -->
    </aside>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">

      @include('common.error')

      @include('flash::message')

      @yield('content')
    </div>
    <!-- /.content-wrapper -->

    <!-- Main Footer -->
    <footer class="main-footer">
      <!-- To the right -->
      <div class="pull-right hidden-xs">
        1.0.0
      </div>
      <!-- Default to the left -->
      <strong>Copyright &copy; 2015</strong>
    </footer>


  </div>
  <!-- ./wrapper -->

  <!-- REQUIRED JS SCRIPTS -->

  <!-- Bootstrap 3.3.5 -->
  <script src="{{ asset('bower_components/AdminLTE/bootstrap/js/bootstrap.min.js') }}"></script>
  <!-- AdminLTE App -->
  <script src="{{ asset('bower_components/AdminLTE/dist/js/app.min.js') }}"></script>

  <!-- DataTables -->
  <script src="{{ asset('bower_components/AdminLTE/plugins/datatables/jquery.dataTables.min.js') }}"></script>
  <script src="{{ asset('bower_components/AdminLTE/plugins/datatables/dataTables.bootstrap.min.js') }}"></script>
  <!-- SlimScroll -->
  <script src="{{ asset('bower_components/AdminLTE/plugins/slimScroll/jquery.slimscroll.min.js') }}"></script>
  <!-- FastClick -->
  <script src="{{ asset('bower_components/AdminLTE/plugins/fastclick/fastclick.js') }}"></script>
  <!-- Select2 -->
  <script src="{{ asset('bower_components/AdminLTE/plugins/select2/select2.min.js') }}"></script>
  <!-- Datepicker -->
  <script src="{{ asset('bower_components/AdminLTE/plugins/datepicker/bootstrap-datepicker.js') }}"></script>
  <!-- Axios -->
    <script src="{{ asset('bower_components/axios/dist/axios.min.js') }}"></script>
  <!-- JS ALL -->
  <script type="text/javascript" src="{{ asset('js/all.js') }}"></script>
  <!-- autoNumeric -->
  <script src="{{ asset('bower_components/autoNumeric/autoNumeric-min.js') }}"></script>

  <!-- This is only necessary if you do Flash::overlay('...') -->
  <script>
    $('#flash-overlay-modal').modal();
  </script>
    
  @yield("other-js")
  
</body>
</html>