<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Datatables;
use Flash;
use Auth;
use App\Barang;

class BarangController extends Controller
{
    
    public function datatables() {
        $list = Barang::select('id', 'nama', 'jenis', 'pcs', 'berat');
        
        return Datatables::of($list)
                ->addColumn('action', function ($list) {
                    $html  = '<div class="text-center btn-group btn-group-justified">';
                    $html .= '<a href="/barang/' . $list->id . '/edit" title="Edit"><button type="button" class="btn btn-sm btn-warning"><i class="fa fa-pencil"></i></button></a> '; 
                    $html .= '<a href="/barang/' . $list->id . '/destroy" title="Delete" onclick="confirmDelete(event, \'' . $list->id . '\', \'' . $list->nama . '\');"><button type="button" class="btn btn-sm btn-danger"><i class="fa fa-trash"></i></button></a>';
                    $html .= '</div>';
                    
                    return $html;
                })
                ->editColumn('pcs', '{{ number_format($pcs, "2", ".", ",") }}')
                ->editColumn('berat', '{{ number_format($berat, "2", ".", ",") }}')
                ->make(true);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('barang.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('barang.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $count = Barang::where('nama', $request->nama)->count();
        
        // check exist
        if ($count > 0) {
            Flash::error('Error: Barang dengan nama ' . $request->nama . ' sudah ada.');
            return redirect('/barang/create')->withInput();
        }
        else {
            try {
                $barang = new Barang;
                $barang->nama = $request->nama;
                $barang->jenis = $request->jenis;
                $barang->pcs = str_replace(',', '', $request->pcs);
                $barang->berat = str_replace(',', '', $request->berat);
                $barang->created_by = Auth::check() ? Auth::user()->username : '';
                $barang->save();
                
                return redirect('/barang');
            }
            catch(\Illuminate\Database\QueryException $e) { 
                Flash::error('Error (' . $e->errorInfo[1] . '): ' . $e->errorInfo[2] . '.');
                return redirect('/barang/create')->withInput();
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
        $data['barang'] = Barang::find($id);
        
        return view('barang.edit', $data);
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
        $count = Barang::where('nama', $request->nama)->count();
        $barang = Barang::find($id);
        
        // check exist
        if ($count == 0 || ($count == 1 && $barang->nama == $request->nama)) {
            try {
                $barang->nama = $request->nama;
                $barang->jenis = $request->jenis;
                $barang->pcs = str_replace(',', '', $request->pcs);
                $barang->berat = str_replace(',', '', $request->berat);
                $barang->updated_by = Auth::check() ? Auth::user()->username : '';
                $barang->save();
                
                return redirect('/barang');
            }
            catch(\Illuminate\Database\QueryException $e) { 
                Flash::error('Error (' . $e->errorInfo[1] . '): ' . $e->errorInfo[2] . '.');
                return redirect('/barang/' . $id . '/edit')->withInput();
            }
        }
        else {
            Flash::error('Error: Barang dengan nama ' . $request->nama . ' sudah ada.');
            return redirect('/barang/' . $id . '/edit')->withInput();
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
        $barang = Barang::find($id);
        
        try {
            $barang->delete();
            echo 'success';
        }
        catch(\Illuminate\Database\QueryException $e) { 
            echo 'Error (' . $e->errorInfo[1] . '): ' . $e->errorInfo[2] . '.';
        }
    }
}
