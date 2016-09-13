@extends('layouts.backend')
@section('title', 'Edit Item')
@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
  <h1>
  Item
  <small>Edit</small>
  </h1>
  <ol class="breadcrumb">
    <li><a href="{{ url('/') }}"><i class="fa fa-tachometer"></i> Dashboard</a></li>
    <li><a href="#">Master</a></li>
    <li><a href="{{ url('/item') }}">Item</a></li>
    <li class="active">Edit</li>
  </ol>
</section>
<!-- Main content -->
<section class="content">
  <form action="{{ url('item').'/'.$item->id }}" method="post">
    <input name="_method" type="hidden" value="put">
    <input type="hidden" name="_token" value="{{ csrf_token() }}">
    
    <div class="row">
      <div class="col-lg-12">
        <div class="box">
          <!-- /.box-header -->
          <div class="box-body">
            <div class="row">
            <div class="col-lg-6">
                
                <div class="form-group">
                  <label>Model</label>
                  <div id="model_input">
                    <input type="text" class="form-control" placeholder="Enter your new item" name="model" value="{{ $item->model }}">
                  </div>
                </div>
              
            </div>
            </div>

             <div class="row">
            
            <div class="col-lg-3">
              <div class="form-group">
                <label>Size</label>
                <input type="text" class="form-control" name="size" value="{{ $item->size }}">
              </div>
            </div>

            <div class="col-lg-3">
              <div class="form-group">
                <label for="exampleInputEmail1">Color</label>
                <input type="text" class="form-control" name="color" value="{{ $item->color }}">
              </div>
            </div>

            <div class="col-lg-3">
              <div class="form-group">
                <label for="exampleInputEmail1">Stok</label>
                <input type="text" class="form-control" name="stok" value="{{ $item->stok }}">
              </div>
            </div>
            
          </div>
          <div class="row">
            
            <div class="col-lg-3">
              <div class="form-group">
                <label for="exampleInputEmail1">Normal Price</label>
                 <input type="text" class="form-control number" name="normal_price" value="{{ $item->normal_price }}">

                <!-- <input type="text" class="form-control number" data-input="normal_price" value="{{ old('normal_price') }}">
                <input type="hidden" name="normal_price"> -->
              </div>
            </div>

            <div class="col-lg-3">
              <div class="form-group">
                <label for="exampleInputEmail1">Reseller Price</label>
                <input type="text" class="form-control number" name="reseller_price" value="{{ $item->reseller_price }}">
                <!-- <input type="hidden" name="reseller_price"> -->
              </div>
            </div>

            <div class="col-lg-3">
              <div class="form-group">
                <label for="exampleInputEmail1">Buy Price</label>
                <input type="text" class="form-control number" name="buy_price" value="{{ $item->buy_price }}">
                <!-- <input type="hidden" name="reseller_price"> -->
              </div>
            </div>
          </div>
          
          <div class="row">
              <div class="col-lg-12">
                <div class="form-group">
                  <a role="button" href="{{ url('item') }}" class="btn btn-info"><i class="fa fa-chevron-left fa-fw"></i> Back</a>
                  <button type="submit" class="btn btn-primary"><i class="fa fa-send fa-fw"></i> Save</button>
                </div>
              </div>
          </div>

          </div>
          <!-- /.box-body -->
        </div>
        <!-- /.box -->
      </div>
      <!-- /.col -->
    </div>
    <!-- /.row -->
  </form>
</section>
<!-- page script -->
<script type="text/javascript">
$(document).ready(function(){
itemModule.init();
});
</script>
@endsection