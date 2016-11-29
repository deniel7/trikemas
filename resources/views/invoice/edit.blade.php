@extends('layouts.backend')

@section('other-css')
    <link rel="stylesheet" href="{{ asset('vendor/formvalidation/formValidation.css') }}">
    <!-- bootstrap datepicker -->
    <link rel="stylesheet" href="{{ asset('bower_components/AdminLTE/plugins/datepicker/datepicker3.css') }}">
    <!-- bootstrap select -->
    <link rel="stylesheet" href="{{ asset('bower_components/bootstrap-select/dist/css/bootstrap-select.min.css') }}">
    <!-- jquery-auto-complete -->
    <link rel="stylesheet" href="{{ asset('bower_components/jquery-auto-complete/jquery.auto-complete.css') }}">
@endsection

@section('content')
    
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Invoice Penjualan
        <small>Edit</small>
      </h1>
        
      <ol class="breadcrumb">
        <li><a href="{{ url('/') }}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="{{ url('/invoice') }}"> Invoice Penjualan</a></li>
        <li class="active">Edit</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">

      <!-- Your Page Content Here -->
      
      <div class="row">
        
        <div class="col-xs-12">
          
          <div class="box box-primary">
            
            <div class="box-header with-border">
              <h3 class="box-title">Data Invoice</h3>
            </div>
            <!-- /.box-header -->
            
            <!-- form start -->
            <form class="form-horizontal" id="frmData" method="post" action="{{ url('/invoice') }}/{{ $invoice_penjualan->id }}" autocomplete="off">
              
              {{ csrf_field() }}
              {{ method_field('PUT') }}
              
              <div class="box-body">
                
                <div class="form-group">
                  <label for="no_invoice" class="col-sm-2 control-label">No. Invoice *</label>
                  <div class="col-sm-10">
                    <input type="text" style="background: #EDF7FA;" class="form-control" name="no_invoice" id="no_invoice" placeholder="No. Invoice" value="{{ old('no_invoice') !== null ? old('no_invoice') : $invoice_penjualan->no_invoice }}" readonly>
                  </div>
                </div>
                <div class="form-group">
                  <label for="tanggal" class="col-sm-2 control-label">Tanggal *</label>
                  <div class="col-sm-10">
                    <div class="input-group date">
                      <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                      </div>
                      <input type="text" class="form-control" name="tanggal" id="tanggal" placeholder="Tanggal" value="{{ old('tanggal') !== null ? old('tanggal') : $invoice_penjualan->tanggal->format('d/m/Y') }}">
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <label for="konsumen_id" class="col-sm-2 control-label">Konsumen *</label>
                  <div class="col-sm-10">
                    <select name="konsumen_id" id="konsumen_id" class="form-control selectpicker" title="-- Pilih konsumen --">
                      @foreach($konsumen as $item)
                        <option value="{{ $item->id }}" {{ $item->id == (old('konsumen_id') !== null ? old('konsumen_id') : $invoice_penjualan->konsumen_id) ? 'selected' : '' }} >{{ $item->nama }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
                <div class="form-group">
                  <label for="konsumen_branch_id" class="col-sm-2 control-label">Konsumen Branch </label>
                  <div class="col-sm-10">
                    <select name="konsumen_branch_id" id="konsumen_branch_id" class="form-control selectpicker" title="-- Pilih konsumen branch --">
                      @foreach($konsumen_branch as $branch)
                        <option value="{{ $branch->id }}" {{ $branch->id == (old('konsumen_branch_id') !== null ? old('konsumen_branch_id') : $invoice_penjualan->konsumen_branch_id) ? 'selected' : '' }} >{{ $branch->nama }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
                <div class="form-group">
                  <label for="tanggal_jatuh_tempo" class="col-sm-2 control-label">Tgl. Jatuh Tempo *</label>
                  <div class="col-sm-10">
                    <div class="input-group date">
                      <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                      </div>
                      <input type="text" class="form-control" name="tanggal_jatuh_tempo" id="tanggal_jatuh_tempo" placeholder="Tanggal Jatuh Tempo" value="{{ old('tanggal_jatuh_tempo') !== null ? old('tanggal_jatuh_tempo') : $invoice_penjualan->tgl_jatuh_tempo->format('d/m/Y') }}">
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <label for="no_po" class="col-sm-2 control-label">No. PO</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" name="no_po" id="no_po" placeholder="No. PO" value="{{ old('no_po') !== null ? old('no_po') : $invoice_penjualan->no_po }}">
                  </div>
                </div>
                <div class="form-group">
                  <label for="angkutan_id" class="col-sm-2 control-label">Angkutan *</label>
                  <div class="col-sm-10">
                    <select name="angkutan_id" id="angkutan_id" class="form-control selectpicker" title="-- Pilih angkutan --">
                      @foreach($angkutan as $item)
                        <option value="{{ $item->id }}" {{ $item->id == (old('angkutan_id') !== null ? old('angkutan_id') : $invoice_penjualan->angkutan_id) ? 'selected' : '' }} >{{ $item->nama }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
                <div class="form-group">
                  <label for="tujuan_id" class="col-sm-2 control-label">Tujuan *</label>
                  <div class="col-sm-10">
                    <select name="tujuan_id" id="tujuan_id" class="form-control selectpicker" title="-- Pilih tujuan --">
                      @foreach($tujuan as $item)
                        <option value="{{ $item->id }}" {{ $item->id == (old('tujuan_id') !== null ? old('tujuan_id') : $invoice_penjualan->tujuan_id) ? 'selected' : '' }} >{{ $item->nama }}</option>
                      @endforeach
                    </select>
                  </div>
                </div>
                <div class="form-group">
                  <label for="no_surat_jalan" class="col-sm-2 control-label">No. Surat Jalan *</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" name="no_surat_jalan" id="no_surat_jalan" placeholder="No. Surat Jalan" value="{{ old('no_surat_jalan') !== null ? old('no_surat_jalan') : $invoice_penjualan->no_surat_jalan }}">
                  </div>
                </div>
                <div class="form-group">
                  <label for="no_mobil" class="col-sm-2 control-label">No. Mobil *</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" name="no_mobil" id="no_mobil" placeholder="No. Mobil" value="{{ old('no_mobil') !== null ? old('no_mobil') : $invoice_penjualan->no_mobil }}">
                  </div>
                </div>
                
                <div class="form-group">
                  <label for="detail_barang" class="col-sm-2 control-label">&nbsp;</label>
                  <div class="col-sm-10">  
                    
                    <div class="box">
                      
                      <div class="box-header with-border">
                        <h3 class="box-title">Detail Barang</h3>
                      </div>
                      <!-- /.box-header -->
                      
                      <div class="box-body table-responsive no-padding">
                        <table class="table table-hover" id="tbl_detail">
                          <tr>
                            <th>Nama Barang</th>
                            <th class="text-right">Ball</th>
                            <th class="text-right">Pcs</th>
                            <th class="text-right">Harga / Pcs</th>
                            <th class="text-right">Jumlah</th>
                            <th>&nbsp;</th>
                          </tr>
                          
                          @for($i = 0; $i < sizeof($detail_penjualan); $i++)
                            
                            <tr>
                              <td>
                                <input type="text" value="{{ $detail_penjualan[$i]->barang_id . ' | ' . $barang_helper->find($detail_penjualan[$i]->barang_id)->nama }}" style="width: 300px;" class="form-control nama_barang" name="nama_barang[]" id="nama_barang_{{ $i+1 }}" placeholder="Nama barang">
                              </td>
                              <td class="text-right">
                                <input type="text" value="{{ number_format($detail_penjualan[$i]->jumlah_ball, 0, '.', ',') }}" style="width: 70px;" class="form-control text-right ball" name="ball[]" id="ball_{{ $i+1 }}" placeholder="Ball">
                              </td>
                              <td class="text-right">
                                <input type="text" value="{{ number_format($detail_penjualan[$i]->jumlah, 0, '.', ',') }}" style="width: 70px; background: #EDF7FA;" class="form-control text-right pcs" name="pcs[]" id="pcs_{{ $i+1 }}" placeholder="Pcs" readonly>
                              </td>
                              <td class="text-right">
                                <input type="text" value="{{ number_format($detail_penjualan[$i]->harga_barang, 2, '.', ',') }}" style="width: 120px; background: #EDF7FA;" class="form-control text-right harga" name="harga[]" id="harga_{{ $i+1 }}" placeholder="Harga" readonly>
                              </td>
                              <td class="text-right">
                                <input type="text" value="{{ number_format($detail_penjualan[$i]->subtotal, 2, '.', ',') }}" style="width: 120px; background: #EDF7FA;" class="form-control text-right jumlah" name="jumlah[]" id="jumlah_{{ $i+1 }}" placeholder="Jumlah" readonly>
                              </td>
                              <td>
                                <input type="hidden" id="pcs_in_ball_{{ $i+1 }}" value="{{ $barang_helper->find($detail_penjualan[$i]->barang_id)->pcs }}">
                                <a href="#" class="btn btn-danger btn-flat btn-del-row"><i class="fa fa-minus-circle"></i></a>
                              </td>
                            </tr>
                              
                          @endfor
                            
                        </table>
                      </div>
                      <!-- /.box-body -->
                      
                      <div class="box-footer clearfix">
                        <ul class="pagination pagination-sm no-margin pull-right">
                          <li><a href="#" class="btn-add-row">more..</a></li>    
                        </ul>
                      </div>
                      <!-- /.box-footer -->
                      
                    </div>
                    <!-- /.box -->
                    
                  </div>
                </div>
                
                <div class="form-group">
                  <label for="sub_total" class="col-sm-9 control-label">Sub Total</label>
                  <div class="col-sm-3">
                    <input type="text" style="background: #EDF7FA;" class="form-control text-right" name="sub_total" id="sub_total" placeholder="Sub Total" value="{{ old('sub_total') !== null ? old('sub_total') : number_format($invoice_penjualan->sub_total, 2, '.', ',') }}" readonly>
                  </div>
                </div>
                <div class="form-group">
                  <label for="discount" class="col-sm-9 control-label">Discount</label>
                  <div class="col-sm-3">
                    <input type="text" class="form-control text-right" name="discount" id="discount" placeholder="Discount" value="{{ old('discount') !== null ? str_replace(',', '', old('discount')) : $invoice_penjualan->diskon }}">
                  </div>
                </div>
                <div class="form-group">
                  <label for="total" class="col-sm-9 control-label">Total</label>
                  <div class="col-sm-3">
                    <input type="text" style="background: #EDF7FA;" class="form-control text-right" name="total" id="total" placeholder="Total" value="{{ old('total') !== null ? old('total') : number_format($invoice_penjualan->total, 2, '.', ',') }}" readonly>
                  </div>
                </div>
                <div class="form-group">
                  <label for="ppn" class="col-sm-9 control-label">PPN</label>
                  <div class="col-sm-3">
                    <input type="text" style="background: #EDF7FA;" class="form-control text-right" name="ppn" id="ppn" placeholder="PPN" value="{{ old('ppn') !== null ? old('ppn') : number_format($invoice_penjualan->ppn, 2, '.', ',') }}" readonly>
                  </div>
                </div>
                <div class="form-group">
                  <label for="grand_total" class="col-sm-9 control-label">Grand Total</label>
                  <div class="col-sm-3">
                    <input type="text" style="background: #EDF7FA;" class="form-control text-right" name="grand_total" id="grand_total" placeholder="Grand Total" value="{{ old('grand_total') !== null ? old('grand_total') : number_format($invoice_penjualan->grand_total, 2, '.', ',') }}" readonly>
                  </div>
                </div>
                  
              </div>
              <!-- /.box-body -->
              
              <div class="box-footer">
                <div class="pull-right">
                  <a href="{{ url('/invoice') }}" class="btn btn-warning"><i class="fa fa-chevron-left"></i> Back</a>
                  <button type="submit" class="btn btn-primary" id="btnSubmit"><i class="fa fa-refresh"></i> Update</button>
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
    
    <input type="hidden" id="last_index" value="{{ sizeof($detail_penjualan) }}">
    
@endsection

@section('other-js')
    <script src="{{ asset('vendor/formvalidation/formValidation.min.js') }}"></script>
    <script src="{{ asset('vendor/formvalidation/framework/bootstrap.min.js') }}"></script>
    <script src="{{ asset('bower_components/autoNumeric/autoNumeric.js') }}"></script>
    <!-- bootstrap datepicker -->
    <script src="{{ asset('bower_components/AdminLTE/plugins/datepicker/bootstrap-datepicker.js') }}"></script>
    <!-- bootstrap select -->
    <script src="{{ asset('bower_components/bootstrap-select/dist/js/bootstrap-select.min.js') }}"></script>
    <!-- jquery-auto-complete -->
    <script src="{{ asset('bower_components/jquery-auto-complete/jquery.auto-complete.min.js') }}"></script>
    <script src="{{ asset('js/invoice.js') }}"></script>
      
    <!-- page script -->
    <script type="text/javascript">
    $(document).ready(function() {
        advanceElements.init();
        validation.init();
    });
    </script>
@endsection
