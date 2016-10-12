<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Datatables;
use Flash;
use Auth;
use Carbon\Carbon;
use App\InvoicePenjualan;
use App\DetailPenjualan;
use App\Konsumen;
use App\Barang;
use App\Angkutan;
use App\Tujuan;
use App\AngkutanTujuan;
use DB;
use PDF;

class InvoiceController extends Controller
{
    public function datatables(Request $request) {
        $list = DB::table('invoice_penjualans')
                ->join('konsumens', 'konsumens.id', '=', 'invoice_penjualans.konsumen_id')
                ->join('angkutans', 'angkutans.id', '=', 'invoice_penjualans.angkutan_id')
                ->join('tujuans', 'tujuans.id', '=', 'invoice_penjualans.tujuan_id')
                ->select('invoice_penjualans.id', 'invoice_penjualans.tanggal', 'invoice_penjualans.no_invoice', 'invoice_penjualans.no_po', 'invoice_penjualans.no_surat_jalan',
                         'invoice_penjualans.no_mobil', 'invoice_penjualans.tgl_jatuh_tempo', 'invoice_penjualans.grand_total', 'konsumens.nama as nama_konsumen',
                         'angkutans.nama as nama_angkutan', 'tujuans.kota as nama_tujuan', 'invoice_penjualans.bank_tujuan_bayar',
                         'invoice_penjualans.tanggal_bayar', 'invoice_penjualans.status_bayar', 'invoice_penjualans.keterangan');
        
        return Datatables::of($list)
                ->addColumn('action', function ($list) {
                    $html  = '<div class="text-center btn-group btn-group-justified">';
                    $html .= '<a href="/invoice/print/' . $list->id . '" title="Print" target="_blank"><button type="button" class="btn btn-sm"><i class="fa fa-print"></i></button></a> '; 
                    $html .= '<a href="/invoice/' . $list->id . '" title="Detail"><button type="button" class="btn btn-sm bg-purple"><i class="fa fa-search"></i></button></a> '; 
                    $html .= '<a href="/invoice/' . $list->id . '/edit" title="Edit"><button type="button" class="btn btn-sm btn-warning"><i class="fa fa-pencil"></i></button></a> '; 
                    $html .= '<a href="/invoice/' . $list->id . '/destroy" title="Delete" onclick="confirmDelete(event, \'' . $list->id . '\', \'' . $list->no_invoice . '\');"><button type="button" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button></a>';
                    $html .= '</div>';
                    
                    return $html;
                })
                ->editColumn('tanggal', function($list) {
                    return with(new Carbon($list->tanggal))->format('d-m-Y');
                })
                ->editColumn('tgl_jatuh_tempo', function($list) {
                    return with(new Carbon($list->tgl_jatuh_tempo))->format('d-m-Y');
                })
                ->editColumn('grand_total', '{{ number_format($grand_total, "2", ".", ",") }}')
                ->editColumn('status_bayar', '{{ $status_bayar == 1 ? "Sudah Bayar" : "Belum Bayar" }}')
                ->editColumn('tanggal_bayar', function($list) {
                    if ($list->tanggal_bayar = '0000-00-00') {
                        return '';   
                    }
                    else {
                        return with(new Carbon($list->tanggal_bayar))->format('d-m-Y');    
                    }
                })
                ->filter(function($query) use ($request) {
                    if ($request->has('no_invoice')) {
                        $query->where('invoice_penjualans.no_invoice', 'like', '%'.$request->get('no_invoice').'%');
                    }
                    if ($request->has('no_po')) {
                        $query->where('invoice_penjualans.no_po', 'like', '%'.$request->get('no_po').'%');
                    }
                    if ($request->has('no_surat_jalan')) {
                        $query->where('invoice_penjualans.no_surat_jalan', 'like', '%'.$request->get('no_surat_jalan').'%');
                    }
                    if ($request->has('tanggal_jatuh_tempo')) {
                        $tanggal = Carbon::createFromFormat('d/m/Y', $request->get('tanggal_jatuh_tempo'))->format('Y-m-d');
                        $query->where('invoice_penjualans.tgl_jatuh_tempo', '=', $tanggal);
                    }
                    if ($request->has('konsumen_id')) {
                        $query->where('invoice_penjualans.konsumen_id', '=', $request->get('konsumen_id'));
                    }
                    if ($request->has('tujuan_id')) {
                        $query->where('invoice_penjualans.tujuan_id', '=', $request->get('tujuan_id'));
                    }
                    if ($request->has('angkutan_id')) {
                        $query->where('invoice_penjualans.angkutan_id', '=', $request->get('angkutan_id'));
                    }
                    if ($request->has('no_mobil')) {
                        $query->where('invoice_penjualans.no_mobil', 'like', '%'.$request->get('no_mobil').'%');
                    }
                })
                ->make(true);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data['default_date'] = date('d/m/Y');
        $data['konsumen'] = Konsumen::select('id', 'nama')->orderBy('nama')->get();
        $data['angkutan'] = Angkutan::select('id', 'nama')->orderBy('nama')->get();
        $data['tujuan'] = Tujuan::select('id', 'kota as nama')->orderBy('nama')->get();
        
        return view('invoice.index', $data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['default_date'] = date('d/m/Y');
        $data['konsumen'] = Konsumen::select('id', 'nama')->orderBy('nama')->get();
        $data['angkutan'] = Angkutan::select('id', 'nama')->orderBy('nama')->get();
        $data['tujuan'] = Tujuan::select('id', 'kota as nama')->orderBy('nama')->get();
        
        return view('invoice.add', $data);
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $count = InvoicePenjualan::where('no_invoice', $request->no_invoice)->count();
        
        // check exist
        if ($count > 0) {
            Flash::error('Error: Invoice dengan nomor ' . $request->no_invoice . ' sudah ada.');
            return redirect('/invoice/create')->withInput();
        }
        else {
            
            DB::beginTransaction();
            try {
                // invoice header
                $invoice = new InvoicePenjualan;
                
                $invoice->tanggal = Carbon::createFromFormat('d/m/Y', $request->tanggal)->format('Y-m-d');
                $invoice->konsumen_id = $request->konsumen_id;
                $invoice->no_invoice = $request->no_invoice;
                $invoice->tgl_jatuh_tempo = Carbon::createFromFormat('d/m/Y', $request->tanggal_jatuh_tempo)->format('Y-m-d');
                $invoice->no_po = $request->no_po;
                $invoice->angkutan_id = $request->angkutan_id;
                $invoice->tujuan_id = $request->tujuan_id;
                
                $angkutan_tujuan = AngkutanTujuan::where('angkutan_id', $request->angkutan_id)->where('tujuan_id', $request->tujuan_id)->first();
                $invoice->harga_angkutan = $angkutan_tujuan ? $angkutan_tujuan->harga : 0;
                
                $invoice->no_surat_jalan = $request->no_surat_jalan;
                $invoice->no_mobil = $request->no_mobil;
                $invoice->sub_total = str_replace(',', '', $request->sub_total);
                $invoice->diskon = str_replace(',', '', $request->discount);
                $invoice->total = str_replace(',', '', $request->total);
                $invoice->ppn = str_replace(',', '', $request->ppn);
                $invoice->grand_total = str_replace(',', '', $request->grand_total);
                $invoice->created_by = Auth::check() ? Auth::user()->username : '';
                $invoice->save();
                
                $a_item = $request->nama_barang;
                $a_ball = $request->ball;
                $a_quantity = $request->pcs;
                $a_price = $request->harga;
                $a_subtotal = $request->jumlah;
                    
                // invoice detail
                for ($i = 0; $i < sizeof($a_item); $i++) {
                    $item_name = $a_item[$i];
                    $barang = Barang::where('nama', $item_name)->first();
                    
                    $item = $barang ? $barang->id : -1;
                    $ball = isset($a_ball[$i]) ? str_replace(',', '', $a_ball[$i]) : 0;
                    $quantity = isset($a_quantity[$i]) ? str_replace(',', '', $a_quantity[$i]) : 0;
                    $price = isset($a_price[$i]) ? str_replace(',', '', $a_price[$i]) : 0;
                    $subtotal = isset($a_subtotal[$i]) ? str_replace(',', '', $a_subtotal[$i]) : 0;
                    
                    if ($item !== -1) {
                        $detail_invoice = new DetailPenjualan;
                       
                        $detail_invoice->invoice_penjualan_id = $invoice->id;
                        $detail_invoice->konsumen_id = $request->konsumen_id;
                        $detail_invoice->barang_id = $item;
                        $detail_invoice->jumlah_ball = $ball;
                        $detail_invoice->jumlah = $quantity;
                        $detail_invoice->harga_barang = $price;
                        $detail_invoice->subtotal = $subtotal;
                        
                        $detail_invoice->created_by = Auth::check() ? Auth::user()->username : '';
                        $detail_invoice->save();    
                    }
                }
                
                DB::commit();
                return redirect('/invoice');
            }
            catch(\Illuminate\Database\QueryException $e) {
                Flash::error('Error (' . $e->errorInfo[1] . '): ' . $e->errorInfo[2] . '.');
                
                DB::rollback();
                return redirect('/invoice/create')->withInput();
            }
        
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data['default_date'] = date('d/m/Y');
        $data['konsumen'] = new Konsumen;
        $data['angkutan'] = new Angkutan;
        $data['tujuan'] = new Tujuan;
        $invoice_penjualan = InvoicePenjualan::find($id);
        $data['invoice_penjualan'] = $invoice_penjualan;
        $data['detail_penjualan'] = $invoice_penjualan->detail;
        $data['barang_helper'] = new Barang;
            
        return view('invoice.detail', $data);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $data['default_date'] = date('d/m/Y');
        $data['konsumen'] = Konsumen::select('id', 'nama')->orderBy('nama')->get();
        $data['angkutan'] = Angkutan::select('id', 'nama')->orderBy('nama')->get();
        $data['tujuan'] = Tujuan::select('id', 'kota as nama')->orderBy('nama')->get();
        $invoice_penjualan = InvoicePenjualan::find($id);
        $data['invoice_penjualan'] = $invoice_penjualan;
        $data['detail_penjualan'] = $invoice_penjualan->detail;
        $data['barang_helper'] = new Barang;
            
        return view('invoice.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $count = InvoicePenjualan::where('no_invoice', $request->no_invoice)->count();
        $invoice = InvoicePenjualan::find($id);
        
        // check exist
        if ($count == 0 || ($count == 1 && $invoice->no_invoice == $request->no_invoice)) {
        
            DB::beginTransaction();
            try {
                // invoice header
                $invoice->tanggal = Carbon::createFromFormat('d/m/Y', $request->tanggal)->format('Y-m-d');
                $invoice->konsumen_id = $request->konsumen_id;
                $invoice->no_invoice = $request->no_invoice;
                $invoice->tgl_jatuh_tempo = Carbon::createFromFormat('d/m/Y', $request->tanggal_jatuh_tempo)->format('Y-m-d');
                $invoice->no_po = $request->no_po;
                $invoice->angkutan_id = $request->angkutan_id;
                $invoice->tujuan_id = $request->tujuan_id;
                
                $angkutan_tujuan = AngkutanTujuan::where('angkutan_id', $request->angkutan_id)->where('tujuan_id', $request->tujuan_id)->first();
                $invoice->harga_angkutan = $angkutan_tujuan ? $angkutan_tujuan->harga : 0;
                
                $invoice->no_surat_jalan = $request->no_surat_jalan;
                $invoice->no_mobil = $request->no_mobil;
                $invoice->sub_total = str_replace(',', '', $request->sub_total);
                $invoice->diskon = str_replace(',', '', $request->discount);
                $invoice->total = str_replace(',', '', $request->total);
                $invoice->ppn = str_replace(',', '', $request->ppn);
                $invoice->grand_total = str_replace(',', '', $request->grand_total);
                $invoice->updated_by = Auth::check() ? Auth::user()->username : '';
                $invoice->save();
                
                $a_item = $request->nama_barang;
                $a_ball = $request->ball;
                $a_quantity = $request->pcs;
                $a_price = $request->harga;
                $a_subtotal = $request->jumlah;
                    
                // invoice detail
                // delete first
                DetailPenjualan::where('invoice_penjualan_id', $id)->delete();
                // then insert
                for ($i = 0; $i < sizeof($a_item); $i++) {
                    $item_name = $a_item[$i];
                    $barang = Barang::where('nama', $item_name)->first();
                    
                    $item = $barang ? $barang->id : -1;
                    $ball = isset($a_ball[$i]) ? str_replace(',', '', $a_ball[$i]) : 0;
                    $quantity = isset($a_quantity[$i]) ? str_replace(',', '', $a_quantity[$i]) : 0;
                    $price = isset($a_price[$i]) ? str_replace(',', '', $a_price[$i]) : 0;
                    $subtotal = isset($a_subtotal[$i]) ? str_replace(',', '', $a_subtotal[$i]) : 0;
                    
                    if ($item !== -1) {
                        $detail_invoice = new DetailPenjualan;
                       
                        $detail_invoice->invoice_penjualan_id = $invoice->id;
                        $detail_invoice->konsumen_id = $request->konsumen_id;
                        $detail_invoice->barang_id = $item;
                        $detail_invoice->jumlah_ball = $ball;
                        $detail_invoice->jumlah = $quantity;
                        $detail_invoice->harga_barang = $price;
                        $detail_invoice->subtotal = $subtotal;
                        
                        $detail_invoice->updated_by = Auth::check() ? Auth::user()->username : '';
                        $detail_invoice->save();    
                    }
                }
                
                DB::commit();
                return redirect('/invoice');
            }
            catch(\Illuminate\Database\QueryException $e) { 
                Flash::error('Error (' . $e->errorInfo[1] . '): ' . $e->errorInfo[2] . '.');
                
                DB::rollback();
                return redirect('/invoice/' . $id . '/edit')->withInput();
            }
        
        }
        else {
            Flash::error('Error: Invoice dengan nomor ' . $request->no_invoice . ' sudah ada.');
            return redirect('/invoice/' . $id . '/edit')->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $invoice_detail = DetailPenjualan::where('invoice_penjualan_id', $id)->delete();
            $invoice = InvoicePenjualan::find($id)->delete();
            
            DB::commit();
            echo 'success';
        }
        catch(\Illuminate\Database\QueryException $e) {
            DB::rollback();
            echo 'Error (' . $e->errorInfo[1] . '): ' . $e->errorInfo[2] . '.';
        }
    }
    
    // do print
    public function doPrint($id) {
        $invoice = InvoicePenjualan::find($id);
        $invoice_detail = $invoice->detail;
        
        // set document information
        PDF::SetAuthor('PT. TRIMITRA KEMASINDO');
        PDF::SetTitle('Invoice Penjualan - Trimitra Kemasindo');
        PDF::SetSubject('Invoice Penjualan');
        PDF::SetKeywords('Invoice Penjualan Trimitra Kemasindo');
        
        // AddPage ($orientation='', $format='', $keepmargins=false, $tocpage=false)
        PDF::AddPage('P', 'A4');
        
        // SetMargins ($left, $top, $right=-1, $keepmargins=false)
        PDF::SetMargins(15, 10);
        
        // Image ($file, $x='', $y='', $w=0, $h=0, $type='', $link='', $align='', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border=0, $fitbox=false, $hidden=false, $fitonpage=false, $alt=false, $altimgs=array())
        //PDF::Image(asset('/image/logo-ponpes-darussalam.png'), 15, 5, 15, 15, '', '', 'T', true);
        //PDF::Image(asset('/image/bmt.png'), 15, 5, 35, 15, '', '', 'T', true);
        
        PDF::setX(15);
        
        // SetFont ($family, $style='', $size=null, $fontfile='', $subset='default', $out=true)
        PDF::SetFont('times', 'B', 11);
        // Cell ($w, $h=0, $txt='', $border=0, $ln=0, $align='', $fill=false, $link='', $stretch=0, $ignore_min_height=false, $calign='T', $valign='M')
        PDF::Cell(0, 0, 'PT. Trimitra Kemasindo', 0, 0, 'L', 0, '', 0);
        PDF::Ln();
        PDF::SetFont('', '', 9);
        PDF::Cell(0, 0, 'Jalan Raya Sapan KM 1 No. 15', 0, 0, 'L', 0, '', 0);
        PDF::Ln();
        PDF::Cell(0, 0, 'Tlp. (022) 87304121', 0, 0, 'L', 0, '', 0);
        PDF::Ln();
        PDF::Cell(0, 0, 'Fax. (022) 87304123', 0, 0, 'L', 0, '', 0);
        PDF::Ln();
        PDF::Cell(0, 0, 'Bandung', 0, 0, 'L', 0, '', 0);
        PDF::Ln();
        
        PDF::SetFont('times', 'B', 16);
        PDF::setXY(151, 10);
        PDF::Cell(0, 0, 'I  N  V  O  I  C  E', 0, 0, 'L', 0, '', 0);
        PDF::Ln();
        
        PDF::setX(130);
        PDF::SetFont('', '', 10);
        PDF::Cell(30, 0, 'No. :', 0, 0, 'R', 0, '', 0);
        PDF::Cell(0, 0, ' ' . $invoice->no_invoice, 0, 0, 'L', 0, '', 0);
        PDF::Ln();
        PDF::setX(130);
        PDF::Cell(30, 0, 'Tanggal :', 0, 0, 'R', 0, '', 0);
        PDF::Cell(0, 0, ' ' . $invoice->tanggal->format('d-m-Y'), 0, 0, 'L', 0, '', 0);
        PDF::Ln();
        PDF::setX(130);
        PDF::Cell(30, 0, 'Konsumen :', 0, 0, 'R', 0, '', 0);
        PDF::Cell(0, 0, ' ' . Konsumen::find($invoice->konsumen_id)->nama, 0, 0, 'L', 0, '', 0);
        PDF::Ln();
        PDF::setX(130);
        PDF::Cell(30, 0, 'No. PO :', 0, 0, 'R', 0, '', 0);
        PDF::Cell(0, 0, ' ' . $invoice->no_po, 0, 0, 'L', 0, '', 0);
        PDF::Ln();
        PDF::setX(130);
        PDF::Cell(30, 0, 'Tgl. Jatuh Tempo :', 0, 0, 'R', 0, '', 0);
        PDF::Cell(0, 0, ' ' . $invoice->tgl_jatuh_tempo->format('d-m-Y'), 0, 0, 'L', 0, '', 0);
        PDF::Ln();
        
        PDF::Ln(10); 
        
        PDF::Cell(8, 10, 'No', 1, 0, 'C', 0, '', 0);
        PDF::Cell(25, 10, 'Jenis', 1, 0, 'C', 0, '', 0);
        PDF::Cell(60, 10, 'Barang', 1, 0, 'C', 0, '', 0);
        PDF::Cell(30, 5, 'Qty', 1, 0, 'C', 0, '', 0);
        PDF::Cell(25, 10, 'Harga / Pcs', 1, 0, 'C', 0, '', 0);
        PDF::Cell(30, 10, 'Jumlah', 1, 0, 'C', 0, '', 0);
        PDF::Ln();
        
        $curY = PDF::getY();
        PDF::setXY(108, $curY-5);
        PDF::Cell(15, 5, 'Ball', 1, 0, 'C', 0, '', 0);
        PDF::Cell(15, 5, 'Pcs', 1, 0, 'C', 0, '', 0);
        PDF::Ln();
        
        
        $no = 1;
        foreach($invoice_detail as $item) {
            $barang = Barang::find($item->barang_id);
            
            PDF::Cell(8, 6, $no, 1, 0, 'R', 0, '', 1);
            PDF::Cell(25, 6, $barang->jenis, 1, 0, 'L', 0, '', 1);
            PDF::Cell(60, 6, $barang->nama, 1, 0, 'L', 0, '', 1);
            PDF::Cell(15, 6, number_format($item->jumlah_ball, 0, '.', ','), 1, 0, 'R', 0, '', 1);
            PDF::Cell(15, 6, number_format($item->jumlah, 0, '.', ','), 1, 0, 'R', 0, '', 1);
            PDF::Cell(25, 6, number_format($item->harga_barang, 2, '.', ','), 1, 0, 'R', 0, '', 1);
            PDF::Cell(30, 6, number_format($item->subtotal, 2, '.', ','), 1, 0, 'R', 0, '', 1);
            PDF::Ln();
            
            $no++;
        }
        
        PDF::Cell(8, 6, '', 1, 0, 'R', 0, '', 1);
        PDF::Cell(25, 6, '', 1, 0, 'L', 0, '', 1);
        PDF::Cell(60, 6, '', 1, 0, 'L', 0, '', 1);
        PDF::Cell(15, 6, '', 1, 0, 'R', 0, '', 1);
        PDF::Cell(15, 6, '', 1, 0, 'R', 0, '', 1);
        PDF::Cell(25, 6, '', 1, 0, 'R', 0, '', 1);
        PDF::Cell(30, 6, '', 1, 0, 'R', 0, '', 1);
        PDF::Ln();
        
        PDF::setX(138);
        PDF::Cell(25, 6, 'Sub Total', 1, 0, 'R', 0, '', 0);
        PDF::Cell(30, 6, number_format($invoice->sub_total, 0, '.', ','), 1, 0, 'R', 0, '', 0);
        PDF::Ln();
        
        // get this y position
        $curY = PDF::getY();
        
        PDF::setX(138);
        PDF::Cell(25, 6, 'Discount', 1, 0, 'R', 0, '', 0);
        PDF::Cell(30, 6, number_format($invoice->diskon, 0, '.', ','), 1, 0, 'R', 0, '', 0);
        PDF::Ln();
        
        PDF::setX(138);
        PDF::Cell(25, 6, 'Total', 1, 0, 'R', 0, '', 0);
        PDF::Cell(30, 6, number_format($invoice->total, 0, '.', ','), 1, 0, 'R', 0, '', 0);
        PDF::Ln();
        
        PDF::setX(138);
        PDF::Cell(25, 6, 'PPN', 1, 0, 'R', 0, '', 0);
        PDF::Cell(30, 6, number_format($invoice->ppn, 0, '.', ','), 1, 0, 'R', 0, '', 0);
        PDF::Ln();
        
        PDF::SetFont('', 'b', 10);
        PDF::setX(138);
        PDF::Cell(25, 6, 'Grand Total', 1, 0, 'R', 0, '', 0);
        PDF::Cell(30, 6, number_format($invoice->grand_total, 0, '.', ','), 1, 0, 'R', 0, '', 0);
        PDF::Ln();
        
        PDF::SetFont('', '', 10);
        
        //MultiCell ($w, $h, $txt, $border=0, $align=‘J’, $fill=false, $ln=1, $x=“, $y=”, $reseth=true, $stretch=0, $ishtml=false, $autopadding=true, $maxh=0, $valign=’T’, $fitcell=false)
        
        PDF::setY($curY);
        PDF::MultiCell(120, 0, 'Pembayaran untuk invoice ini mohon ditransfer ke rekening di bawah ini:', 0, 'L', false, 0);
        PDF::Ln(7);
        PDF::Cell(90, 0, 'BRI', 0, 0, 'L', 0, '', 0);
        PDF::Ln();
        PDF::Cell(90, 0, 'No. Rek: 0286.01.000829.30.7', 0, 0, 'L', 0, '', 0);
        PDF::Ln();
        PDF::Cell(90, 0, 'a/n: PT. Trimitra Kemasindo', 0, 0, 'L', 0, '', 0);
        PDF::Ln(7);
        PDF::Cell(90, 0, 'BCA', 0, 0, 'L', 0, '', 0);
        PDF::Ln();
        PDF::Cell(90, 0, 'No. Rek: 775.036.0850', 0, 0, 'L', 0, '', 0);
        PDF::Ln();
        PDF::Cell(90, 0, 'a/n: Ety Juniati Buntaran', 0, 0, 'L', 0, '', 0);
        PDF::Ln();
        
        
        // Output ($name='doc.pdf', $dest='I'), I=inline, D=Download
        PDF::Output('invoice_penjualan.pdf');
        
        // need to call exit, i don't know why
        exit;
    }
}
