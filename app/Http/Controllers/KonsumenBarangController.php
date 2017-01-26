<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Datatables;
use Flash;
use Auth;
use App\Konsumen;
use App\Barang;
use App\KonsumenBarang;
use DB;

class KonsumenBarangController extends Controller
{
    
    public function datatables() {
        $list = DB::table('konsumen_barangs')
                ->join('konsumens', 'konsumen_barangs.konsumen_id', '=', 'konsumens.id')
                ->join('barangs', 'konsumen_barangs.barang_id', '=', 'barangs.id')
                ->select('konsumen_barangs.id', 'konsumen_barangs.konsumen_id', 'konsumen_barangs.barang_id', 'konsumen_barangs.harga', 'barangs.nama as nama_barang', 'barangs.jenis as jenis_barang', 'konsumens.nama as nama_konsumen');
        
        return Datatables::of($list)
                ->addColumn('action', function ($list) {
                    $html  = '<div class="text-center btn-group btn-group-justified">';
                    $html .= '<a href="/konsumen-barang/' . $list->id . '/edit" title="Edit"><button type="button" class="btn btn-sm btn-warning"><i class="fa fa-pencil"></i></button></a> '; 
                    $html .= '<a href="/konsumen-barang/' . $list->id . '/destroy" title="Delete" onclick="confirmDelete(event, \'' . $list->id . '\', \'' . $list->nama_konsumen . '\', \'' . $list->nama_barang . '\');"><button type="button" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button></a>';
                    $html .= '</div>';
                    
                    return $html;
                })
                ->editColumn('harga', '{{ number_format($harga, "2", ".", ",") }}')
                ->make(true);
    }
    
    public function getBarangsByKonsumen($konsumen_id) {
        $barangs = DB::table('konsumen_barangs')
                    ->join('barangs', 'konsumen_barangs.barang_id', '=', 'barangs.id')
                    ->select('barangs.id', 'barangs.nama', 'barangs.jenis')
                    ->where('konsumen_barangs.konsumen_id', '=', $konsumen_id)
                    ->orderBy('barangs.nama')->get();
        
        $opts = '';
        foreach ($barangs as $barang) {
            $opts .= '<option value="' . $barang->id . '">' . $barang->nama . ' - ' . $barang->jenis . ' (' . $barang->id . ')</option>';
        }
        
        echo $opts;
    }
    
    public function getPrice($item_name, $konsumen_id) {
        $barang = Barang::where('nama', $item_name)->first();
        $barang_id = -1;
        $pcs_in_ball = 0;
        if ($barang) {
            $barang_id = $barang->id;
            $pcs_in_ball = $barang->pcs;
        }
        
        $harga = 0;
        $konsumen_barang = KonsumenBarang::where('barang_id', $barang_id)->where('konsumen_id', $konsumen_id)->first();
        if ($konsumen_barang) {
            $harga = $konsumen_barang->harga;
        }
        else {
            $harga = 0.00;
        }
        
        $output['harga'] = $harga;
        $output['pcs_in_ball'] = $pcs_in_ball;
        
        //return $harga;
        return response()->json($output);
    }
    
    public function getPriceById($item_id, $konsumen_id) {
        $barang = Barang::find($item_id);
        $barang_id = -1;
        $pcs_in_ball = 0;
        if ($barang) {
            $barang_id = $barang->id;
            $pcs_in_ball = $barang->pcs;
        }
        
        $harga = 0;
        $konsumen_barang = KonsumenBarang::where('barang_id', $barang_id)->where('konsumen_id', $konsumen_id)->first();
        if ($konsumen_barang) {
            $harga = $konsumen_barang->harga;
        }
        else {
            $harga = 0.00;
        }
        
        $output['harga'] = $harga;
        $output['pcs_in_ball'] = $pcs_in_ball;
        
        //return $harga;
        return response()->json($output);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('konsumen_barang.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['konsumen'] = Konsumen::select('id', 'nama')->orderBy('nama')->get();
        $data['barang'] = Barang::select('id', 'nama', 'jenis')->orderBy('nama')->get();
        
        return view('konsumen_barang.add', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $count = KonsumenBarang::where('konsumen_id', $request->konsumen_id)->where('barang_id', $request->barang_id)->count();
        
        // check exist
        if ($count > 0) {
            $konsumen = Konsumen::find($request->konsumen_id);
            $barang = Barang::find($request->barang_id);
            $nama_konsumen = $konsumen ? $konsumen->nama : '';
            $nama_barang = $barang ? $barang->nama : '';
            $jenis_barang = $barang ? $barang->jenis : '';
            Flash::error('Error: Data harga dengan konsumen ' . $nama_konsumen . ' dan barang ' . $nama_barang . ' (' . $jenis_barang . ') sudah ada.');
            return redirect('/konsumen-barang/create')->withInput();
        }
        else {
            try {
                $konsumen_barang = new KonsumenBarang;
                $konsumen_barang->konsumen_id = $request->konsumen_id;
                $konsumen_barang->barang_id = $request->barang_id;
                $konsumen_barang->harga = str_replace(',', '', $request->harga);
                $konsumen_barang->created_by = Auth::check() ? Auth::user()->username : '';
                $konsumen_barang->save();
                
                return redirect('/konsumen-barang');
            }
            catch(\Illuminate\Database\QueryException $e) { 
                Flash::error('Error (' . $e->errorInfo[1] . '): ' . $e->errorInfo[2] . '.');
                return redirect('/konsumen-barang/create')->withInput();
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
        $data['konsumen'] = Konsumen::select('id', 'nama')->orderBy('nama')->get();
        $data['barang'] = Barang::select('id', 'nama', 'jenis')->orderBy('nama')->get();
        $data['konsumen_barang'] = KonsumenBarang::find($id);
        
        return view('konsumen_barang.edit', $data);
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
        $count = KonsumenBarang::where('konsumen_id', $request->konsumen_id)->where('barang_id', $request->barang_id)->count();
        $konsumen_barang = KonsumenBarang::find($id);
        
        // check exist
        if ($count == 0 || ($count == 1 && $konsumen_barang->konsumen_id == $request->konsumen_id && $konsumen_barang->barang_id == $request->barang_id)) {
            try {
                $konsumen_barang->konsumen_id = $request->konsumen_id;
                $konsumen_barang->barang_id = $request->barang_id;
                $konsumen_barang->harga = str_replace(',', '', $request->harga);
                $konsumen_barang->updated_by = Auth::check() ? Auth::user()->username : '';
                $konsumen_barang->save();
                
                return redirect('/konsumen-barang');
            }
            catch(\Illuminate\Database\QueryException $e) { 
                Flash::error('Error (' . $e->errorInfo[1] . '): ' . $e->errorInfo[2] . '.');
                return redirect('/konsumen-barang/' . $id . '/edit')->withInput();
            }
        }
        else {
            $konsumen = Konsumen::find($request->konsumen_id);
            $barang = Barang::find($request->barang_id);
            $nama_konsumen = $konsumen ? $konsumen->nama : '';
            $nama_barang = $barang ? $barang->nama : '';
            $jenis_barang = $barang ? $barang->jenis : '';
            Flash::error('Error: Data harga dengan konsumen ' . $nama_konsumen . ' dan barang ' . $nama_barang. ' (' . $jenis_barang . ') sudah ada.');
            return redirect('/konsumen-barang/' . $id . '/edit')->withInput();
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
        $konsumen_barang = KonsumenBarang::find($id);
        
        try {
            $konsumen_barang->delete();
            echo 'success';
        }
        catch(\Illuminate\Database\QueryException $e) { 
            echo 'Error (' . $e->errorInfo[1] . '): ' . $e->errorInfo[2] . '.';
        }
    }
}
