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

class KonsumenBarangController extends Controller
{
    
    public function datatables() {
        $list = KonsumenBarang::select('id', 'konsumen_id', 'barang_id', 'harga');
        
        return Datatables::of($list)
                ->addColumn('action', function ($list) {
                    $konsumen = Konsumen::find($list->konsumen_id);
                    $barang = Barang::find($list->barang_id);
                    
                    $nama_konsumen = $konsumen ? $konsumen->nama : '';
                    $nama_barang = $barang ? $barang->nama : '';
                    
                    $html  = '<div class="text-center btn-group btn-group-justified">';
                    $html .= '<a href="/konsumen-barang/' . $list->id . '/edit" title="Edit"><button type="button" class="btn btn-sm btn-warning"><i class="fa fa-pencil"></i></button></a> '; 
                    $html .= '<a href="/konsumen-barang/' . $list->id . '/destroy" title="Delete" onclick="confirmDelete(event, \'' . $list->id . '\', \'' . $nama_konsumen . '\', \'' . $nama_barang . '\');"><button type="button" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button></a>';
                    $html .= '</div>';
                    
                    return $html;
                })
                ->editColumn('konsumen_id', function($list) {
                    $konsumen = Konsumen::find($list->konsumen_id);
                    return $konsumen ? $konsumen->nama : '';
                })
                ->editColumn('barang_id', function($list) {
                    $barang = Barang::find($list->barang_id);
                    return $barang ? $barang->nama : '';
                })
                ->editColumn('harga', '{{ number_format($harga, "2", ".", ",") }}')
                ->make(true);
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
        $data['barang'] = Barang::select('id', 'nama')->orderBy('nama')->get();
        
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
            Flash::error('Error: Data harga dengan konsumen ' . $nama_konsumen . ' dan barang ' . $nama_barang . ' sudah ada.');
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
        $data['barang'] = Barang::select('id', 'nama')->orderBy('nama')->get();
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
            Flash::error('Error: Data harga dengan konsumen ' . $nama_konsumen . ' dan barang ' . $nama_barang . ' sudah ada.');
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
