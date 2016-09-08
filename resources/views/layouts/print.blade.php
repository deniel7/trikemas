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
  
  <!-- CSS ALL -->

<style type="text/css">
  
  /* Print styling */

@media print {

[class*="col-sm-"] {
  float: left;
}

[class*="col-xs-"] {
  float: left;
}

.col-sm-12, .col-xs-12 { 
  width:100% !important;
}

.col-sm-11, .col-xs-11 { 
  width:91.66666667% !important;
}

.col-sm-10, .col-xs-10 { 
  width:83.33333333% !important;
}

.col-sm-9, .col-xs-9 { 
  width:75% !important;
}

.col-sm-8, .col-xs-8 { 
  width:66.66666667% !important;
}

.col-sm-7, .col-xs-7 { 
  width:58.33333333% !important;
}

.col-sm-6, .col-xs-6 { 
  width:50% !important;
}

.col-sm-5, .col-xs-5 { 
  width:41.66666667% !important;
}

.col-sm-4, .col-xs-4 { 
  width:33.33333333% !important;
}

.col-sm-3, .col-xs-3 { 
  width:25% !important;
}

.col-sm-2, .col-xs-2 { 
  width:16.66666667% !important;
}

.col-sm-1, .col-xs-1 { 
  width:8.33333333% !important;
}
  
.col-sm-1,
.col-sm-2,
.col-sm-3,
.col-sm-4,
.col-sm-5,
.col-sm-6,
.col-sm-7,
.col-sm-8,
.col-sm-9,
.col-sm-10,
.col-sm-11,
.col-sm-12,
.col-xs-1,
.col-xs-2,
.col-xs-3,
.col-xs-4,
.col-xs-5,
.col-xs-6,
.col-xs-7,
.col-xs-8,
.col-xs-9,
.col-xs-10,
.col-xs-11,
.col-xs-12 {
float: left !important;
}

body {
  margin: 0;
  padding 0 !important;
  min-width: 768px;
}

.container {
  width: auto;
  min-width: 750px;
}

body {
  font-size: 10px;
}

a[href]:after {
  content: none;
}

.noprint, 
div.alert, 
header, 
.group-media, 
.btn, 
.footer, 
form, 
#comments, 
.nav, 
ul.links.list-inline,
ul.action-links {
  display:none !important;
}

}

</style>

</head>

<body>
  @yield('content')
</body>
</html>