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
use App\Konsumen;
use App\Angkutan;
use App\Tujuan;
use DB;

class PembayaranAngkutanController extends Controller
{
    
    public function datatables(Request $request) {
        $list = DB::table('invoice_penjualans')
                ->join('konsumens', 'konsumens.id', '=', 'invoice_penjualans.konsumen_id')
                ->join('angkutans', 'angkutans.id', '=', 'invoice_penjualans.angkutan_id')
                ->join('tujuans', 'tujuans.id', '=', 'invoice_penjualans.tujuan_id')
                ->select('invoice_penjualans.id', 'invoice_penjualans.tanggal', 'invoice_penjualans.no_surat_jalan', 'invoice_penjualans.harga_angkutan', 
                         'invoice_penjualans.no_mobil', 'konsumens.nama as nama_konsumen', 'angkutans.nama as nama_angkutan', 'tujuans.kota as nama_tujuan',
                         'invoice_penjualans.tanggal_bayar_angkutan', 'invoice_penjualans.diskon_bayar_angkutan', 'invoice_penjualans.jumlah_bayar_angkutan',
                         'invoice_penjualans.status_bayar_angkutan', 'invoice_penjualans.bank_tujuan_bayar_angkutan', 'invoice_penjualans.keterangan_bayar_angkutan');
        
        return Datatables::of($list)
                ->addColumn('action', function ($list) {
                    $html  = '<div class="text-center btn-group btn-group-justified">';
                    if (in_array(341, session()->get('allowed_menus'))) {
                        if ($list->status_bayar_angkutan == 0) {
                            $html .= '<a href="/pembayaran-angkutan/complete/' . $list->id . '" title="Konfirmasi Pembayaran" onclick="confirmComplete(event, \'' . $list->id . '\', \'' . $list->no_surat_jalan . '\', \'' . $list->harga_angkutan . '\');"><button type="button" class="btn btn-sm btn-warning"><i class="fa fa-flag"></i></button></a>';    
                        }
                    }
                    $html .= '</div>';
                    
                    return $html;
                })
                ->editColumn('tanggal', function($list) {
                    return with(new Carbon($list->tanggal))->format('d-m-Y');
                })
                ->editColumn('harga_angkutan', '{{ number_format($harga_angkutan, "2", ".", ",") }}')
                ->editColumn('diskon_bayar_angkutan', '{{ number_format($diskon_bayar_angkutan, "2", ".", ",") }}')
                ->editColumn('jumlah_bayar_angkutan', '{{ number_format($jumlah_bayar_angkutan, "2", ".", ",") }}')
                ->editColumn('status_bayar_angkutan', '{{ $status_bayar_angkutan == 1 ? "Sudah Bayar" : "Belum Bayar" }}')
                ->editColumn('tanggal_bayar_angkutan', function($list) {
                    if ($list->tanggal_bayar_angkutan == '0000-00-00' || $list->tanggal_bayar_angkutan == null) {
                        return '';   
                    }
                    else {
                        return with(new Carbon($list->tanggal_bayar_angkutan))->format('d-m-Y');    
                    }
                })
                ->filter(function($query) use ($request) {
                    if ($request->has('no_surat_jalan')) {
                        $query->where('invoice_penjualans.no_surat_jalan', 'like', '%'.$request->get('no_surat_jalan').'%');
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
        if (in_array(340, session()->get('allowed_menus'))) {
            $data['default_date'] = date('d/m/Y');
            $data['konsumen'] = Konsumen::select('id', 'nama')->orderBy('nama')->get();
            $data['angkutan'] = Angkutan::select('id', 'nama')->orderBy('nama')->get();
            $data['tujuan'] = Tujuan::select('id', 'kota as nama')->orderBy('nama')->get();
            
            return view('pembayaran_angkutan.index', $data);
        }
        else {
            //
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
    
    public function complete(Request $request, $id) {
        try {
            $invoice = InvoicePenjualan::find($id);
            $invoice->status_bayar_angkutan = 1;
            $invoice->tanggal_bayar_angkutan = Carbon::createFromFormat('d/m/Y', $request->tanggal_bayar)->format('Y-m-d');
            $invoice->diskon_bayar_angkutan = str_replace(',', '', $request->discount); 
            $invoice->jumlah_bayar_angkutan = str_replace(',', '', $request->jumlah_bayar); 
            //$invoice->bank_tujuan_bayar_angkutan = $request->bank_tujuan_bayar;
            $invoice->keterangan_bayar_angkutan = $request->keterangan;
            $invoice->save();
            
            echo 'success';
        }
        catch(\Illuminate\Database\QueryException $e) {
            echo 'Error (' . $e->errorInfo[1] . '): ' . $e->errorInfo[2] . '.';
        }
    }
}
