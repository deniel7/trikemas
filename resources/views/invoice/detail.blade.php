@extends('layouts.backend')

@section('other-css')
    
@endsection

@section('content')
    
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Invoice Penjualan
        <small>Detail</small>
      </h1>
        
      <ol class="breadcrumb">
        <li><a href="{{ url('/') }}"><i class="fa fa-dashboard"></i> Home</a></li>
        <li><a href="{{ url('/invoice') }}"> Invoice Penjualan</a></li>
        <li class="active">Detail</li>
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
                  <label for="no_invoice" class="col-sm-2 control-label">No. Invoice </label>
                  <div class="col-sm-10">
                    <input type="text" style="background: #EDF7FA;" class="form-control" name="no_invoice" id="no_invoice" placeholder="No. Invoice" value="{{ $invoice_penjualan->no_invoice }}" readonly>
                  </div>
                </div>
                <div class="form-group">
                  <label for="tanggal" class="col-sm-2 control-label">Tanggal </label>
                  <div class="col-sm-10">
                    <div class="input-group date">
                      <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                      </div>
                      <input type="text" style="background: #EDF7FA;" class="form-control" name="tanggal" id="tanggal" placeholder="Tanggal" value="{{ $invoice_penjualan->tanggal->format('d/m/Y') }}" readonly>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <label for="konsumen_id" class="col-sm-2 control-label">Konsumen </label>
                  <div class="col-sm-10">
                    <input type="text" style="background: #EDF7FA;" class="form-control" name="konsumen_id" id="konsumen_id" placeholder="Konsumen" value="{{ $konsumen->find($invoice_penjualan->konsumen_id)->nama }}" readonly>
                  </div>
                </div>
                <div class="form-group">
                  <label for="tanggal_jatuh_tempo" class="col-sm-2 control-label">Tgl. Jatuh Tempo </label>
                  <div class="col-sm-10">
                    <div class="input-group date">
                      <div class="input-group-addon">
                        <i class="fa fa-calendar"></i>
                      </div>
                      <input type="text"style="background: #EDF7FA;" class="form-control" name="tanggal_jatuh_tempo" id="tanggal_jatuh_tempo" placeholder="Tanggal Jatuh Tempo" value="{{ $invoice_penjualan->tgl_jatuh_tempo->format('d/m/Y') }}" readonly>
                    </div>
                  </div>
                </div>
                <div class="form-group">
                  <label for="no_po" class="col-sm-2 control-label">No. PO</label>
                  <div class="col-sm-10">
                    <input type="text" style="background: #EDF7FA;" class="form-control" name="no_po" id="no_po" placeholder="No. PO" value="{{ $invoice_penjualan->no_po }}" readonly>
                  </div>
                </div>
                <div class="form-group">
                  <label for="angkutan_id" class="col-sm-2 control-label">Angkutan </label>
                  <div class="col-sm-10">
                    <input type="text" style="background: #EDF7FA;" class="form-control" name="angkutan_id" id="angkutan_id" placeholder="Angkutan" value="{{ $angkutan->find($invoice_penjualan->angkutan_id)->nama }}" readonly>
                  </div>
                </div>
                <div class="form-group">
                  <label for="tujuan_id" class="col-sm-2 control-label">Tujuan </label>
                  <div class="col-sm-10">
                    <input type="text" style="background: #EDF7FA;" class="form-control" name="tujuan_id" id="tujuan_id" placeholder="Tujuan" value="{{ $tujuan->find($invoice_penjualan->tujuan_id)->kota }}" readonly>
                  </div>
                </div>
                <div class="form-group">
                  <label for="no_surat_jalan" class="col-sm-2 control-label">No. Surat Jalan </label>
                  <div class="col-sm-10">
                    <input type="text" style="background: #EDF7FA;" class="form-control" name="no_surat_jalan" id="no_surat_jalan" placeholder="No. Surat Jalan" value="{{ $invoice_penjualan->no_surat_jalan }}" readonly>
                  </div>
                </div>
                <div class="form-group">
                  <label for="no_mobil" class="col-sm-2 control-label">No. Mobil </label>
                  <div class="col-sm-10">
                    <input type="text" style="background: #EDF7FA;" class="form-control" name="no_mobil" id="no_mobil" placeholder="No. Mobil" value="{{ $invoice_penjualan->no_mobil }}" readonly>
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
                          </tr>
                          
                          @for($i = 0; $i < sizeof($detail_penjualan); $i++)
                            
                            <tr>
                              <td>
                                <input type="text" value="{{ $barang_helper->find($detail_penjualan[$i]->barang_id)->nama }}" style="width: 310px; background: #EDF7FA;" class="form-control nama_barang" name="nama_barang[]" id="nama_barang_{{ $i+1 }}" placeholder="Nama barang" readonly>
                              </td>
                              <td class="text-right">
                                <input type="text" value="{{ number_format($detail_penjualan[$i]->jumlah_ball, 0, '.', ',') }}" style="width: 80px; background: #EDF7FA;" class="form-control text-right ball" name="ball[]" id="ball_{{ $i+1 }}" placeholder="Ball" readonly>
                              </td>
                              <td class="text-right">
                                <input type="text" value="{{ number_format($detail_penjualan[$i]->jumlah, 0, '.', ',') }}" style="width: 80px; background: #EDF7FA;" class="form-control text-right pcs" name="pcs[]" id="pcs_{{ $i+1 }}" placeholder="Pcs" readonly>
                              </td>
                              <td class="text-right">
                                <input type="text" value="{{ number_format($detail_penjualan[$i]->harga_barang, 2, '.', ',') }}" style="width: 130px; background: #EDF7FA;" class="form-control text-right harga" name="harga[]" id="harga_{{ $i+1 }}" placeholder="Harga" readonly>
                              </td>
                              <td class="text-right">
                                <input type="text" value="{{ number_format($detail_penjualan[$i]->subtotal, 2, '.', ',') }}" style="width: 130px; background: #EDF7FA;" class="form-control text-right jumlah" name="jumlah[]" id="jumlah_{{ $i+1 }}" placeholder="Jumlah" readonly>
                                <input type="hidden" id="pcs_in_ball_{{ $i+1 }}" value="{{ $barang_helper->find($detail_penjualan[$i]->barang_id)->pcs }}">
                              </td>
                            </tr>
                              
                          @endfor
                            
                        </table>
                      </div>
                      <!-- /.box-body -->
                      
                      <div class="box-footer clearfix">
                        &nbsp;  
                      </div>
                      <!-- /.box-footer -->
                      
                    </div>
                    <!-- /.box -->
                    
                  </div>
                </div>
                
                <div class="form-group">
                  <label for="sub_total" class="col-sm-9 control-label">Sub Total</label>
                  <div class="col-sm-3">
                    <input type="text" style="background: #EDF7FA;" class="form-control text-right" name="sub_total" id="sub_total" placeholder="Sub Total" value="{{ number_format($invoice_penjualan->sub_total, 2, '.', ',') }}" readonly>
                  </div>
                </div>
                <div class="form-group">
                  <label for="discount" class="col-sm-9 control-label">Discount</label>
                  <div class="col-sm-3">
                    <input type="text" style="background: #EDF7FA;" class="form-control text-right" name="discount" id="discount" placeholder="Discount" value="{{ number_format($invoice_penjualan->diskon, 2, '.', ',') }}" readonly>
                  </div>
                </div>
                <div class="form-group">
                  <label for="total" class="col-sm-9 control-label">Total</label>
                  <div class="col-sm-3">
                    <input type="text" style="background: #EDF7FA;" class="form-control text-right" name="total" id="total" placeholder="Total" value="{{ number_format($invoice_penjualan->total, 2, '.', ',') }}" readonly>
                  </div>
                </div>
                <div class="form-group">
                  <label for="ppn" class="col-sm-9 control-label">PPN</label>
                  <div class="col-sm-3">
                    <input type="text" style="background: #EDF7FA;" class="form-control text-right" name="ppn" id="ppn" placeholder="PPN" value="{{ number_format($invoice_penjualan->ppn, 2, '.', ',') }}" readonly>
                  </div>
                </div>
                <div class="form-group">
                  <label for="grand_total" class="col-sm-9 control-label">Grand Total</label>
                  <div class="col-sm-3">
                    <input type="text" style="background: #EDF7FA;" class="form-control text-right" name="grand_total" id="grand_total" placeholder="Grand Total" value="{{ number_format($invoice_penjualan->grand_total, 2, '.', ',') }}" readonly>
                  </div>
                </div>
                  
              </div>
              <!-- /.box-body -->
              
              <div class="box-footer">
                <div class="pull-right">
                  <a href="{{ url('/invoice') }}" class="btn btn-warning"><i class="fa fa-chevron-left"></i> Back</a>
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
    
@endsection
